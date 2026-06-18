<?php
// app/Http/Controllers/Doctor/DoctorController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\MedicalCase;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $doctor = Auth::user();

        $totalPatients    = Patient::where('registered_by', $doctor->id)->count();
        $totalRecords     = MedicalRecord::where('doctor_id', $doctor->id)->count();
        $openCases        = MedicalCase::where('posted_by', $doctor->id)
                                       ->whereIn('status', ['open', 'assigned', 'in_discussion'])
                                       ->count();
        $recentPatients   = Patient::where('registered_by', $doctor->id)
                                   ->with('medicalRecords')
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return view('Doctor.Dashboard', compact(
            'totalPatients', 'totalRecords', 'openCases', 'recentPatients'
        ));
    }
}