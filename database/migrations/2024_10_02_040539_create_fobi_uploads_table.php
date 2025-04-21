<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('fobi_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'burungnesia', 'kupunesia', 'media'
            $table->string('location')->nullable();
            $table->date('date')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->string('activity')->nullable();
            $table->string('habitat')->nullable();
            $table->string('other_observers')->nullable();
            $table->text('description')->nullable();
            $table->string('scientific_name')->nullable();
            $table->string('media_path')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_identified')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_uploads');
    }
};
