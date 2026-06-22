<?php

// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ── Full notifications page ───────────────────────────

    public function index()
    {
        $notifications = $this->visibleNotifications()
            ->latest('created_at')
            ->paginate(20);

        // Mark all as read when viewing the page
        $this->visibleNotifications()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    // ── Mark single notification as read ─────────────────

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        abort_if($this->isClinicalNotificationHiddenFromAdmin($notification), 404);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // ── Mark all as read ──────────────────────────────────

    public function markAllRead()
    {
        $this->visibleNotifications()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    // ── Get unread count (AJAX) ───────────────────────────

    public function unreadCount()
    {
        $count = $this->visibleNotifications()
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // ── Get recent notifications (AJAX dropdown) ──────────

    public function recent()
    {
        $notifications = $this->visibleNotifications()
            ->latest('created_at')
            ->take(8)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'url' => $n->url,
                'color' => $n->color,
                'is_read' => $n->is_read,
                'time' => $n->created_at->diffForHumans(),
                'created_at_iso' => $n->created_at->toIso8601String(),
            ]);

        return response()->json($notifications);
    }

    // ── Delete single notification ────────────────────────

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        abort_if($this->isClinicalNotificationHiddenFromAdmin($notification), 404);

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    // ── Clear all notifications ───────────────────────────

    public function clearAll()
    {
        $this->visibleNotifications()->delete();

        return back()->with('success', 'All notifications cleared.');
    }

    private function visibleNotifications(): Builder
    {
        $query = Notification::query()->where('user_id', Auth::id());

        if (Auth::user()->isAdmin()) {
            $query->whereNotIn('type', $this->clinicalNotificationTypes());
        }

        return $query;
    }

    private function isClinicalNotificationHiddenFromAdmin(Notification $notification): bool
    {
        return Auth::user()->isAdmin()
            && in_array($notification->type, $this->clinicalNotificationTypes(), true);
    }

    private function clinicalNotificationTypes(): array
    {
        return [
            'new_case',
            'new_discussion',
            'case_assigned',
            'specialist_assigned',
            'case_accepted',
            'case_declined',
            'case_completed',
            'case_resolved',
        ];
    }
}
