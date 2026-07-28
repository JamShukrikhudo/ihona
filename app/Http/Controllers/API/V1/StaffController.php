<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StaffController
{
    public function index(Request $request): JsonResponse
    {
        $team = $this->team($request);
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'branch_id' => ['nullable', 'integer'],
            'department_id' => ['nullable', 'integer'],
        ]);

        $query = $team->users()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($query) => $query
                    ->where('users.name', 'like', $term)
                    ->orWhere('users.email', 'like', $term)
                    ->orWhere('team_user.job_title', 'like', $term));
            })
            ->when($request->filled('branch_id'), fn ($query) => $query->where('team_user.branch_id', $request->integer('branch_id')))
            ->when($request->filled('department_id'), fn ($query) => $query->where('team_user.department_id', $request->integer('department_id')))
            ->orderBy('users.name');

        return response()->json(
            $query->paginate(min(max($request->integer('per_page', 20), 1), 100))
                ->through(fn (User $user) => $this->serialize($team, $user))
        );
    }

    public function show(Request $request, int $staff): JsonResponse
    {
        $team = $this->team($request);

        return response()->json(['data' => $this->serialize($team, $this->member($team, $staff))]);
    }

    public function store(Request $request): JsonResponse
    {
        $team = $this->team($request);
        $attributes = $request->validate([
            'email' => ['required', 'email', Rule::exists('users', 'email')],
            ...$this->profileRules($team),
        ]);
        $user = User::where('email', $attributes['email'])->firstOrFail();

        if ($team->hasUser($user)) {
            throw ValidationException::withMessages(['email' => ['This user already belongs to the organisation.']]);
        }

        unset($attributes['email']);
        $team->users()->attach($user, $attributes);

        return response()->json([
            'data' => $this->serialize($team, $this->member($team, $user->id)),
        ], 201);
    }

    public function update(Request $request, int $staff): JsonResponse
    {
        $team = $this->team($request);
        abort_if($team->user_id === $staff, 422, 'The organisation owner profile cannot be changed here.');
        $this->member($team, $staff);
        $attributes = $request->validate($this->profileRules($team, true));
        $team->users()->updateExistingPivot($staff, $attributes);

        return response()->json([
            'data' => $this->serialize($team, $this->member($team, $staff)),
        ]);
    }

    public function destroy(Request $request, int $staff): JsonResponse
    {
        $team = $this->team($request);
        abort_if($team->user_id === $staff, 422, 'The organisation owner cannot be removed.');
        $this->member($team, $staff);
        $team->users()->detach($staff);

        return response()->json(null, 204);
    }

    private function profileRules(Team $team, bool $updating = false): array
    {
        return [
            'role' => [$updating ? 'sometimes' : 'required', Rule::in(['admin', 'editor', 'member'])],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('team_id', $team->id)],
            'department_id' => ['nullable', Rule::exists('departments', 'id')->where('team_id', $team->id)],
            'job_title' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }

    private function member(Team $team, int $staff): User
    {
        return $team->users()
            ->findOrFail($staff);
    }

    private function serialize(Team $team, User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_url' => $user->profile_photo_url,
            'role' => $user->membership->role,
            'branch_id' => $user->membership->branch_id,
            'branch' => $user->membership->branch_id
                ? Branch::query()->select(['id', 'name'])->find($user->membership->branch_id)
                : null,
            'department_id' => $user->membership->department_id,
            'department' => $user->membership->department_id
                ? Department::query()->select(['id', 'name'])->find($user->membership->department_id)
                : null,
            'job_title' => $user->membership->job_title,
            'phone' => $user->membership->phone,
            'bio' => $user->membership->bio,
            'is_public' => $user->membership->is_public,
        ];
    }

    private function team(Request $request): Team
    {
        $user = $request->user();
        $team = $user->current_team_id ? Team::find($user->current_team_id) : null;

        if (! $team || ! $user->allTeams()->contains('id', $team->id)) {
            throw ValidationException::withMessages(['team' => ['Select an organisation you belong to first.']]);
        }

        return $team;
    }
}
