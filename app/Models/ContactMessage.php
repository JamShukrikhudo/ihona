<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'interest',
        'property_id',
        'message',
        'is_read',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
