<?php
// app/Http/Controllers/Doctor/MedicalRecordController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Attachment;
use App\Models\Hospital;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    // ── Create form ───────────────────────────────────────

    public function create(Patient $patient)
    {
        $hospitals = Hospital::where('is_active', true)->orderBy('name')->get();

        return view('Doctor.medical-records.create', compact('patient', 'hospitals'));
    }

    // ── Store new record + attachments ────────────────────

    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'visit_date'     => 'required|date|before_or_equal:today',
            'visit_type'     => 'required|in:outpatient,inpatient,emergency,follow_up',
            'hospital_id'    => 'required|exists:hospitals,id',
            'symptoms'       => 'required|string',
            'diagnosis'      => 'required|string',
            'treatment_plan' => 'required|string',
            'prescription'   => 'nullable|string',
            'notes'          => 'nullable|string',
            'status'         => 'required|in:active,resolved,pending',
            // Attachments
            'attachments'          => 'nullable|array|max:10',
            'attachments.*'        => 'file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx',
            'attachment_desc'      => 'nullable|array',
            'attachment_desc.*'    => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create the medical record
            $record = MedicalRecord::create([
                'patient_id'     => $patient->id,
                'doctor_id'      => Auth::user()->id,
                'hospital_id'    => $validated['hospital_id'],
                'visit_date'     => $validated['visit_date'],
                'visit_type'     => $validated['visit_type'],
                'symptoms'       => $validated['symptoms'],
                'diagnosis'      => $validated['diagnosis'],
                'treatment_plan' => $validated['treatment_plan'],
                'prescription'   => $validated['prescription'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'status'         => $validated['status'],
            ]);

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    $originalName = $file->getClientOriginalName();
                    $path         = $file->store("attachments/{$patient->patient_uid}", 'public');
                    $sizeKb       = (int) ceil($file->getSize() / 1024);

                    Attachment::create([
                        'medical_record_id' => $record->id,
                        'uploaded_by'       => Auth::user()->id,
                        'file_name'         => $originalName,
                        'file_path'         => $path,
                        'file_type'         => $file->getMimeType(),
                        'file_size'         => $sizeKb,
                        'description'       => $request->input("attachment_desc.{$index}"),
                        'created_at'        => now(),
                    ]);
                }
            }

            AuditLog::record(
                'created_medical_record',
                "Added medical record for patient: {$patient->full_name} — Diagnosis: {$record->diagnosis}",
                $record
            );

            DB::commit();

            return redirect()
                ->route('doctor.medical-records.show', $record)
                ->with('success', 'Medical record added successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // ── Show single record ────────────────────────────────

    public function show(MedicalRecord $medical_record)
    {
        $record = $medical_record;

        $record->load([
            'patient',
            'doctor',
            'hospital',
            'attachments.uploadedBy',
            'cases',
        ]);

        AuditLog::record(
            'viewed_medical_record',
            "Viewed medical record #{$record->id} for patient: {$record->patient?->full_name}",
            $record
        );

        return view('Doctor.medical-records.show', compact('record'));
    }

    // ── Edit form ─────────────────────────────────────────

    public function edit(MedicalRecord $record)
    {
        // Only the doctor who created it can edit
        if ($record->doctor_id !== Auth::user()->id) {
            abort(403, 'You can only edit records you created.');
        }

        $record->load(['patient', 'attachments']);
        $hospitals = Hospital::where('is_active', true)->orderBy('name')->get();

        return view('Doctor.medical-records.edit', compact('record', 'hospitals'));
    }

    // ── Update record ─────────────────────────────────────

    public function update(Request $request, MedicalRecord $record)
    {
        if ($record->doctor_id !== Auth::user()->id) {
            abort(403, 'You can only edit records you created.');
        }

        $validated = $request->validate([
            'visit_date'     => 'required|date|before_or_equal:today',
            'visit_type'     => 'required|in:outpatient,inpatient,emergency,follow_up',
            'hospital_id'    => 'required|exists:hospitals,id',
            'symptoms'       => 'required|string',
            'diagnosis'      => 'required|string',
            'treatment_plan' => 'required|string',
            'prescription'   => 'nullable|string',
            'notes'          => 'nullable|string',
            'status'         => 'required|in:active,resolved,pending',
            // New attachments
            'attachments'       => 'nullable|array|max:10',
            'attachments.*'     => 'file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx',
            'attachment_desc'   => 'nullable|array',
            'attachment_desc.*' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $record->update([
                'visit_date'     => $validated['visit_date'],
                'visit_type'     => $validated['visit_type'],
                'hospital_id'    => $validated['hospital_id'],
                'symptoms'       => $validated['symptoms'],
                'diagnosis'      => $validated['diagnosis'],
                'treatment_plan' => $validated['treatment_plan'],
                'prescription'   => $validated['prescription'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'status'         => $validated['status'],
            ]);

            // Handle new file uploads
            if ($request->hasFile('attachments')) {
                $patient = $record->patient;
                foreach ($request->file('attachments') as $index => $file) {
                    $path   = $file->store("attachments/{$patient->patient_uid}", 'public');
                    $sizeKb = (int) ceil($file->getSize() / 1024);

                    Attachment::create([
                        'medical_record_id' => $record->id,
                        'uploaded_by'       => Auth::user()->id,
                        'file_name'         => $file->getClientOriginalName(),
                        'file_path'         => $path,
                        'file_type'         => $file->getMimeType(),
                        'file_size'         => $sizeKb,
                        'description'       => $request->input("attachment_desc.{$index}"),
                        'created_at'        => now(),
                    ]);
                }
            }

            AuditLog::record(
                'updated_medical_record',
                "Updated medical record #{$record->id} for patient: {$record->patient->full_name}",
                $record
            );

            DB::commit();

            return redirect()
                ->route('doctor.medical-records.show', $record)
                ->with('success', 'Medical record updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ── Delete record ─────────────────────────────────────

    public function destroy(MedicalRecord $record)
    {
        if ($record->doctor_id !== Auth::user()->id) {
            abort(403, 'You can only delete records you created.');
        }

        $patientId = $record->patient_id;

        // Delete attached files from storage
        foreach ($record->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $record->delete();

        AuditLog::record(
            'deleted_medical_record',
            "Deleted medical record #{$record->id}",
            $record
        );

        return redirect()
            ->route('doctor.patients.records', $patientId)
            ->with('success', 'Medical record deleted.');
    }

    // ── Delete single attachment ──────────────────────────

    public function destroyAttachment(Attachment $attachment)
    {
        $record = $attachment->medicalRecord;

        if ($record->doctor_id !== Auth::user()->id) {
            abort(403);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Attachment removed.');
    }
}