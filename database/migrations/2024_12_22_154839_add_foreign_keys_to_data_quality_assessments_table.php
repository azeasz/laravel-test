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
        Schema::table('data_quality_assessments', function (Blueprint $table) {
            $table->foreign(['fauna_id'])->references(['id'])->on('faunas')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['observation_id'])->references(['id'])->on('fobi_checklists')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_quality_assessments', function (Blueprint $table) {
            $table->dropForeign('data_quality_assessments_fauna_id_foreign');
            $table->dropForeign('data_quality_assessments_observation_id_foreign');
        });
    }
};
