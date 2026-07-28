<?php

namespace App\Models;

use Laravel\Jetstream\Membership as JetstreamMembership;

class Membership extends JetstreamMembership
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $fillable = [
        'role',
        'permissions',
        'branch_id',
        'department_id',
        'job_title',
        'phone',
        'bio',
        'is_public',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_public' => 'boolean',
    ];
}
