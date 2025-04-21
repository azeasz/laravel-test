<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteTaxasTable extends Migration
{
    public function up()
    {
        Schema::create('favorite_taxas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fobi_user_id'); // Tambahkan kolom fobi_user_id
            $table->string('taxa');
            $table->timestamps();

            // Tambahkan foreign key constraint
            $table->foreign('fobi_user_id')->references('id')->on('fobi_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('favorite_taxas', function (Blueprint $table) {
            // Hapus foreign key constraint sebelum menghapus tabel
            $table->dropForeign(['fobi_user_id']);
        });

        Schema::dropIfExists('favorite_taxas');
    }
}
