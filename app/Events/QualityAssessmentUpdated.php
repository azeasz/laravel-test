<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QualityAssessmentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $assessment;
    public $checklistId;

    public function __construct($assessment)
    {
        $this->assessment = $assessment;
        $this->checklistId = $assessment->taxa_id;
    }

    public function broadcastOn()
    {
        return new Channel('checklist.' . $this->checklistId);
    }

    public function broadcastAs()
    {
        return 'QualityAssessmentUpdated';
    }

    public function broadcastWith()
    {
        return [
            'grade' => $this->assessment->grade,
            'agreement_count' => $this->assessment->agreement_count,
            'curator_verified' => $this->assessment->curator_verified,
            'community_id_level' => $this->assessment->community_id_level
        ];
    }
}
