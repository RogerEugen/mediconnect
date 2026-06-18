<?php
// app/Http/Controllers/Admin/CaseAssignmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalCase;
use App\Models\CaseAssignment;
use App\Models\User;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseAssignmentController extends Controller
{
    // ── All cases ─────────────────────────────────────────

    public function index(Request $request)
    {
        $query = MedicalCase::with([
            'patient',
            'postedBy',
            'specialization',
            'hospital',
            'activeAssignment.specialist',
        ])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by urgency
        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        $cases = $query->paginate(15)->withQueryString();

        // Counts for tabs
        $counts = [
            'all'           => MedicalCase::count(),
            'open'          => MedicalCase::where('status', 'open')->count(),
            'assigned'      => MedicalCase::where('status', 'assigned')->count(),
            'in_discussion' => MedicalCase::where('status', 'in_discussion')->count(),
            'resolved'      => MedicalCase::where('status', 'resolved')->count(),
        ];

        return view('Admin.cases.index', compact('cases', 'counts'));
    }

    // ── Show case ─────────────────────────────────────────

    public function show(MedicalCase $case)
    {
        $case->load([
            'patient',
            'medicalRecord',
            'postedBy',
            'hospital',
            'specialization',
            'assignments.specialist',
            'assignments.assignedBy',
            'discussions.user',
        ]);

        return view('Admin.cases.show', compact('case'));
    }

    // ── Assign form ───────────────────────────────────────

    public function assign(MedicalCase $case)
    {
        $case->load(['patient', 'specialization', 'assignments']);

        // Get specialists with the matching specialization
        // and show their current active case count
        $specialists = User::where('role', 'specialist')
            ->where('is_active', true)
            ->whereHas('specializations', function ($q) use ($case) {
                $q->where('specializations.id', $case->specialization_id);
            })
            ->withCount([
                'assignedCases as active_cases_count' => function ($q) {
                    $q->whereIn('status', ['pending', 'accepted', 'in_progress']);
                }
            ])
            ->with(['hospital', 'specializations'])
            ->orderBy('active_cases_count')
            ->get();

        return view('Admin.cases.assign', compact('case', 'specialists'));
    }

    // ── Store assignment ──────────────────────────────────

    public function storeAssignment(Request $request, MedicalCase $case)
    {
        $validated = $request->validate([
            'specialist_id' => 'required|exists:users,id',
            'due_date'      => 'nullable|date|after:today',
            'notes'         => 'nullable|string|max:500',
        ]);

        // Create assignment record
        $assignment = CaseAssignment::create([
            'case_id'       => $case->id,
            'specialist_id' => $validated['specialist_id'],
            'assigned_by'   => Auth::id(),
            'status'        => 'pending',
            'due_date'      => $validated['due_date'] ?? null,
        ]);

        // Update case status
        $case->update(['status' => 'assigned']);

        $specialist = User::find($validated['specialist_id']);

        // Notify specialist
        Notification::send(
            $specialist->id,
            'New case assigned to you',
            "You have been assigned to case {$case->case_number} for patient {$case->patient->full_name}. Urgency: {$case->urgency}." .
            ($validated['notes'] ? " Note from admin: {$validated['notes']}" : ''),
            'case_assigned'
        );

        // Notify the doctor who posted
        Notification::send(
            $case->posted_by,
            'Specialist assigned to your case',
            "A specialist ({$specialist->name}) has been assigned to your case {$case->case_number}.",
            'specialist_assigned'
        );

        AuditLog::record(
            'assigned_case',
            "Assigned case {$case->case_number} to specialist: {$specialist->name}",
            $case
        );

        return redirect()
            ->route('admin.cases.show', $case)
            ->with('success', "Case {$case->case_number} assigned to {$specialist->name} successfully.");
    }

    // ── Mark resolved ─────────────────────────────────────

    public function resolve(Request $request, MedicalCase $case)
    {
        $request->validate([
            'resolution_notes' => 'required|string|min:10',
        ]);

        $case->update([
            'status'           => 'resolved',
            'resolution_notes' => $request->resolution_notes,
            'resolved_at'      => now(),
        ]);

        // Update assignment
        if ($case->activeAssignment) {
            $case->activeAssignment->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);
        }

        // Notify doctor
        Notification::send(
            $case->posted_by,
            'Case resolved',
            "Your case {$case->case_number} has been marked as resolved.",
            'case_resolved'
        );

        AuditLog::record('resolved_case', "Resolved case {$case->case_number}", $case);

        return back()->with('success', 'Case marked as resolved.');
    }

    // ── Close case ────────────────────────────────────────

    public function close(MedicalCase $case)
    {
        $case->update(['status' => 'closed']);

        AuditLog::record('closed_case', "Closed case {$case->case_number}", $case);

        return back()->with('success', 'Case closed.');
    }
}