<?php

namespace App\Actions\Jetstream;

use App\Enums\AgencyRole;
use App\Models\Team;
use App\Models\User;
use App\Services\AgencyPermissionService;
use App\Services\AgencyRoleAuditService;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\Events\AddingTeamMember;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class AddTeamMember implements AddsTeamMembers
{
    public function __construct(
        private readonly AgencyPermissionService $permissions,
        private readonly AgencyRoleAuditService $roleAudits,
    ) {}

    /**
     * Add a new team member to the given team.
     */
    public function add(User $user, Team $team, string $email, ?string $role = null): void
    {
        Gate::forUser($user)->authorize('addTeamMember', $team);
        $actorRole = $this->permissions->roleFor($user, $team);
        $newRole = AgencyRole::tryFrom($role ?? '') ?? AgencyRole::Member;

        if (! $actorRole->canManage($newRole)) {
            throw new AuthorizationException('You cannot assign this organisation role.');
        }

        $this->validate($team, $email, $role);

        $newTeamMember = Jetstream::findUserByEmailOrFail($email);

        AddingTeamMember::dispatch($team, $newTeamMember);

        DB::transaction(function () use ($user, $team, $newTeamMember, $newRole): void {
            $team->users()->attach($newTeamMember, ['role' => $newRole->value]);
            $request = Request::createFrom(request());
            $request->setUserResolver(fn () => $user);
            $this->roleAudits->record(
                $request,
                $team,
                $newTeamMember->id,
                'membership_created',
                null,
                $newRole->value,
            );
        });

        TeamMemberAdded::dispatch($team, $newTeamMember);
    }

    /**
     * Validate the add member operation.
     */
    protected function validate(Team $team, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnTeam($team, $email)
        )->validateWithBag('addTeamMember');
    }

    /**
     * Get the validation rules for adding a team member.
     *
     * @return array<string, Rule|array|string>
     */
    protected function rules(): array
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
            'role' => Jetstream::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the team.
     */
    protected function ensureUserIsNotAlreadyOnTeam(Team $team, string $email): Closure
    {
        return function ($validator) use ($team, $email) {
            $validator->errors()->addIf(
                $team->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the team.')
            );
        };
    }
}
