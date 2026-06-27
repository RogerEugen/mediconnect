<?php

use App\Events\NotificationSent;
use App\Models\Hospital;
use App\Models\MedicalCase;
use App\Models\Notification;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function clinician(string $role, ?Hospital $hospital = null): User
{
    return User::factory()->create([
        'role' => $role,
        'hospital_id' => $hospital?->id,
        'is_active' => true,
    ]);
}

function discussionCase(User $author, Hospital $hospital, Specialization $specialization): MedicalCase
{
    static $sequence = 0;
    $sequence++;

    return MedicalCase::create([
        'case_number' => 'CASE-2026-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
        'posted_by' => $author->id,
        'hospital_id' => $hospital->id,
        'specialization_id' => $specialization->id,
        'patient_age_group' => 'adult',
        'patient_sex' => 'female',
        'private_reference' => 'LOCAL-42',
        'title' => 'Difficult respiratory presentation',
        'description' => str_repeat('Complex anonymized clinical presentation. ', 3),
        'clinical_history' => 'Relevant anonymized history is provided here.',
        'symptoms' => 'Persistent hypoxaemia and fatigue.',
        'discussion_question' => 'What additional differential diagnoses should be considered?',
        'urgency' => 'medium',
        'status' => 'open',
    ]);
}

test('a specialist sees matching specialties while a doctor sees their own cases', function () {
    $hospital = Hospital::create(['name' => 'MediConnect Hospital']);
    $cardiology = Specialization::create(['name' => 'Cardiology']);
    $neurology = Specialization::create(['name' => 'Neurology']);
    $author = clinician('doctor', $hospital);
    $specialist = clinician('specialist', $hospital);
    $specialist->specializations()->attach($neurology->id);
    $cardiologyCase = discussionCase($author, $hospital, $cardiology);
    $neurologyCase = discussionCase($author, $hospital, $neurology);

    $this->actingAs($specialist)
        ->get(route('clinical-cases.show', $cardiologyCase))
        ->assertForbidden();

    $this->actingAs($specialist)
        ->get(route('clinical-cases.show', $neurologyCase))
        ->assertOk()
        ->assertSee($neurologyCase->title);

    $this->actingAs($author)
        ->get(route('clinical-cases.show', $cardiologyCase))
        ->assertOk();
});

test('administrators can see aggregate counts but cannot open clinical discussions', function () {
    $hospital = Hospital::create(['name' => 'MediConnect Hospital']);
    $specialization = Specialization::create(['name' => 'Internal Medicine']);
    $author = clinician('doctor', $hospital);
    $admin = clinician('admin');
    $case = discussionCase($author, $hospital, $specialization);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('1')
        ->assertDontSee($case->title);

    $this->actingAs($admin)
        ->get(route('clinical-cases.show', $case))
        ->assertForbidden();
});

