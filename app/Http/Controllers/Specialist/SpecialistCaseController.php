<?php
// app/Http/Controllers/Specialist/SpecialistCaseController.php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\CaseAssignment;
use App\Models\MedicalCase;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SpecialistCaseController extends Controller
{
    // ── List assigned cases ───────────────────────────────

    public function index(Request $request)
    {
        $query = CaseAssignment::with([
                        'case.patient',
                        'case.specialization',
                        'case.hospital',
                        'case.postedBy',
                    ])
                    ->where('specialist_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('case_assignments.status', $request->status);
        }

        // Sort critical/high first
        $query->join('cases', 'case_assignments.case_id', '=', 'cases.id')
              ->select('case_assignments.*')
              ->orderByRaw("FIELD(case_assignments.status, 'in_progress', 'pending', 'accepted', 'completed', 'declined')")
              ->orderByRaw("FIELD(cases.urgency, 'critical', 'high', 'medium', 'low')");

        $assignments = $query->paginate(15)->withQueryString();

        $counts = [
            'all'         => CaseAssignment::where('specialist_id', Auth::id())->count(),
            'pending'     => CaseAssignment::where('specialist_id', Auth::id())->where('status', 'pending')->count(),
            'in_progress' => CaseAssignment::where('specialist_id', Auth::id())->where('status', 'in_progress')->count(),
            'completed'   => CaseAssignment::where('specialist_id', Auth::id())->where('status', 'completed')->count(),
            'declined'    => CaseAssignment::where('specialist_id', Auth::id())->where('status', 'declined')->count(),
        ];

        return view('Specialist.cases.index', compact('assignments', 'counts'));
    }

    // ── Show case details ─────────────────────────────────

    public function show(MedicalCase $case)
    {
        // Verify this specialist is assigned
        $assignment = CaseAssignment::where('case_id', $case->id)
            ->where('specialist_id', Auth::id())
            ->firstOrFail();

        $case->load([
            'patient.medicalRecords.hospital',
            'patient.medicalRecords.doctor',
            'patient.medicalRecords.attachments',
            'postedBy',
            'hospital',
            'specialization',
            'medicalRecord.attachments',
            'assignments.assignedBy',
            'discussions' => fn($q) => $q->topLevel()->with([
                'user',
                'replies.user',
            ])->oldest(),
        ]);

        AuditLog::record(
            'specialist_viewed_case',
            "Specialist viewed case {$case->case_number} for patient: {$case->patient->full_name}",
            $case
        );

        return view('Specialist.cases.show', compact('case', 'assignment'));
    }

    // ── Accept assignment ─────────────────────────────────

    public function accept(CaseAssignment $assignment)
    {
        if ($assignment->specialist_id !== Auth::id()) abort(403);

        $assignment->update(['status' => 'in_progress']);

        // Update case status
        $assignment->case->update(['status' => 'in_discussion']);

        // Notify doctor
        Notification::send(
            $assignment->case->posted_by,
            'Specialist accepted your case',
            "Dr. " . Auth::user()->name . " has accepted case {$assignment->case->case_number} and is now reviewing it.",
            'case_accepted'
        );

        // Notify admin
        $this->notifyAdmins(
            'Case accepted by specialist',
            "Case {$assignment->case->case_number} has been accepted by " . Auth::user()->name . ".",
            'case_accepted'
        );

        AuditLog::record('accepted_case', "Accepted case {$assignment->case->case_number}", $assignment->case);

        return back()->with('success', 'Case accepted. You can now review and post your expert opinion.');
    }

    // ── Decline assignment ────────────────────────────────

    public function decline(Request $request, CaseAssignment $assignment)
    {
        if ($assignment->specialist_id !== Auth::id()) abort(403);

        $request->validate([
            'decline_reason' => 'required|string|min:10|max:500',
        ]);

        $assignment->update([
            'status'         => 'declined',
            'decline_reason' => $request->decline_reason,
        ]);

        // Revert case to open so admin can reassign
        $assignment->case->update(['status' => 'open']);

        // Notify admin
        $this->notifyAdmins(
            'Case declined — needs reassignment',
            "Specialist " . Auth::user()->name . " declined case {$assignment->case->case_number}. Reason: {$request->decline_reason}",
            'case_declined'
        );

        AuditLog::record('declined_case', "Declined case {$assignment->case->case_number}", $assignment->case);

        return redirect()
            ->route('specialist.cases.index')
            ->with('success', 'Case declined. Admin has been notified to reassign.');
    }

    // ── Mark complete ─────────────────────────────────────

    public function complete(CaseAssignment $assignment)
    {
        if ($assignment->specialist_id !== Auth::id()) abort(403);

        $assignment->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        $assignment->case->update(['status' => 'resolved']);

        // Notify doctor
        Notification::send(
            $assignment->case->posted_by,
            'Specialist has completed the case review',
            "Dr. " . Auth::user()->name . " has completed their review of case {$assignment->case->case_number}. Please review the expert opinion.",
            'case_completed'
        );

        // Notify admins
        $this->notifyAdmins(
            'Case completed by specialist',
            "Case {$assignment->case->case_number} has been completed by " . Auth::user()->name . ".",
            'case_completed'
        );

        AuditLog::record('completed_case', "Completed case {$assignment->case->case_number}", $assignment->case);

        return back()->with('success', 'Case marked as completed. Doctor and admin have been notified.');
    }

    // ── Helper: notify all admins ─────────────────────────

    private function notifyAdmins(string $title, string $message, string $type): void
    {
        \App\Models\User::where('role', 'admin')
            ->where('is_active', true)
            ->each(fn($admin) => Notification::send($admin->id, $title, $message, $type));
    }
}