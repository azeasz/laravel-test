<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\FobiUser;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tokenType;
    private $frontendUrl;
    public $verificationUrl;

    public function __construct(FobiUser $user, $tokenType)
    {
        $this->user = $user;
        $this->tokenType = $tokenType;
        $this->frontendUrl = 'https://talinara.com';

        try {
            $token = $this->getToken();
            $this->verificationUrl = $this->frontendUrl . '/verify-email/' . $token . '/' . $this->tokenType;

            \Log::info('Email verification details:', [
                'user_id' => $this->user->id,
                'token_type' => $this->tokenType,
                'token' => $token,
                'frontend_url' => $this->frontendUrl,
                'verification_url' => $this->verificationUrl
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in VerifyEmail constructor:', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'token_type' => $tokenType
            ]);
            throw $e;
        }
    }

    public function build()
    {
        try {
            \Log::info('Building verification email', [
                'user_id' => $this->user->id,
                'token_type' => $this->tokenType,
                'verification_url' => $this->verificationUrl
            ]);

            return $this->view('emails.verify')
                       ->subject('Verifikasi Email FOBI')
                       ->with([
                           'user' => $this->user,
                           'verificationUrl' => $this->verificationUrl,
                           'tokenType' => $this->tokenType
                       ]);
        } catch (\Exception $e) {
            \Log::error('Error building verification email:', [
                'error' => $e->getMessage(),
                'user_id' => $this->user->id,
                'token_type' => $this->tokenType,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getToken()
    {
        try {
            $token = match ($this->tokenType) {
                'email_verification_token' => $this->user->email_verification_token,
                'burungnesia_email_verification_token' => $this->user->burungnesia_email_verification_token,
                'kupunesia_email_verification_token' => $this->user->kupunesia_email_verification_token,
                default => throw new \Exception("Invalid token type: {$this->tokenType}")
            };

            if (empty($token)) {
                throw new \Exception("Token is empty for type: {$this->tokenType}");
            }

            return $token;
        } catch (\Exception $e) {
            \Log::error('Error getting token:', [
                'error' => $e->getMessage(),
                'user_id' => $this->user->id,
                'token_type' => $this->tokenType
            ]);
            throw $e;
        }
    }
}
