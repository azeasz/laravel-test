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
        Schema::table('orthoptera_fobi', function (Blueprint $table) {
            $table->increments('id')->first(); // Menambahkan kolom id sebagai primary key di awal tabel
        });
    }

    public function down(): void
    {
        Schema::table('orthoptera_fobi', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
