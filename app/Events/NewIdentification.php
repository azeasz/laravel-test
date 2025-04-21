<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewIdentification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $identification;

    public function __construct($identification)
    {
        $this->identification = $identification;
    }

    public function broadcastOn()
    {
        return new Channel('observations.' . $this->identification->observation_id);
    }

    public function broadcastAs()
    {
        return 'new-identification';
    }
}
