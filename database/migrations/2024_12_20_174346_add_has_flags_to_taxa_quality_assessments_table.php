<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('taxa_quality_assessments', function (Blueprint $table) {
            $table->boolean('has_flags')->default(false)->after('grade');
        });
    }

    public function down()
    {
        Schema::table('taxa_quality_assessments', function (Blueprint $table) {
            $table->dropColumn('has_flags');
        });
    }
};
