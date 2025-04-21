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
        Schema::create('setting_members', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->text('contribution')->nullable();
            $table->string('link')->nullable();
            $table->integer('type')->default(0)->comment('1->photographers;3->member;2->writers');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_members');
    }
};
