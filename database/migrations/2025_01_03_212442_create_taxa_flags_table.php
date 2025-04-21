<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxa_flags', function (Blueprint $table) {
            $table->id();
            // Kolom untuk FOBI
            $table->unsignedBigInteger('checklist_id')->nullable();
            // Kolom untuk Burungnesia
            $table->unsignedBigInteger('burnes_checklist_id')->nullable();
            // Kolom untuk Kupunesia
            $table->unsignedBigInteger('kupnes_checklist_id')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->enum('flag_type', [
                'identification',
                'location',
                'media',
                'date',
                'other'
            ]);
            $table->text('reason');
            $table->boolean('is_resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key untuk FOBI
            $table->foreign('checklist_id')
                ->references('id')
                ->on('fobi_checklist_taxas')
                ->onDelete('cascade');

            // Foreign key untuk Burungnesia
            $table->foreign('burnes_checklist_id')
                ->references('id')
                ->on('fobi_checklists')
                ->onDelete('cascade');

            // Foreign key untuk Kupunesia
            $table->foreign('kupnes_checklist_id')
                ->references('id')
                ->on('fobi_checklists_kupnes')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('fobi_users')
                ->onDelete('cascade');

            $table->foreign('resolved_by')
                ->references('id')
                ->on('fobi_users')
                ->onDelete('set null');
        });

        // Tambahkan check constraint menggunakan raw SQL
        DB::statement('
            ALTER TABLE taxa_flags
            ADD CONSTRAINT check_only_one_checklist
            CHECK (
                (CASE WHEN checklist_id IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN burnes_checklist_id IS NOT NULL THEN 1 ELSE 0 END +
                CASE WHEN kupnes_checklist_id IS NOT NULL THEN 1 ELSE 0 END) = 1
            )
        ');
    }

    public function down()
    {
        Schema::dropIfExists('taxa_flags');
    }
};
