<?php

namespace App\Services;

use App\Models\AgencyTask;
use App\Models\AutomationRule;
use App\Models\AutomationRun;
use App\Models\CalendarEntry;
use App\Models\PortalIntegration;
use App\Models\PortalListing;
use App\Models\Property;
use App\Models\User;
use RuntimeException;
use Throwable;

class AutomationEngine
{
    public function __construct(private readonly NotificationDispatcher $notifications) {}

    public function run(AutomationRule $rule, array $context, User $actor): AutomationRun
    {
        $run = AutomationRun::create([
            'team_id' => $rule->team_id,
            'automation_rule_id' => $rule->id,
            'status' => 'running',
            'context' => $context,
            'started_at' => now(),
        ]);

        try {
            if (! $rule->active) {
                throw new RuntimeException('Inactive automation rules cannot be run.');
            }
            if (! $this->conditionsMatch($rule->conditions ?? [], $context)) {
                $run->update(['status' => 'skipped', 'results' => [], 'completed_at' => now()]);

                return $run->fresh();
            }

            $results = [];
            foreach ($rule->actions as $action) {
                $results[] = $this->executeAction($rule, $action, $context, $actor);
            }
            $run->update(['status' => 'completed', 'results' => $results, 'completed_at' => now()]);
            $rule->update(['last_run_at' => now()]);
        } catch (Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'error' => $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }

        return $run->fresh();
    }

    private function conditionsMatch(array $conditions, array $context): bool
    {
        foreach ($conditions as $condition) {
            $actual = data_get($context, $condition['field']);
            $expected = $condition['value'] ?? null;
            $matches = match ($condition['operator'] ?? 'equals') {
                'equals' => $actual == $expected,
                'not_equals' => $actual != $expected,
                'contains' => str_contains((string) $actual, (string) $expected),
                'greater_than' => $actual > $expected,
                'less_than' => $actual < $expected,
                default => false,
            };
            if (! $matches) {
                return false;
            }
        }

        return true;
    }

    private function executeAction(AutomationRule $rule, array $action, array $context, User $actor): array
    {
        return match ($action['type']) {
            'create_task' => $this->createTask($rule, $action, $actor),
            'notify_user' => $this->notifyUser($rule, $action, $context),
            'update_property_status' => $this->updateProperty($rule, $action, $context),
            'assign_staff' => $this->assignStaff($rule, $action, $context),
            'publish_listing' => $this->publishListing($rule, $action, $context),
            'export_portal' => $this->exportPortal($rule, $action, $context),
            'schedule_reminder' => $this->scheduleReminder($rule, $action, $context, $actor),
            default => throw new RuntimeException("Unsupported automation action [{$action['type']}]."),
        };
    }

    private function createTask(AutomationRule $rule, array $action, User $actor): array
    {
        $assignedTo = $action['assigned_to'] ?? $actor->id;
        if (! $actor->allTeams()->firstWhere('id', $rule->team_id)?->allUsers()->contains('id', $assignedTo)) {
            throw new RuntimeException('Task assignee does not belong to this organisation.');
        }
        $task = AgencyTask::create([
            'team_id' => $rule->team_id,
            'created_by' => $actor->id,
            'assigned_to' => $assignedTo,
            'title' => $action['title'],
            'description' => $action['description'] ?? null,
            'priority' => $action['priority'] ?? 'normal',
            'due_at' => isset($action['due_in_days']) ? now()->addDays((int) $action['due_in_days']) : null,
        ]);

        return ['type' => 'create_task', 'id' => $task->id];
    }

    private function notifyUser(AutomationRule $rule, array $action, array $context): array
    {
        $user = User::findOrFail($action['user_id']);
        if (! $user->allTeams()->contains('id', $rule->team_id)) {
            throw new RuntimeException('Notification recipient does not belong to this organisation.');
        }
        $deliveries = $this->notifications->dispatch(
            $rule->team_id,
            $user,
            $rule->trigger,
            $action['title'],
            $action['body'] ?? null,
            $context,
            $action['channels'] ?? ['in_app'],
        );

        return ['type' => 'notify_user', 'user_id' => $user->id, 'deliveries' => $deliveries];
    }

