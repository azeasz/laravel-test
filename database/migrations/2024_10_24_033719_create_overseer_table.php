<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOverseerTable extends Migration
{
    public function up()
    {
        Schema::create('overseer', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('fieldguide_id')->nullable();
            $table->string('fname', 20);
            $table->string('lname', 20);
            $table->string('email', 50);
            $table->string('burungnesia_email', 50)->nullable();
            $table->string('kupunesia_email', 50)->nullable();
            $table->string('uname', 50);
            $table->string('password', 74);
            $table->tinyInteger('level');
            $table->string('phone', 14);
            $table->string('organization', 50);
            $table->string('ip_addr', 20);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->smallInteger('is_approved')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('profile_picture', 255)->nullable();
            $table->string('email_verification_token', 60)->nullable();
            $table->timestamp('burungnesia_email_verified_at')->nullable();
            $table->timestamp('kupunesia_email_verified_at')->nullable();
            $table->string('burungnesia_email_verification_token', 60)->nullable();
            $table->string('kupunesia_email_verification_token', 60)->nullable();
            $table->unsignedBigInteger('access_code_id')->nullable();
            $table->unsignedBigInteger('burungnesia_user_id')->nullable();
            $table->unsignedBigInteger('kupunesia_user_id')->nullable();
        });

        // Pindahkan data dari fobi_users ke overseer
        DB::table('overseer')->insertUsing(
            [
                'fieldguide_id', 'fname', 'lname', 'email', 'burungnesia_email',
                'kupunesia_email', 'uname', 'password', 'level', 'phone',
                'organization', 'ip_addr', 'created_at', 'updated_at',
                'deleted_at', 'remember_token', 'is_approved', 'email_verified_at',
                'profile_picture', 'email_verification_token',
                'burungnesia_email_verified_at', 'kupunesia_email_verified_at',
                'burungnesia_email_verification_token',
                'kupunesia_email_verification_token', 'access_code_id',
                'burungnesia_user_id', 'kupunesia_user_id'
            ],
            DB::table('fobi_users')
                ->whereIn('level', [3, 4])
                ->select([
                    'fieldguide_id', 'fname', 'lname', 'email', 'burungnesia_email',
                    'kupunesia_email', 'uname', 'password', 'level', 'phone',
                    'organization', 'ip_addr', 'created_at', 'updated_at',
                    'deleted_at', 'remember_token', 'is_approved', 'email_verified_at',
                    'profile_picture', 'email_verification_token',
                    'burungnesia_email_verified_at', 'kupunesia_email_verified_at',
                    'burungnesia_email_verification_token',
                    'kupunesia_email_verification_token', 'access_code_id',
                    'burungnesia_user_id', 'kupunesia_user_id'
                ])
        );
        }

    public function down()
    {
        Schema::dropIfExists('overseer');
    }
}
