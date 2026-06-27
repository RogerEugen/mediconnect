<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $hospitals = Hospital::where('is_active', true)->orderBy('name')->get();
        $specializations = Specialization::where('is_active', true)->orderBy('name')->get();

        return view('auth.register', compact('hospitals', 'specializations'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', Rule::in(['doctor', 'specialist'])],
            'hospital_name' => ['required', 'string', 'min:2', 'max:200'],
            'specialization_ids' => ['required_if:role,specialist', 'array', 'min:1'],
            'specialization_ids.*' => ['integer', Rule::exists('specializations', 'id')->where('is_active', true)],
            'phone' => ['required', 'string', 'max:20'],
            'staff_card' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $cardPath = $request->file('staff_card')->store('staff-cards', 'local');

        try {
            $user = DB::transaction(function () use ($validated, $request, $cardPath) {
                $hospitalName = preg_replace('/\s+/', ' ', trim($validated['hospital_name']));
                $hospital = Hospital::whereRaw('LOWER(name) = ?', [mb_strtolower($hospitalName)])->first();

                if (! $hospital) {
                    $hospital = Hospital::create([
                        'name' => $hospitalName,
                        'is_active' => false,
                    ]);
                }

                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'role' => $validated['role'],
                    'hospital_id' => $hospital->id,
                    'is_active' => false,
                    'password' => Hash::make($validated['password']),
                ]);

                $user->profile()->create([
                    'phone' => $validated['phone'],
                    'staff_card_path' => $cardPath,
                    'staff_card_original_name' => $request->file('staff_card')->getClientOriginalName(),
                ]);
                $user->hospitals()->attach($hospital->id, ['is_primary' => true]);

                if ($validated['role'] === 'specialist') {
                    $sync = collect($validated['specialization_ids'])
                        ->mapWithKeys(fn ($id, $index) => [$id => ['is_primary' => $index === 0]])
                        ->all();
                    $user->specializations()->sync($sync);
                }

                return $user;
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($cardPath);
            throw $exception;
        }

        event(new Registered($user));

        return redirect()->route('login')
            ->with('status', 'Registration submitted. An administrator will verify your staff card and activate your account.');
    }
}
