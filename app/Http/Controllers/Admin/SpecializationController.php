<?php
// app/Http/Controllers/Admin/SpecializationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::latest()->paginate(10);
        return view('Admin.specializations.index', compact('specializations'));
    }

    public function create()
    {
        return view('Admin.specializations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:specializations,name',
            'description' => 'nullable|string',
        ]);

        Specialization::create($validated);

        return redirect()->route('admin.specializations.index')
                         ->with('success', 'Specialization created successfully.');
    }

    public function show(Specialization $specialization)
    {
        $specialization->load('doctors');
        return view('admin.specializations.show', compact('specialization'));
    }

    public function edit(Specialization $specialization)
    {
        return view('Admin.specializations.edit', compact('specialization'));
    }

    public function update(Request $request, Specialization $specialization)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:specializations,name,' . $specialization->id,
            'description' => 'nullable|string',
        ]);

        $specialization->update($validated);

        return redirect()->route('admin.specializations.index')
                         ->with('success', 'Specialization updated successfully.');
    }

    public function destroy(Specialization $specialization)
    {
        if ($specialization->doctors()->count() > 0) {
            return back()->with('error', 'Cannot delete a specialization that has doctors assigned.');
        }

        $specialization->delete();

        return redirect()->route('admin.specializations.index')
                         ->with('success', 'Specialization deleted.');
    }

    public function toggle(Specialization $specialization)
    {
        $specialization->update(['is_active' => !$specialization->is_active]);

        $status = $specialization->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Specialization {$status} successfully.");
    }
}