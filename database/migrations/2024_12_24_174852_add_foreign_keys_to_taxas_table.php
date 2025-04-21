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
        Schema::table('taxas', function (Blueprint $table) {
            $table->foreign(['created_by'], 'taxa_created_by_foreign')->references(['id'])->on('fobi_users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['updated_by'], 'taxa_updated_by_foreign')->references(['id'])->on('fobi_users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxas', function (Blueprint $table) {
            $table->dropForeign('taxa_created_by_foreign');
            $table->dropForeign('taxa_updated_by_foreign');
        });
    }
};
