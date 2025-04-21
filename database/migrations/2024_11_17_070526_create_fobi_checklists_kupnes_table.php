<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFobiChecklistsKupnesTable extends Migration
{
    public function up()
    {
        Schema::create('fobi_checklists_kupnes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fobi_user_id');
            $table->double('latitude', 11, 3);
            $table->double('longitude', 11, 3);
            $table->text('observer')->nullable();
            $table->text('additional_note')->nullable();
            $table->integer('active')->default(1);
            $table->date('tgl_pengamatan')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->smallInteger('tujuan_pengamatan');
            $table->tinyInteger('completed')->default(0);
            $table->tinyInteger('can_edit')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fobi_user_id')->references('id')->on('fobi_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fobi_checklists_kupnes');
    }
}
