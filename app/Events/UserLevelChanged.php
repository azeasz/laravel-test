<?php

namespace App\Events;

use App\Models\FobiUser;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLevelChanged
{
    use Dispatchable, SerializesModels;

    public $user;

    public function __construct(FobiUser $user)
    {
        $this->user = $user;
    }
}
