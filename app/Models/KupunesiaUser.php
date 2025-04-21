<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KupunesiaUser extends Model
{
    protected $connection = 'third';
    protected $table = 'users';
}
