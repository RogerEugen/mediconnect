<?php

use App\Models\MedicalCase;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('clinical-case.{caseId}', function ($user, $caseId) {
    $case = MedicalCase::find($caseId);

    return $user->is_active
        && $case
        && $case->isVisibleTo($user);
});
