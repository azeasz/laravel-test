<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Events\UserLevelChanged;
use Tymon\JWTAuth\Contracts\JWTSubject;

class FobiUser extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id', 'fieldguide_id', 'fname', 'lname', 'email', 'burungnesia_email', 'kupunesia_email', 'uname', 'password', 'level', 'phone', 'organization', 'ip_addr', 'remember_token', 'is_approved', 'email_verified_at', 'profile_picture', 'email_verification_token', 'burungnesia_email_verified_at', 'kupunesia_email_verified_at', 'burungnesia_email_verification_token', 'kupunesia_email_verification_token', 'access_code_id', 'burungnesia_user_id', 'kupunesia_user_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function favoriteTaxa()
{
    return $this->hasMany(FavoriteTaxa::class);
}
public function fauna()
{
    return $this->belongsTo(Fauna::class, 'fauna_id'); // Sesuaikan dengan nama kolom yang benar
}
public function comments()
{
    return $this->hasMany(Comment::class);
}
public function follow()
{
    return $this->belongsTo(UserFollow::class);
}
public function identification()
{
    return $this->belongsTo(Identification::class);
}
public function save(array $options = [])
{
    if ($this->isDirty('level')) {
        event(new UserLevelChanged($this));
    }

    return parent::save($options);
}
public function overseer()
{
    return $this->belongsTo(Overseer::class);
}
public function checklistTaxas()
    {
        return $this->hasMany(FobiChecklistTaxa::class, 'user_id');
    }
}
