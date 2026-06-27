<?php

use App\Models\Hospital;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('admin can review a private staff card and approve clinician access', function () {
    Storage::fake('local');
    $hospital = Hospital::create(['name' => 'MediConnect Hospital']);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $doctor = User::factory()->create([
        'role' => 'doctor',
        'hospital_id' => $hospital->id,
        'is_active' => false,
        'password' => Hash::make('password'),
    ]);
    $card = UploadedFile::fake()->image('doctor-id.jpg');
    $cardPath = $card->store('staff-cards', 'local');
    $doctor->profile()->create([
        'phone' => '0712345678',
        'staff_card_path' => $cardPath,
        'staff_card_original_name' => 'doctor-id.jpg',
    ]);

    $this->post(route('login'), ['email' => $doctor->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();

    $this->actingAs($admin)
        ->get(route('admin.users.staff-card', $doctor))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg');

    $this->actingAs($admin)
        ->patch(route('admin.users.approve', $doctor))
        ->assertSessionHas('success');

    $doctor->refresh();
    expect($doctor->is_active)->toBeTrue()
        ->and($doctor->approved_by)->toBe($admin->id)
        ->and($doctor->approved_at)->not->toBeNull();

    auth()->logout();
    $this->post(route('login'), ['email' => $doctor->email, 'password' => 'password'])
        ->assertRedirect(route('doctor.dashboard'));
    $this->assertAuthenticatedAs($doctor);
});

test('a specialist can register with specialties and a private staff card', function () {
    Storage::fake('local');
    $hospital = Hospital::create(['name' => 'MediConnect Hospital']);
    $dentistry = Specialization::create(['name' => 'Dentistry']);

    $response = $this->post('/register', [
        'name' => 'Dr Dental',
        'email' => 'dental@example.com',
        'role' => 'specialist',
        'hospital_name' => $hospital->name,
        'specialization_ids' => [$dentistry->id],
        'phone' => '0712345678',
        'staff_card' => UploadedFile::fake()->image('staff-card.jpg'),
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertGuest();

    $user = User::where('email', 'dental@example.com')->firstOrFail();
    expect($user->role)->toBe('specialist')
        ->and($user->is_active)->toBeFalse()
        ->and($user->specializations()->whereKey($dentistry->id)->exists())->toBeTrue()
        ->and($user->profile->staff_card_path)->not->toBeNull();
    Storage::disk('local')->assertExists($user->profile->staff_card_path);
});
