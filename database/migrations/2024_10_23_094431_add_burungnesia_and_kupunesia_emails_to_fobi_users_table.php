<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBurungnesiaAndKupunesiaEmailsToFobiUsersTable extends Migration
{
    public function up()
    {
        Schema::table('fobi_users', function (Blueprint $table) {
            $table->string('burungnesia_email', 50)->nullable()->after('email');
            $table->string('kupunesia_email', 50)->nullable()->after('burungnesia_email');
        });
    }

    public function down()
    {
        Schema::table('fobi_users', function (Blueprint $table) {
            $table->dropColumn('burungnesia_email');
            $table->dropColumn('kupunesia_email');
        });
    }
}
