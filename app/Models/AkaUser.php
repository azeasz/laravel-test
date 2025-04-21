<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkaUser extends Model
{
    protected $connection = 'second';
    protected $table = 'users'; // Sesuaikan dengan nama tabel di database kedua
}
