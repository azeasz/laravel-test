<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('fobi_uploads', function (Blueprint $table) {
            $table->unsignedInteger('fauna_id')->nullable()->after('is_identified');

            // Jika Anda ingin menambahkan foreign key constraint
            // $table->foreign('fauna_id')->references('id')->on('faunas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('fobi_uploads', function (Blueprint $table) {
            $table->dropForeign(['fauna_id']);
            $table->dropColumn('fauna_id');
        });
    }
};
