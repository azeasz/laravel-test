<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('discussion_id')->nullable()->index('comments_discussion_id_foreign');
            $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
        });

        Schema::table('suggestions', function (Blueprint $table) {
            $table->unsignedBigInteger('discussion_id')->nullable()->index('suggestions_discussion_id_foreign');
            $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
        });

        Schema::table('identifications', function (Blueprint $table) {
            $table->unsignedBigInteger('discussion_id')->nullable()->index('identifications_discussion_id_foreign');
            $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['discussion_id']);
            $table->dropColumn('discussion_id');
        });

        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropForeign(['discussion_id']);
            $table->dropColumn('discussion_id');
        });

        Schema::table('identifications', function (Blueprint $table) {
            $table->dropForeign(['discussion_id']);
            $table->dropColumn('discussion_id');
        });
    }
};
