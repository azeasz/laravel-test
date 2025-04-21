<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FobiChecklistM extends Model
{
    use SoftDeletes;

    protected $table = 'checklists';

    protected $fillable = [
        'user_id', 'latitude', 'longitude', 'observer', 'additional_note',
        'active', 'tgl_pengamatan', 'start_time', 'end_time',
        'tujuan_pengamatan', 'completed', 'can_edit'
    ];
    public function user()
   {
       return $this->belongsTo(User::class);
   }
   public function fobiUser()
   {
       return $this->belongsTo(FobiUser::class);
   }
}
