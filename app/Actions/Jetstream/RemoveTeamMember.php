<?php

namespace App\Actions\Jetstream;

use App\Enums\AgencyRole;
use App\Models\Team;
use App\Models\User;
use App\Services\AgencyPermissionService;
use App\Services\AgencyRoleAuditService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Events\TeamMemberRemoved;

class RemoveTeamMember implements RemovesTeamMembers
{
    public function __construct(
        private readonly AgencyPermissionService $permissions,
        private readonly AgencyRoleAuditService $roleAudits,
    ) {}

    /**
     * Remove the team member from the given team.
     */
    public function remove(User $user, Team $team, User $teamMember): void
    {
        $this->authorize($user, $team, $teamMember);

        $this->ensureUserDoesNotOwnTeam($teamMember, $team);

        $subjectRole = $this->permissions->roleFor($teamMember, $team);

        if ($user->id !== $teamMember->id) {
            $actorRole = $this->permissions->roleFor($user, $team);

            if (! $actorRole->canManage($subjectRole)) {
                throw new AuthorizationException('You cannot remove a member with this organisation role.');
            }
        }

        DB::transaction(function () use ($user, $team, $teamMember, $subjectRole): void {
            $team->removeUser($teamMember);
            $request = Request::createFrom(request());
            $request->setUserResolver(fn () => $user);
            $this->roleAudits->record(
                $request,
                $team,
                $teamMember->id,
                'membership_removed',
                $subjectRole === AgencyRole::Owner ? null : $subjectRole->value,
                null,
            );
        });

        TeamMemberRemoved::dispatch($team, $teamMember);
    }

    /**
     * Authorize that the user can remove the team member.
     */
    protected function authorize(User $user, Team $team, User $teamMember): void
    {
        if (! Gate::forUser($user)->check('removeTeamMember', $team) &&
            $user->id !== $teamMember->id) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the team.
     */
    protected function ensureUserDoesNotOwnTeam(User $teamMember, Team $team): void
    {
        if ($teamMember->id === $team->owner->id) {
            throw ValidationException::withMessages([
                'team' => [__('You may not leave a team that you created.')],
            ])->errorBag('removeTeamMember');
        }
    }
}
