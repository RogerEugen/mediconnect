<?php
// app/Http/Controllers/Doctor/DiscussionController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\MedicalCase;
use App\Models\CaseAssignment;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function store(Request $request, MedicalCase $case)
    {
        $validated = $request->validate([
            'message'          => 'required|string|min:5',
            'parent_id'        => 'nullable|exists:discussions,id',
            'is_expert_opinion' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Verify user is allowed to post on this case
        $allowed = false;
        if ($user->role === 'doctor' && $case->posted_by === $user->id) {
            $allowed = true;
        } elseif ($user->role === 'specialist') {
            $allowed = CaseAssignment::where('case_id', $case->id)
                ->where('specialist_id', $user->id)
                ->whereIn('status', ['in_progress', 'accepted'])
                ->exists();
        } elseif ($user->role === 'admin') {
            $allowed = true;
        }

        if (!$allowed) {
            abort(403, 'You are not authorized to post on this case.');
        }

        $discussion = Discussion::create([
            'case_id'           => $case->id,
            'user_id'           => $user->id,
            'parent_id'         => $validated['parent_id'] ?? null,
            'message'           => $validated['message'],
            'is_expert_opinion' => $request->boolean('is_expert_opinion'),
        ]);

        // Update case status to in_discussion if still at assigned
        if ($case->status === 'assigned') {
            $case->update(['status' => 'in_discussion']);
        }

        // Notifications based on who posted
        if ($user->role === 'specialist') {

            // Notify the posting doctor
            Notification::send(
                $case->posted_by,
                $discussion->is_expert_opinion
                    ? '★ Expert opinion posted on your case'
                    : 'New reply on your case from specialist',
                "Dr. {$user->name} posted a " .
                    ($discussion->is_expert_opinion ? 'formal expert opinion' : 'reply') .
                    " on case {$case->case_number}.",
                'new_discussion'
            );

            // Notify admins
            \App\Models\User::where('role', 'admin')->where('is_active', true)
                ->each(fn($a) => Notification::send(
                    $a->id,
                    'Specialist posted on case ' . $case->case_number,
                    "Dr. {$user->name} posted a message on case {$case->case_number}.",
                    'new_discussion'
                ));

        } elseif ($user->role === 'doctor') {

            // Notify the assigned specialist
            $assignment = CaseAssignment::where('case_id', $case->id)
                ->whereIn('status', ['in_progress', 'accepted'])
                ->latest()
                ->first();

            if ($assignment) {
                Notification::send(
                    $assignment->specialist_id,
                    'Doctor replied on case ' . $case->case_number,
                    "Dr. {$user->name} posted a reply on case {$case->case_number}.",
                    'new_discussion'
                );
            }
        }

        AuditLog::record(
            'posted_discussion',
            "Posted message on case {$case->case_number}" .
                ($discussion->is_expert_opinion ? ' (expert opinion)' : ''),
            $discussion
        );

        $redirect = $user->role === 'specialist'
            ? route('specialist.cases.show', $case)
            : route('doctor.cases.show', $case);

        return redirect($redirect . '#discussion')
            ->with('success', $discussion->is_expert_opinion
                ? 'Expert opinion posted successfully.'
                : 'Reply posted successfully.'
            );
    }

    public function destroy(Discussion $discussion)
    {
        if ($discussion->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow delete if no replies
        if ($discussion->replies()->count() > 0) {
            return back()->with('error', 'Cannot delete a message that has replies.');
        }

        $discussion->delete();

        return back()->with('success', 'Message deleted.');
    }
}