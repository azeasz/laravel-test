<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fieldguide_id')->nullable()->index();
            $table->string('fname', 20);
            $table->string('lname', 20);
            $table->string('email', 50);
            $table->string('uname', 50)->unique();
            $table->string('password', 74);
            $table->tinyInteger('level');
            $table->string('phone', 14);
            $table->string('organization', 50);
            $table->string('ip_addr', 20);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->rememberToken();
            $table->smallInteger('is_approved')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('email_verification_token', 60)->nullable();
            $table->unsignedBigInteger('access_code_id')->nullable()->index('users_access_code_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
