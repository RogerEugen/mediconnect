<?php
// app/Http/Controllers/Admin/HospitalController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    public function index()
    {
        $hospitals = Hospital::latest()->paginate(10);
        return view('Admin.hospitals.index', compact('hospitals'));
    }

    public function create()
    {
        return view('Admin.hospitals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:200|unique:hospitals,name',
            'address'        => 'nullable|string',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:150',
            'license_number' => 'nullable|string|max:100',
        ]);

        Hospital::create($validated);

        return redirect()->route('admin.hospitals.index')
                         ->with('success', 'Hospital created successfully.');
    }

    public function show(Hospital $hospital)
    {
        $hospital->load('users');
        return view('Admin.hospitals.show', compact('hospital'));
    }

    public function edit(Hospital $hospital)
    {
        return view('Admin.hospitals.edit', compact('hospital'));
    }

    public function update(Request $request, Hospital $hospital)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:200|unique:hospitals,name,' . $hospital->id,
            'address'        => 'nullable|string',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:150',
            'license_number' => 'nullable|string|max:100',
        ]);

        $hospital->update($validated);

        return redirect()->route('admin.hospitals.index')
                         ->with('success', 'Hospital updated successfully.');
    }

    public function destroy(Hospital $hospital)
    {
        // Prevent delete if users are attached
        if ($hospital->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a hospital that has users assigned.');
        }

        $hospital->delete();

        return redirect()->route('admin.hospitals.index')
                         ->with('success', 'Hospital deleted.');
    }

    public function toggle(Hospital $hospital)
    {
        $hospital->update(['is_active' => !$hospital->is_active]);

        $status = $hospital->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Hospital {$status} successfully.");
    }
}