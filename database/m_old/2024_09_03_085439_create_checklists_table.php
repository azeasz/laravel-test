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
        Schema::create('checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->double('latitude', 11, 3);
            $table->double('longitude', 11, 3);
            $table->text('observer')->nullable();
            $table->text('additional_note')->nullable();
            $table->integer('active')->default(1);
            $table->date('tgl_pengamatan')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->smallInteger('tujuan_pengamatan')->default(0);
            $table->boolean('completed')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
