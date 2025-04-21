<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('taxa_identifications', function (Blueprint $table) {
            $table->unsignedBigInteger('curator_id')->nullable()->after('user_id');
            $table->boolean('is_curator_decision')->default(false)->after('is_agreed');
            $table->timestamp('curator_reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->foreign('curator_id')->references('id')->on('fobi_users');
        });
    }

    public function down()
    {
        Schema::table('taxa_identifications', function (Blueprint $table) {
            $table->dropForeign(['curator_id']);
            $table->dropColumn([
                'curator_id',
                'is_curator_decision',
                'curator_reviewed_at',
                'review_notes'
            ]);
        });
    }
};
