<?php
// app/Http/Controllers/Doctor/PatientController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    // ── List & Search ─────────────────────────────────────

    public function index(Request $request)
    {
        $query = Patient::with(['registeredBy', 'medicalRecords'])
                        ->latest();

        // Search by name, UID, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_uid', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $patients = $query->paginate(15)->withQueryString();

        return view('Doctor.patients.index', compact('patients'));
    }

    // ── Create form ───────────────────────────────────────

    public function create()
    {
        return view('Doctor.patients.create');
    }

    // ── Store new patient ─────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'required|in:male,female,other',
            'blood_group'   => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'national_id'   => 'nullable|string|max:50|unique:patients,national_id',
            'emergency_contact_name'  => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $patient = Patient::create([
            ...$validated,
            'patient_uid'   => Patient::generateUid(),
            'registered_by' => Auth::user()->id,
        ]);

        AuditLog::record(
            'registered_patient',
            "Registered new patient: {$patient->full_name} ({$patient->patient_uid})",
            $patient
        );

        return redirect()
            ->route('doctor.patients.show', $patient)
            ->with('success', "Patient {$patient->full_name} registered successfully. UID: {$patient->patient_uid}");
    }

    // ── Show patient profile + medical history ────────────

    public function show(Patient $patient)
    {
        $patient->load([
            'registeredBy',
            'medicalRecords.doctor',
            'medicalRecords.hospital',
            'medicalRecords.attachments',
            'cases',
        ]);

        AuditLog::record(
            'viewed_patient',
            "Viewed patient profile: {$patient->full_name} ({$patient->patient_uid})",
            $patient
        );

        return view('Doctor.patients.show', compact('patient'));
    }

    // ── Edit form ─────────────────────────────────────────

    public function edit(Patient $patient)
    {
        return view('Doctor.patients.edit', compact('patient'));
    }

    // ── Update patient ────────────────────────────────────

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'required|in:male,female,other',
            'blood_group'   => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'national_id'   => 'nullable|string|max:50|unique:patients,national_id,' . $patient->id,
            'emergency_contact_name'  => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $patient->update($validated);

        AuditLog::record(
            'updated_patient',
            "Updated patient record: {$patient->full_name} ({$patient->patient_uid})",
            $patient
        );

        return redirect()
            ->route('doctor.patients.show', $patient)
            ->with('success', 'Patient information updated successfully.');
    }

    // ── Records timeline ──────────────────────────────────

    public function records(Patient $patient)
    {
        $patient->load([
            'medicalRecords.doctor',
            'medicalRecords.hospital',
            'medicalRecords.attachments',
        ]);

        AuditLog::record(
            'viewed_medical_records',
            "Viewed full medical records for: {$patient->full_name} ({$patient->patient_uid})",
            $patient
        );

        return view('Doctor.patients.records', compact('patient'));
    }
}