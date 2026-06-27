<?php

// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['hospital', 'specializations', 'profile'])
            ->whereIn('role', ['doctor', 'specialist']);

        if ($request->filled('role')) {
            $request->validate(['role' => ['in:doctor,specialist']]);
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('specialization')) {
            $request->validate(['specialization' => ['integer', 'exists:specializations,id']]);
            $query->whereHas('specializations', fn ($specializations) => $specializations
                ->where('specializations.id', $request->integer('specialization'))
            );
        }

        if ($request->filled('hospital')) {
            $request->validate(['hospital' => ['integer', 'exists:hospitals,id']]);
            $query->where('hospital_id', $request->integer('hospital'));
        }

        if ($request->filled('status')) {
            $request->validate(['status' => ['in:active,pending,inactive']]);
            match ($request->string('status')->toString()) {
                'active' => $query->where('is_active', true),
                'pending' => $query->where('is_active', false)->whereHas('profile', fn ($profile) => $profile->whereNotNull('staff_card_path')),
                'inactive' => $query->where('is_active', false)->whereDoesntHave('profile', fn ($profile) => $profile->whereNotNull('staff_card_path')),
            };
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($users) use ($search) {
                $users->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $specializations = Specialization::where('is_active', true)->orderBy('name')->get();
        $hospitals = Hospital::where('is_active', true)->orderBy('name')->get();
        $roleCounts = [
            'all' => User::whereIn('role', ['doctor', 'specialist'])->count(),
            'doctor' => User::where('role', 'doctor')->count(),
            'specialist' => User::where('role', 'specialist')->count(),
            'pending' => User::whereIn('role', ['doctor', 'specialist'])
                ->where('is_active', false)
                ->whereHas('profile', fn ($profile) => $profile->whereNotNull('staff_card_path'))
                ->count(),
        ];

        return view('Admin.users.index', compact('users', 'specializations', 'hospitals', 'roleCounts'));
    }

    public function create()
    {
        $hospitals = Hospital::where('is_active', true)->orderBy('name')->get();
        $specializations = Specialization::where('is_active', true)->orderBy('name')->get();

        return view('Admin.users.create', compact('hospitals', 'specializations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:doctor,specialist',
            'hospital_id' => 'required|exists:hospitals,id',
            'password' => ['required', Password::min(8)],
            'specialization_ids' => 'required_if:role,specialist|array|min:1',
            'specialization_ids.*' => 'exists:specializations,id',
            'is_primary_spec' => ['nullable', Rule::in($request->input('specialization_ids', []))],
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'hospital_id' => $validated['hospital_id'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        // Assign to hospital pivot
        $user->hospitals()->attach($validated['hospital_id'], [
            'is_primary' => true,
        ]);

        // If specialist, assign specializations
        if ($validated['role'] === 'specialist' && ! empty($validated['specialization_ids'])) {
            $primaryId = $request->input('is_primary_spec', $validated['specialization_ids'][0]);

            $syncData = [];
            foreach ($validated['specialization_ids'] as $specId) {
                $syncData[$specId] = [
                    'is_primary' => ($specId == $primaryId),
                    'certified_at' => null,
                ];
            }
            $user->specializations()->sync($syncData);
        }

        return redirect()->route('admin.users.index')
            ->with('success', ucfirst($validated['role']).' account created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['hospital', 'specializations', 'hospitals', 'profile', 'photo', 'approvedBy']);

        return view('Admin.users.show', compact('user'));
    }

    public function staffCard(User $user)
    {
        $user->loadMissing('profile');
        abort_unless($user->profile?->staff_card_path, 404);
        abort_unless(Storage::disk('local')->exists($user->profile->staff_card_path), 404);

        $path = Storage::disk('local')->path($user->profile->staff_card_path);
        $mime = Storage::disk('local')->mimeType($user->profile->staff_card_path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $user->profile->staff_card_original_name ?: 'staff-card').'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function approve(User $user)
    {
        abort_unless(in_array($user->role, ['doctor', 'specialist'], true), 404);

        if ($user->is_active) {
            return back()->with('success', 'This clinician account is already active.');
        }

        $user->loadMissing('profile');
        if (! $user->profile?->staff_card_path || ! Storage::disk('local')->exists($user->profile->staff_card_path)) {
            return back()->with('error', 'Approval failed: reviewable staff ID was not found.');
        }

        $user->update([
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);
        $user->hospital?->update(['is_active' => true]);

        return back()->with('success', "{$user->name}'s account has been approved and can now access MediConnect.");
    }

    public function edit(User $user)
    {
        $hospitals = Hospital::where('is_active', true)->orderBy('name')->get();
        $specializations = Specialization::where('is_active', true)->orderBy('name')->get();

        $user->load('specializations');

        return view('Admin.users.edit', compact('user', 'hospitals', 'specializations'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'hospital_id' => 'required|exists:hospitals,id',
            'specialization_ids' => $user->role === 'specialist'
                ? 'required|array|min:1'
                : 'nullable|array',
            'specialization_ids.*' => 'exists:specializations,id',
            'is_primary_spec' => ['nullable', Rule::in($request->input('specialization_ids', []))],
            'password' => ['nullable', Password::min(8)],
        ]);

        // Update base info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'hospital_id' => $validated['hospital_id'],
            'password' => $validated['password']
                                ? Hash::make($validated['password'])
                                : $user->password,
        ]);

        // Update primary hospital pivot
        $user->hospitals()->syncWithoutDetaching([
            $validated['hospital_id'] => ['is_primary' => true],
        ]);

        // Update specializations if specialist
        if ($user->role === 'specialist' && ! empty($validated['specialization_ids'])) {
            $primaryId = $request->input('is_primary_spec', $validated['specialization_ids'][0]);

            $syncData = [];
            foreach ($validated['specialization_ids'] as $specId) {
                $syncData[$specId] = [
                    'is_primary' => ($specId == $primaryId),
                    'certified_at' => null,
                ];
            }
            $user->specializations()->sync($syncData);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->update([
            'is_active' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User access deactivated. The account was retained to preserve clinical and audit history.');
    }

    public function toggle(User $user)
    {
        if ($user->id === Auth::user()->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        if (! $user->is_active) {
            $user->update(['approved_at' => null, 'approved_by' => null]);
        }

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User account {$status} successfully.");
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('Password@123'),
        ]);

        return back()->with('success', 'Password reset to Password@123. Ask the user to change it on next login.');
    }
}
