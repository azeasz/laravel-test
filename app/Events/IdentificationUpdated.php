<?php

// app/Events/IdentificationUpdated.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class IdentificationUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $identification;

    public function __construct($identification)
    {
        $this->identification = $identification;
    }

    public function broadcastOn()
    {
        return new Channel('checklist');
    }
}
