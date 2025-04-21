<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class IdentificationVerified extends Notification implements ShouldQueue
{
    use Queueable;

    protected $checklist;
    protected $verifiedBy;
    protected $isApproved;

    public function __construct($checklist, $verifiedBy, $isApproved)
    {
        $this->checklist = $checklist;
        $this->verifiedBy = $verifiedBy;
        $this->isApproved = $isApproved;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'checklist_id' => $this->checklist->id,
            'message' => $this->isApproved
                ? 'Identifikasi Anda telah diverifikasi oleh kurator'
                : 'Identifikasi Anda memerlukan peninjauan ulang dari kurator',
            'verified_by' => $this->verifiedBy->id,
            'verified_at' => now(),
            'is_approved' => $this->isApproved
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'checklist_id' => $this->checklist->id,
            'message' => $this->isApproved
                ? 'Identifikasi Anda telah diverifikasi oleh kurator'
                : 'Identifikasi Anda memerlukan peninjauan ulang dari kurator',
            'verified_by' => $this->verifiedBy->id,
            'verified_at' => now()->toISOString(),
            'is_approved' => $this->isApproved
        ]);
    }
}
