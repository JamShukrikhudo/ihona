<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\Parties\Models\ContactMessage;

final class CreateContactMessage
{
    public function handle(array $attributes): ContactMessage
    {
        $rules = ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email:rfc', 'max:255'], 'phone' => ['nullable', 'string', 'max:50'], 'interest' => ['nullable', Rule::in(['buying', 'selling', 'renting', 'letting', 'other'])], 'message' => ['required', 'string', 'max:5000']];
        if (array_key_exists('property_id', $attributes) && filled($attributes['property_id']) && Schema::hasTable('real_estate_properties')) {
            $rules['property_id'] = ['nullable', 'integer', Rule::exists('real_estate_properties', 'id')->whereNull('deleted_at')];
        }
        $validated = Validator::make($attributes, $rules, ['email.email' => 'Add the part after the @ so we can reply.', 'property_id.exists' => 'That property is no longer listed.'])->validate();

        return DB::transaction(fn (): ContactMessage => ContactMessage::query()->create($validated));
    }
}
