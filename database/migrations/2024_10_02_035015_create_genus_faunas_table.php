<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_xxxxxx_create_genus_faunas_table.php
   public function up()
   {
       Schema::create('genus_faunas', function (Blueprint $table) {
        $table->increments('id');
        $table->string('genus');
        $table->string('nameId');
        $table->string('nameLat');
        $table->unsignedInteger('fauna_id')->index('genus_faunas_fauna_id_foreign');
        $table->timestamp('created_at')->nullable()->useCurrent();
        $table->timestamp('updated_at')->nullable();
        $table->softDeletes();
       });
   }

   public function down()
   {
       Schema::dropIfExists('genus_faunas');

   }
};
