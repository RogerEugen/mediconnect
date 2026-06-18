<?php
// app/Http/Controllers/Specialist/SpecialistController.php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\CaseAssignment;
use App\Models\MedicalCase;
use Illuminate\Support\Facades\Auth;

class SpecialistController extends Controller
{
    public function dashboard()
    {
        $specialist = Auth::user();

        // Case counts
        $pendingCount    = CaseAssignment::where('specialist_id', $specialist->id)
                                          ->where('status', 'pending')->count();
        $inProgressCount = CaseAssignment::where('specialist_id', $specialist->id)
                                          ->where('status', 'in_progress')->count();
        $completedCount  = CaseAssignment::where('specialist_id', $specialist->id)
                                          ->where('status', 'completed')->count();

        // Recent assigned cases
        $recentCases = CaseAssignment::with([
            'case.patient',
            'case.specialization',
            'case.hospital'
        ])
            ->join('cases', 'case_assignments.case_id', '=', 'cases.id')
            ->where('case_assignments.specialist_id', $specialist->id)
            ->whereNotIn('case_assignments.status', ['completed', 'declined'])
            ->orderByRaw("FIELD(case_assignments.status, 'in_progress', 'pending') DESC")
            ->orderByRaw("FIELD(cases.urgency, 'critical', 'high', 'medium', 'low')")
            ->select('case_assignments.*')
            ->take(6)
            ->get();
        // Unread notifications
        $unreadCount = Auth::user()
            ->notifications()
            ->where('is_read', false)
            ->count();

        return view('Specialist.Dashboard', compact(
            'pendingCount', 'inProgressCount', 'completedCount',
            'recentCases', 'unreadCount'
        ));
    }
}