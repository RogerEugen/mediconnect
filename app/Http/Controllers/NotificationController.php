<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ── Full notifications page ───────────────────────────

    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest('created_at')
            ->paginate(20);

        // Mark all as read when viewing the page
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    // ── Mark single notification as read ─────────────────

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) abort(403);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // ── Mark all as read ──────────────────────────────────

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    // ── Get unread count (AJAX) ───────────────────────────

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // ── Get recent notifications (AJAX dropdown) ──────────

    public function recent()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest('created_at')
            ->take(8)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'url'        => $n->url,
                'color'      => $n->color,
                'is_read'    => $n->is_read,
                'time'       => $n->created_at->diffForHumans(),
            ]);

        return response()->json($notifications);
    }

    // ── Delete single notification ────────────────────────

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) abort(403);

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    // ── Clear all notifications ───────────────────────────

    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return back()->with('success', 'All notifications cleared.');
    }
}
