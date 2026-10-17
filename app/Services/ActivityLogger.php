<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function record(
        Request $request,
        string $action,
        string $description,
        ?User $user = null,
        ?string $subjectType = null,
        $subjectId = null,
        ?string $actorName = null,
        ?string $actorEmail = null
    ): ActivityLog {
        $user = $user ?: $request->user();

        return ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : $actorName,
            'user_email' => $user ? $user->email : $actorEmail,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId === null ? null : (string) $subjectId,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);
    }
}
