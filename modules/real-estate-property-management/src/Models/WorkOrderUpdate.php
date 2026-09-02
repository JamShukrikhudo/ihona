<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class WorkOrderUpdate extends Model
{
    protected $table = 'real_estate_work_order_updates';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['update_date' => 'datetime', 'progress_percentage' => 'integer', 'time_spent' => 'decimal:2', 'materials_used' => 'array', 'issues_encountered' => 'array', 'is_customer_visible' => 'boolean'];
    }
}
