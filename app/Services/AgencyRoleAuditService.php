<?php

namespace App\Services;

use App\Models\AgencyRoleAudit;
use App\Models\Team;
use Illuminate\Http\Request;

class AgencyRoleAuditService
{
    public function record(
        Request $request,
        Team $team,
        int $subjectId,
        string $action,
        ?string $oldRole,
        ?string $newRole,
        ?array $oldPermissions = null,
        ?array $newPermissions = null,
    ): AgencyRoleAudit {
        return AgencyRoleAudit::create([
            'team_id' => $team->id,
            'actor_id' => $request->user()->id,
            'subject_id' => $subjectId,
            'action' => $action,
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'old_permissions' => $oldPermissions,
            'new_permissions' => $newPermissions,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
