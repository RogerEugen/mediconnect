<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Discussion;
use App\Models\MedicalCase;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClinicalDiscussionController extends Controller
{
    public function store(Request $request, MedicalCase $clinicalCase): RedirectResponse
    {
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

        foreach ($recipientIds as $recipientId) {
            Notification::send(
                $recipientId,
                $discussion->is_expert_opinion ? 'Specialist insight added' : 'New contribution to a case',
                Auth::user()->name." replied to {$clinicalCase->case_number}: {$clinicalCase->title}",
                'new_discussion',
                route('clinical-cases.show', $clinicalCase).'#discussion'
            );
        }

        AuditLog::record('posted_discussion', "Contributed to {$clinicalCase->case_number}", $discussion);

        return redirect(route('clinical-cases.show', $clinicalCase).'#discussion')
            ->with('success', 'Your contribution has been added to the discussion.');
    }

    public function destroy(Discussion $discussion): RedirectResponse
    {
        abort_unless(
            Auth::user()->isAdmin() || $discussion->user_id === Auth::id(),
            403
        );

        if ($discussion->replies()->exists() && ! Auth::user()->isAdmin()) {
            return back()->with('error', 'A contribution with replies cannot be deleted.');
        }

        $discussion->delete();

        return back()->with('success', 'Contribution removed.');
    }
}