test('publishing a case notifies only specialists with the matching specialty', function () {
    $hospital = Hospital::create(['name' => 'MediConnect Hospital']);
    $specialization = Specialization::create(['name' => 'Emergency Medicine']);
    $author = clinician('doctor', $hospital);
    $doctor = clinician('doctor', $hospital);
    $specialist = clinician('specialist', $hospital);
    $specialist->specializations()->attach($specialization->id);
    $otherSpecialty = Specialization::create(['name' => 'Dentistry']);
    $unmatchedSpecialist = clinician('specialist', $hospital);
    $unmatchedSpecialist->specializations()->attach($otherSpecialty->id);
    $inactiveDoctor = User::factory()->create([
        'role' => 'doctor',
        'hospital_id' => $hospital->id,
        'is_active' => false,
    ]);
    $admin = clinician('admin');

    $response = $this->actingAs($author)->post(route('clinical-cases.store'), [
        'title' => 'Unexplained neurological deterioration',
        'description' => str_repeat('This is a difficult anonymized clinical case. ', 2),
        'patient_age_group' => 'adult',
        'patient_sex' => 'male',
        'private_reference' => 'LOCAL-99',
        'clinical_history' => 'The anonymized clinical history has evolved over several days.',
        'symptoms' => 'Progressive confusion and weakness.',
        'investigation_results' => 'Initial imaging was inconclusive.',
        'prior_treatments' => 'Supportive care was started.',
        'discussion_question' => 'Which additional investigations should be prioritized?',
        'urgency' => 'high',
        'specialization_id' => $specialization->id,
        'privacy_confirmation' => '1',
    ]);

    $response->assertRedirect();
    expect(Notification::where('user_id', $doctor->id)->where('type', 'new_case')->exists())->toBeFalse()
        ->and(Notification::where('user_id', $specialist->id)->where('type', 'new_case')->exists())->toBeTrue()
        ->and(Notification::where('user_id', $specialist->id)->value('title'))->toBe('New Emergency Medicine case for discussion')
        ->and(Notification::where('user_id', $author->id)->exists())->toBeFalse()
        ->and(Notification::where('user_id', $unmatchedSpecialist->id)->exists())->toBeFalse()
        ->and(Notification::where('user_id', $inactiveDoctor->id)->exists())->toBeFalse()
        ->and(Notification::where('user_id', $admin->id)->exists())->toBeFalse();
});

test('doctors only browse their own cases but can open an eligible similar solved insight', function () {
    $hospital = Hospital::create(['name' => 'MediConnect Hospital']);
    $orthopedics = Specialization::create(['name' => 'Orthopedics']);
    $doctor = clinician('doctor', $hospital);
    $otherDoctor = clinician('doctor', $hospital);
    $source = discussionCase($doctor, $hospital, $orthopedics);
    $similar = discussionCase($otherDoctor, $hospital, $orthopedics);
    $similar->update(['resolution_notes' => 'Immobilization followed by specialist review resolved the case.']);

    $this->actingAs($doctor)
        ->get(route('clinical-cases.index'))
        ->assertOk()
        ->assertSee($source->title)
        ->assertDontSee(route('clinical-cases.show', $similar));

    $this->actingAs($doctor)
        ->get(route('clinical-cases.show', $similar))
        ->assertForbidden();

    $this->actingAs($doctor)
        ->get(route('clinical-cases.similar', [$source, $similar]))
        ->assertOk()
        ->assertSee('% similar')
        ->assertSee('Immobilization followed by specialist review');
});

test('case author is automatically and permanently following the discussion', function () {
    $hospital = Hospital::create(['name' => 'MediConnect Hospital']);
    $specialization = Specialization::create(['name' => 'Orthopedics']);
    $author = clinician('doctor', $hospital);
    $case = discussionCase($author, $hospital, $specialization);

    expect($case->followers()->whereKey($author->id)->exists())->toBeTrue();

    $this->actingAs($author)
        ->get(route('clinical-cases.show', $case))
        ->assertOk()
        ->assertDontSee('Follow discussion')
        ->assertDontSee('Following discussion');

    $this->actingAs($author)
        ->post(route('clinical-cases.follow', $case))
        ->assertForbidden();

    expect($case->followers()->whereKey($author->id)->exists())->toBeTrue();
});

test('notification broadcasting does not wait for a queue worker', function () {
    expect(new NotificationSent(new Notification))
        ->toBeInstanceOf(ShouldBroadcastNow::class);
});

test('admin user filters separate roles and specialties', function () {
    $hospital = Hospital::create(['name' => 'MediConnect Hospital']);
    $cardiology = Specialization::create(['name' => 'Cardiology']);
    $doctor = clinician('doctor', $hospital);
    $specialist = clinician('specialist', $hospital);
    $specialist->specializations()->attach($cardiology->id);
    $admin = clinician('admin');

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['role' => 'doctor']))
        ->assertOk()
        ->assertSee($doctor->name)
        ->assertDontSee($specialist->name);

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['specialization' => $cardiology->id]))
        ->assertOk()
        ->assertSee($specialist->name)
        ->assertDontSee($doctor->name);
});
