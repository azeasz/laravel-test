<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KupnesUser extends Model
{
    protected $connection = 'third';
    protected $table = 'users'; // Sesuaikan dengan nama tabel di database ketiga
}
