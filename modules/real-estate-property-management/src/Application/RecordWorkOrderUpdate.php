<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Domain\WorkOrderStatus;
use Liberu\RealEstate\PropertyManagement\Models\WorkOrder;
use Liberu\RealEstate\PropertyManagement\Models\WorkOrderUpdate;

final class RecordWorkOrderUpdate
{
    public function handle(WorkOrder $order, int|string $teamId, int|string $actorId, array $attributes): WorkOrderUpdate
    {
        abort_unless((string) $order->team_id === (string) $teamId, 404);
        if (! filled($attributes['description'] ?? null)) {
            throw ValidationException::withMessages(['description' => 'An update description is required.']);
        }
        $status = $attributes['status_change'] ?? null;
        if ($status !== null && WorkOrderStatus::tryFrom($status) === null) {
            throw ValidationException::withMessages(['status_change' => 'Select a valid work-order status.']);
        }

        return DB::transaction(function () use ($order, $teamId, $actorId, $attributes, $status): WorkOrderUpdate {
            $update = WorkOrderUpdate::query()->create([...$attributes, 'team_id' => $teamId, 'work_order_id' => $order->getKey(), 'updated_by' => $actorId, 'update_date' => $attributes['update_date'] ?? now()]);
            if ($status !== null) {
                $order->forceFill(['status' => $status, 'started_date' => $status === WorkOrderStatus::InProgress->value ? ($order->started_date ?? now()) : $order->started_date, 'completed_date' => $status === WorkOrderStatus::Completed->value ? ($order->completed_date ?? now()) : $order->completed_date])->save();
            }

            return $update;
        });
    }
}
