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
        Schema::table('taxa_animalia', function (Blueprint $table) {
            $table->id()->first(); // Menambahkan kolom id di awal tabel
        });
    }

    public function down(): void
    {
        Schema::table('taxa_animalia', function (Blueprint $table) {
            $table->dropColumn('id'); // Menghapus kolom id jika rollback
        });
    }
};
