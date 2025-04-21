<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBioToFobiUsersTable extends Migration
{
    public function up()
    {
        Schema::table('fobi_users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('profile_picture');
        });
    }

    public function down()
    {
        Schema::table('fobi_users', function (Blueprint $table) {
            $table->dropColumn('bio');
        });
    }
}
