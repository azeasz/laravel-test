<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationIndexes extends Migration
{
    public function up()
    {
        Schema::table('fobi_checklists', function (Blueprint $table) {
            $table->json('location_details')->nullable()->after('longitude');
            $table->index(['latitude', 'longitude']);
            $table->index(['created_at']);
        });

        Schema::table('fobi_checklists_kupnes', function (Blueprint $table) {
            $table->json('location_details')->nullable()->after('longitude');
            $table->index(['latitude', 'longitude']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::table('fobi_checklists', function (Blueprint $table) {
            $table->dropColumn('location_details');
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('fobi_checklists_kupnes', function (Blueprint $table) {
            $table->dropColumn('location_details');
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['created_at']);
        });
    }
}