    private function updateProperty(AutomationRule $rule, array $action, array $context): array
    {
        $propertyId = $action['property_id'] ?? data_get($context, 'property_id');
        $property = Property::where('team_id', $rule->team_id)->findOrFail($propertyId);
        $property->update(['status' => $action['status']]);

        return ['type' => 'update_property_status', 'property_id' => $property->id, 'status' => $property->status];
    }

    private function assignStaff(AutomationRule $rule, array $action, array $context): array
    {
        $property = $this->property($rule, $action, $context);
        $user = User::findOrFail($action['assigned_to']);
        if (! $user->allTeams()->contains('id', $rule->team_id)) {
            throw new RuntimeException('Assigned staff member does not belong to this organisation.');
        }
        $property->update(['agent_id' => $user->id]);

        return [
            'type' => 'assign_staff',
            'property_id' => $property->id,
            'assigned_to' => $user->id,
        ];
    }

    private function publishListing(AutomationRule $rule, array $action, array $context): array
    {
        $property = $this->property($rule, $action, $context);
        $status = $action['property_status'] ?? 'available';
        $property->update(['status' => $status]);

        return [
            'type' => 'publish_listing',
            'property_id' => $property->id,
            'status' => $property->status,
        ];
    }

    private function exportPortal(AutomationRule $rule, array $action, array $context): array
    {
        $property = $this->property($rule, $action, $context);
        $integrationId = $action['portal_integration_id'] ?? data_get($context, 'portal_integration_id');
        $integration = PortalIntegration::where('team_id', $rule->team_id)
            ->where('active', true)
            ->findOrFail($integrationId);
        $listing = PortalListing::updateOrCreate([
            'portal_integration_id' => $integration->id,
            'property_id' => $property->id,
        ], [
            'team_id' => $rule->team_id,
            'status' => 'pending',
            'last_error' => null,
        ]);

        return [
            'type' => 'export_portal',
            'portal_integration_id' => $integration->id,
            'property_id' => $property->id,
            'portal_listing_id' => $listing->id,
            'status' => $listing->status,
        ];
    }

    private function scheduleReminder(
        AutomationRule $rule,
        array $action,
        array $context,
        User $actor,
    ): array {
        $assignedTo = $action['assigned_to'] ?? $actor->id;
        $assignee = User::findOrFail($assignedTo);
        if (! $assignee->allTeams()->contains('id', $rule->team_id)) {
            throw new RuntimeException('Reminder assignee does not belong to this organisation.');
        }
        $startsAt = now()
            ->addDays((int) ($action['due_in_days'] ?? 0))
            ->addHours((int) ($action['due_in_hours'] ?? 0));
        $propertyId = $action['property_id'] ?? data_get($context, 'property_id');
        if ($propertyId) {
            Property::where('team_id', $rule->team_id)->findOrFail($propertyId);
        }
        $reminder = CalendarEntry::create([
            'team_id' => $rule->team_id,
            'property_id' => $propertyId,
            'organiser_id' => $assignedTo,
            'created_by' => $actor->id,
            'type' => 'reminder',
            'title' => $action['title'],
            'description' => $action['description'] ?? null,
            'starts_at' => $startsAt,
            'reminder_at' => $startsAt,
            'all_day' => false,
            'attendee_user_ids' => [$assignedTo],
        ]);

        return [
            'type' => 'schedule_reminder',
            'calendar_entry_id' => $reminder->id,
            'assigned_to' => $assignedTo,
            'starts_at' => $startsAt->toIso8601String(),
        ];
    }

    private function property(AutomationRule $rule, array $action, array $context): Property
    {
        $propertyId = $action['property_id'] ?? data_get($context, 'property_id');

        return Property::where('team_id', $rule->team_id)->findOrFail($propertyId);
    }
}
