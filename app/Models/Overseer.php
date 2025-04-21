<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Overseer extends Model
{
    use SoftDeletes;

    protected $table = 'overseer';

    protected $fillable = [
        'region_id',
        'fieldguide_id',
        'fname',
        'lname',
        'email',
        'burungnesia_email',
        'kupunesia_email',
        'uname',
        'password',
        'level',
        'phone',
        'organization',
        'ip_addr',
        'is_approved',
        'profile_picture',
        'email_verification_token',
        'burungnesia_email_verification_token',
        'kupunesia_email_verification_token',
        'access_code_id',
        'burungnesia_user_id',
        'kupunesia_user_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
        'burungnesia_email_verified_at',
        'kupunesia_email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
        'burungnesia_email_verification_token',
        'kupunesia_email_verification_token'
    ];

    // Relasi dengan Region
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // Accessor untuk nama lengkap
    public function getFullNameAttribute()
    {
        return "{$this->fname} {$this->lname}";
    }
}
