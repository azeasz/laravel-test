<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CuratorVerificationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $checklistId;
    public $data;

    public function __construct($checklistId, $data)
    {
        $this->checklistId = $checklistId;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new Channel('checklist.' . $this->checklistId);
    }

    public function broadcastAs()
    {
        return 'CuratorVerificationUpdated';
    }

    public function broadcastWith()
    {
        return $this->data;
    }
}
