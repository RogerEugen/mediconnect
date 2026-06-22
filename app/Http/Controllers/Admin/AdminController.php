<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalCase;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'clinicians' => User::whereIn('role', ['doctor', 'specialist'])->where('is_active', true)->count(),
            'active_cases' => MedicalCase::whereIn('status', ['open', 'in_discussion'])->count(),
            'unanswered' => MedicalCase::doesntHave('discussions')->count(),
            'resolved' => MedicalCase::where('status', 'resolved')->count(),
        ];

        return view('Admin.Dashboard', compact('stats'));
    }
}
