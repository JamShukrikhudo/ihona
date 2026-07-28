<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ViewingController extends TenantCrudController
{
    protected string $model = Appointment::class;
    protected string $routeParameter = 'viewing';
    protected array $searchable = ['name', 'contact', 'notes'];
    protected array $filterable = ['property_id', 'agent_id', 'staff_id', 'status'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);
        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'agent_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'staff_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'appointment_date' => [$record ? 'sometimes' : 'required', 'date'],
            'status' => ['sometimes', Rule::in(['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])],
            'name' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['user_id'] = $request->user()->id;
        return $attributes;
    }
}
