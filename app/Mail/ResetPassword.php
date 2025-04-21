<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\FobiUser;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    private $frontendUrl;

    public function __construct(FobiUser $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->frontendUrl = 'https://talinara.com'; // URL langsung tanpa env
    }

    public function build()
    {
        $resetUrl = $this->frontendUrl . '/reset-password?token=' . $this->token . '&email=' . $this->user->email;

        return $this->view('emails.reset-password')
                    ->subject('Reset Password FOBI')
                    ->with([
                        'resetUrl' => $resetUrl,
                        'user' => $this->user
                    ]);
    }
}
