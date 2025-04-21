<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationTokensToFobiUsersTable extends Migration
{
    public function up()
    {
        Schema::table('fobi_users', function (Blueprint $table) {
            $table->string('burungnesia_email_verification_token', 60)->nullable()->after('email_verification_token');
            $table->string('kupunesia_email_verification_token', 60)->nullable()->after('burungnesia_email_verification_token');
            $table->timestamp('burungnesia_email_verified_at')->nullable()->after('email_verified_at');
            $table->timestamp('kupunesia_email_verified_at')->nullable()->after('burungnesia_email_verified_at');
        });
    }

    public function down()
    {
        Schema::table('fobi_users', function (Blueprint $table) {
            $table->dropColumn('burungnesia_email_verification_token');
            $table->dropColumn('kupunesia_email_verification_token');
            $table->dropColumn('burungnesia_email_verified_at');
            $table->dropColumn('kupunesia_email_verified_at');
        });
    }
}
