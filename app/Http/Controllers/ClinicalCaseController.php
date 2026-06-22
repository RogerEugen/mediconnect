<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MedicalCase;
use App\Models\Notification;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClinicalCaseController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureClinician();

        $query = MedicalCase::query()
            ->visibleTo(Auth::user())
            ->with(['postedBy.profile', 'hospital', 'specialization'])
            ->withCount('discussions');

        $query->when($request->filled('search'), function ($query) use ($request) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($query) use ($search) {
                $query->where('case_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('symptoms', 'like', "%{$search}%");
            });
        });

        $query->when($request->filled('specialization'), fn ($query) => $query->where('specialization_id', $request->integer('specialization'))
        );
        $query->when($request->filled('urgency'), fn ($query) => $query->where('urgency', $request->string('urgency'))
        );
        $query->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status'))
        );
        $query->when($request->boolean('mine'), fn ($query) => $query->where('posted_by', Auth::id())
        );
        $query->when($request->boolean('following'), fn ($query) => $query->whereHas('followers', fn ($followers) => $followers->where('users.id', Auth::id()))
        );

        match ($request->string('sort')->toString()) {
            'active' => $query->orderByDesc('updated_at'),
            'unanswered' => $query->orderBy('discussions_count')->latest(),
            'urgent' => $query->orderByRaw("FIELD(urgency, 'critical', 'high', 'medium', 'low')")->latest(),
            default => $query->latest(),
        };

        $cases = $query->paginate(12)->withQueryString();
        $specializations = Specialization::where('is_active', true)->orderBy('name')->get();
        $visibleCases = MedicalCase::query()->visibleTo(Auth::user());
        $stats = [
            'open' => (clone $visibleCases)->whereIn('status', ['open', 'in_discussion'])->count(),
            'unanswered' => (clone $visibleCases)->doesntHave('discussions')->count(),
            'resolved' => (clone $visibleCases)->where('status', 'resolved')->count(),
        ];

        return view('clinical-cases.index', compact('cases', 'specializations', 'stats'));
    }

    public function create(): View
    {
        $this->ensureClinician();

        $specializations = Specialization::where('is_active', true)->orderBy('name')->get();

        return view('clinical-cases.create', compact('specializations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureClinician();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'patient_age_group' => ['required', 'in:neonate,infant,child,adolescent,young_adult,adult,older_adult'],
            'patient_sex' => ['required', 'in:male,female,other,not_relevant'],
            'private_reference' => ['nullable', 'string', 'max:100'],
            'clinical_history' => ['required', 'string', 'min:20'],
            'symptoms' => ['required', 'string', 'min:10'],
            'investigation_results' => ['nullable', 'string'],
            'prior_treatments' => ['nullable', 'string'],
            'discussion_question' => ['required', 'string', 'min:10'],
            'urgency' => ['required', 'in:low,medium,high,critical'],
            'specialization_id' => ['required', 'exists:specializations,id'],
            'author_anonymous' => ['nullable', 'boolean'],
            'privacy_confirmation' => ['accepted'],
        ]);

        unset($validated['privacy_confirmation']);

        $case = MedicalCase::create([
            ...$validated,
            'case_number' => MedicalCase::generateCaseNumber(),
            'posted_by' => Auth::id(),
            'hospital_id' => Auth::user()->hospital_id,
            'status' => 'open',
            'author_anonymous' => $request->boolean('author_anonymous'),
        ]);

        $case->loadMissing('specialization');
        $specialtyName = $case->specialization?->name ?? 'clinical';

        User::where('is_active', true)
            ->whereIn('role', ['doctor', 'specialist'])
            ->whereKeyNot(Auth::id())
            ->each(fn (User $user) => Notification::send(
                $user->id,
                "New {$specialtyName} case for discussion",
                ($case->author_anonymous ? 'A verified clinician' : Auth::user()->name)
                    ." shared {$case->case_number}: {$case->title}",
                'new_case',
                route('clinical-cases.show', $case)
            ));

        AuditLog::record('posted_case', "Posted anonymized clinical case {$case->case_number}", $case);

        return redirect()
            ->route('clinical-cases.show', $case)
            ->with('success', 'Clinical case published. The medical community has been notified.');
    }

    public function show(MedicalCase $clinicalCase): View
    {
        $this->authorizeVisibility($clinicalCase);

        $clinicalCase->load([
            'postedBy.profile',
            'hospital',
            'specialization',
            'followers',
            'discussions' => fn ($query) => $query->topLevel()->with([
                'user.profile',
                'replies.user.profile',
            ])->oldest(),
        ])->loadCount(['discussions', 'followers']);

        $isFollowing = $clinicalCase->followers->contains(Auth::id());
        AuditLog::record('viewed_case', "Viewed clinical case {$clinicalCase->case_number}", $clinicalCase);

        return view('clinical-cases.show', compact('clinicalCase', 'isFollowing'));
    }

    public function toggleFollow(MedicalCase $clinicalCase): RedirectResponse
    {
        $this->authorizeVisibility($clinicalCase);
        abort_if(
            $clinicalCase->posted_by === Auth::id(),
            403,
            'Case authors automatically follow their own discussions.'
        );

        $attached = $clinicalCase->followers()->toggle(Auth::id());
        $following = count($attached['attached']) > 0;

        return back()->with('success', $following
            ? 'You are now following this discussion.'
            : 'You stopped following this discussion.'
        );
    }

    public function resolve(Request $request, MedicalCase $clinicalCase): RedirectResponse
    {
        $this->authorizeVisibility($clinicalCase);

        abort_unless(
            $clinicalCase->posted_by === Auth::id(),
            403
        );

        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'min:10'],
        ]);

        $clinicalCase->update([
            'status' => 'resolved',
            'resolution_notes' => $validated['resolution_notes'],
            'resolved_at' => now(),
        ]);

        AuditLog::record('resolved_case', "Resolved clinical case {$clinicalCase->case_number}", $clinicalCase);

        return back()->with('success', 'Discussion marked as resolved with a clinical summary.');
    }

    public function reopen(MedicalCase $clinicalCase): RedirectResponse
    {
        $this->authorizeVisibility($clinicalCase);

        abort_unless(
            $clinicalCase->posted_by === Auth::id(),
            403
        );

        $clinicalCase->update([
            'status' => $clinicalCase->discussions()->exists() ? 'in_discussion' : 'open',
            'resolved_at' => null,
        ]);

        return back()->with('success', 'Clinical discussion reopened.');
    }

    public function destroy(MedicalCase $clinicalCase): RedirectResponse
    {
        $this->authorizeVisibility($clinicalCase);

        abort_unless(
            $clinicalCase->posted_by === Auth::id(),
            403
        );

        if ($clinicalCase->discussions()->exists()) {
            return back()->with('error', 'A case with contributions cannot be deleted. Resolve it instead.');
        }

        $clinicalCase->delete();

        return redirect()->route('clinical-cases.index')->with('success', 'Clinical case removed.');
    }

    private function ensureClinician(): void
    {
        abort_unless(in_array(Auth::user()->role, ['doctor', 'specialist'], true), 403);
    }

    private function authorizeVisibility(MedicalCase $clinicalCase): void
    {
        abort_unless($clinicalCase->isVisibleTo(Auth::user()), 403);
    }
}
