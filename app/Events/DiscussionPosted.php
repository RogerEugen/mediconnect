<?php

namespace App\Events;

use App\Models\Discussion;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionPosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Discussion $discussion)
    {
        $this->discussion->loadMissing('user');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clinical-case.'.$this->discussion->case_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'discussion.posted';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->discussion->id,
            'case_id' => $this->discussion->case_id,
            'parent_id' => $this->discussion->parent_id,
            'message' => $this->discussion->message,
            'is_expert_opinion' => $this->discussion->is_expert_opinion,
            'created_at' => $this->discussion->created_at->diffForHumans(),
            'user' => [
                'id' => $this->discussion->user->id,
                'name' => $this->discussion->user->name,
                'role' => $this->discussion->user->role,
            ],
        ];
    }
}
