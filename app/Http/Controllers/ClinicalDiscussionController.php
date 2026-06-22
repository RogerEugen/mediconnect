<?php

namespace App\Http\Controllers;

use App\Events\DiscussionPosted;
use App\Models\AuditLog;
use App\Models\Discussion;
use App\Models\MedicalCase;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClinicalDiscussionController extends Controller
{
    public function sync(Request $request, MedicalCase $clinicalCase): JsonResponse
    {
        $this->authorizeVisibility($clinicalCase);

        $after = max(0, $request->integer('after'));

        $discussions = $clinicalCase->discussions()
            ->with('user')
            ->where('id', '>', $after)
            ->oldest('id')
            ->get()
            ->map(fn (Discussion $discussion) => $this->payload($discussion));

        return response()->json([
            'discussions' => $discussions,
            'latest_id' => $clinicalCase->discussions()->max('id') ?? $after,
            'count' => $clinicalCase->discussions()->count(),
        ]);
    }

    public function store(Request $request, MedicalCase $clinicalCase): RedirectResponse|JsonResponse
    {
        $this->authorizeVisibility($clinicalCase);
        abort_unless(in_array(Auth::user()->role, ['doctor', 'specialist'], true), 403);
        abort_if($clinicalCase->status === 'closed', 403, 'This discussion is closed.');

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:10000'],
            'parent_id' => [
                'nullable',
                Rule::exists('discussions', 'id')->where('case_id', $clinicalCase->id),
            ],
            'is_expert_opinion' => ['nullable', 'boolean'],
        ]);

        $discussion = Discussion::create([
            'case_id' => $clinicalCase->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'message' => $validated['message'],
            'is_expert_opinion' => Auth::user()->isSpecialist() && $request->boolean('is_expert_opinion'),
        ]);
        $discussion->load('user');

        if ($clinicalCase->status === 'open') {
            $clinicalCase->update(['status' => 'in_discussion']);
        } else {
            $clinicalCase->touch();
        }

        $recipientIds = $clinicalCase->followers()->pluck('users.id')
            ->merge($clinicalCase->discussions()->pluck('user_id'))
            ->push($clinicalCase->posted_by)
            ->unique()
            ->reject(fn ($id) => $id === Auth::id());

        $recipients = User::whereKey($recipientIds)->get()
            ->filter(fn (User $user) => $clinicalCase->isVisibleTo($user));

        foreach ($recipients as $recipient) {
            Notification::send(
                $recipient->id,
                $discussion->is_expert_opinion ? 'Specialist insight added' : 'New contribution to a case',
                Auth::user()->name." replied to {$clinicalCase->case_number}: {$clinicalCase->title}",
                'new_discussion',
                route('clinical-cases.show', $clinicalCase).'#discussion'
            );
        }

        AuditLog::record('posted_discussion', "Contributed to {$clinicalCase->case_number}", $discussion);
        broadcast(new DiscussionPosted($discussion));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your contribution has been added to the discussion.',
                'discussion' => $this->payload($discussion),
            ], 201);
        }

        return redirect(route('clinical-cases.show', $clinicalCase).'#discussion')
            ->with('success', 'Your contribution has been added to the discussion.');
    }

    public function destroy(Discussion $discussion): RedirectResponse
    {
        $this->authorizeVisibility($discussion->case);

        abort_unless(
            $discussion->user_id === Auth::id(),
            403
        );

        if ($discussion->replies()->exists()) {
            return back()->with('error', 'A contribution with replies cannot be deleted.');
        }

        $discussion->delete();

        return back()->with('success', 'Contribution removed.');
    }

    private function payload(Discussion $discussion): array
    {
        $discussion->loadMissing('user');

        return [
            'id' => $discussion->id,
            'case_id' => $discussion->case_id,
            'parent_id' => $discussion->parent_id,
            'message' => $discussion->message,
            'is_expert_opinion' => $discussion->is_expert_opinion,
            'created_at' => $discussion->created_at->diffForHumans(),
            'user' => [
                'id' => $discussion->user->id,
                'name' => $discussion->user->name,
                'role' => $discussion->user->role,
            ],
        ];
    }

    private function authorizeVisibility(MedicalCase $clinicalCase): void
    {
        abort_unless($clinicalCase->isVisibleTo(Auth::user()), 403);
    }
}
