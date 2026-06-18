<?php
// app/Http/Controllers/Doctor/CaseController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalCase;
use App\Models\MedicalRecord;
use App\Models\Specialization;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseController extends Controller
{
    // ── List doctor's own cases ───────────────────────────

    public function index()
    {
        $cases = MedicalCase::with([
                        'patient',
                        'specialization',
                        'hospital',
                        'activeAssignment.specialist',
                    ])
                    ->where('posted_by', Auth::id())
                    ->latest()
                    ->paginate(15);

        return view('Doctor.cases.index', compact('cases'));
    }

    // ── Create form ───────────────────────────────────────

    public function create(Patient $patient)
    {
        $specializations = Specialization::where('is_active', true)
                                          ->orderBy('name')
                                          ->get();

        $medicalRecords  = MedicalRecord::where('patient_id', $patient->id)
                                         ->where('doctor_id', Auth::id())
                                         ->orderByDesc('visit_date')
                                         ->get();

        return view('Doctor.cases.create', compact(
            'patient', 'specializations', 'medicalRecords'
        ));
    }

    // ── Store new case ────────────────────────────────────

    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'required|string|min:50',
            'symptoms'           => 'required|string',
            'prior_treatments'   => 'nullable|string',
            'urgency'            => 'required|in:low,medium,high,critical',
            'specialization_id'  => 'required|exists:specializations,id',
            'medical_record_id'  => 'nullable|exists:medical_records,id',
        ]);

        $case = MedicalCase::create([
            ...$validated,
            'case_number'  => MedicalCase::generateCaseNumber(),
            'patient_id'   => $patient->id,
            'posted_by'    => Auth::id(),
            'hospital_id'  => Auth::user()->hospital_id,
            'status'       => 'open',
        ]);

        // Notify all admins that a new case is open
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        foreach ($admins as $admin) {
            Notification::send(
                $admin->id,
                'New case posted — ' . $case->urgency . ' urgency',
                "Dr. " . Auth::user()->name . " posted case {$case->case_number} for patient {$patient->full_name}. Specialization needed: {$case->specialization->name}.",
                'new_case'
            );
        }

        AuditLog::record(
            'posted_case',
            "Posted case {$case->case_number} for patient: {$patient->full_name}",
            $case
        );

        return redirect()
            ->route('doctor.cases.show', $case)
            ->with('success', "Case {$case->case_number} posted successfully. Admin will assign a specialist shortly.");
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
            'discussions.replies.user',
        ]);

        return view('Doctor.cases.show', compact('case'));
    }

    // ── Delete case (only if still open) ─────────────────

    public function destroy(MedicalCase $case)
    {
        if ($case->posted_by !== Auth::id()) {
            abort(403);
        }

        if (!in_array($case->status, ['open'])) {
            return back()->with('error', 'Cannot delete a case that has been assigned or is in progress.');
        }

        $case->delete();

        return redirect()
            ->route('doctor.cases.index')
            ->with('success', 'Case deleted successfully.');
    }
}