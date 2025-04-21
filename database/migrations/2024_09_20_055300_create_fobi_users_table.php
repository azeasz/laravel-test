<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFobiUsersTable extends Migration
{
    public function up()
    {
        Schema::create('fobi_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('fieldguide_id')->nullable();
            $table->string('fname', 20);
            $table->string('lname', 20);
            $table->string('email', 50);
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
            $table->unsignedBigInteger('access_code_id')->nullable();
            $table->unsignedBigInteger('burungnesia_user_id')->nullable(); // New column
            $table->unsignedBigInteger('kupunesia_user_id')->nullable(); // New column
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_users');
    }
}
