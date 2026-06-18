<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('clinical-case.{caseId}', function ($user) {
    return $user->is_active
        && in_array($user->role, ['admin', 'doctor', 'specialist'], true);
});
