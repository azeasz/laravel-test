<?php

namespace App\Listeners;

use App\Events\UserLevelChanged;
use Illuminate\Support\Facades\DB;

class UpdateOverseerTable
{
    public function handle(UserLevelChanged $event)
    {
        if (in_array($event->user->level, [3, 4])) {
            DB::table('overseer')->updateOrInsert(
                ['email' => $event->user->email],
                [
                    'fieldguide_id' => $event->user->fieldguide_id,
                    'fname' => $event->user->fname,
                    'lname' => $event->user->lname,
                    'email' => $event->user->email,
                    'burungnesia_email' => $event->user->burungnesia_email,
                    'kupunesia_email' => $event->user->kupunesia_email,
                    'uname' => $event->user->uname,
                    'password' => $event->user->password,
                    'level' => $event->user->level,
                    'phone' => $event->user->phone,
                    'organization' => $event->user->organization,
                    'ip_addr' => $event->user->ip_addr,
                    'created_at' => $event->user->created_at,
                    'updated_at' => $event->user->updated_at,
                    'deleted_at' => $event->user->deleted_at,
                    'remember_token' => $event->user->remember_token,
                    'is_approved' => $event->user->is_approved,
                    'email_verified_at' => $event->user->email_verified_at,
                    'profile_picture' => $event->user->profile_picture,
                    'email_verification_token' => $event->user->email_verification_token,
                    'burungnesia_email_verified_at' => $event->user->burungnesia_email_verified_at,
                    'kupunesia_email_verified_at' => $event->user->kupunesia_email_verified_at,
                    'burungnesia_email_verification_token' => $event->user->burungnesia_email_verification_token,
                    'kupunesia_email_verification_token' => $event->user->kupunesia_email_verification_token,
                    'access_code_id' => $event->user->access_code_id,
                    'burungnesia_user_id' => $event->user->burungnesia_user_id,
                    'kupunesia_user_id' => $event->user->kupunesia_user_id
                ]
            );
        }
    }
}
