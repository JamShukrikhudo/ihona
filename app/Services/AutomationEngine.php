<?php

namespace App\Services;

use App\Models\AgencyTask;
use App\Models\AutomationRule;
use App\Models\AutomationRun;
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
}
