<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    private $checklistId;

    public function __construct($comment, $checklistId)
    {
        $this->comment = $comment;
        $this->checklistId = $checklistId;
    }

    public function broadcastOn()
    {
        // Menggunakan format channel yang sama dengan frontend
        return new Channel('checklist.' . $this->checklistId);
    }

    public function broadcastAs()
    {
        return 'CommentAdded';
    }

    public function broadcastWith()
    {
        return [
            'comment' => [
                'id' => $this->comment->id,
                'comment' => $this->comment->comment,
                'user_name' => $this->comment->user_name,
                'created_at' => $this->comment->created_at,
                'user_id' => $this->comment->user_id,
                'checklist_id' => $this->checklistId
            ]
        ];
    }
}
