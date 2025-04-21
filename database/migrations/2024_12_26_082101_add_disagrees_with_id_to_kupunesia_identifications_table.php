<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kupunesia_identifications', function (Blueprint $table) {
            $table->unsignedBigInteger('disagrees_with_id')->nullable()->after('agrees_with_id');

            // Tambahkan foreign key jika diperlukan
            $table->foreign('disagrees_with_id')
                  ->references('id')
                  ->on('kupunesia_identifications')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('kupunesia_identifications', function (Blueprint $table) {
            $table->dropForeign(['disagrees_with_id']);
            $table->dropColumn('disagrees_with_id');
        });
    }
};
