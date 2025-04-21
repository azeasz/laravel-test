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
        Schema::table('taxa_quality_assessments', function (Blueprint $table) {
            $table->boolean('can_be_improved')->nullable()->after('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxa_quality_assessments', function (Blueprint $table) {
            $table->dropColumn('can_be_improved');
        });
    }
};
