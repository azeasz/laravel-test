<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class IdentificationAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $identification;
    private $checklistId;

    public function __construct($identification, $checklistId)
    {
        $this->identification = $identification;
        $this->checklistId = $checklistId;
    }

    public function broadcastOn()
    {
        return new Channel('checklist.' . $this->checklistId);
    }

    public function broadcastAs()
    {
        return 'IdentificationAdded';
    }

    public function broadcastWith()
    {
        return [
            'identification' => [
                'id' => $this->identification->id,
                'user_id' => $this->identification->user_id,
                'identifier_name' => $this->identification->identifier_name,
                'scientific_name' => $this->identification->scientific_name,
                'class' => $this->identification->class,
                'order' => $this->identification->order,
                'family' => $this->identification->family,
                'genus' => $this->identification->genus,
                'species' => $this->identification->species,
                'identification_level' => $this->identification->identification_level,
                'comment' => $this->identification->comment,
                'photo_url' => $this->identification->photo_url,
                'created_at' => $this->identification->created_at,
                'agreement_count' => 0,
                'user_agreed' => false,
                'user_disagreed' => false,
                'is_first' => $this->identification->is_first ?? false
            ]
        ];
    }
}
