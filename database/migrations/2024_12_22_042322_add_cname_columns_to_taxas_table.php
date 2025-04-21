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
        Schema::table('taxas', function (Blueprint $table) {
            // Tambahkan kolom Cname_two sampai Cname_ten setelah cname_species
            $table->text('Cname')->nullable()->after('cname_species');
            $table->text('Cname_two')->nullable()->after('Cname');
            $table->text('Cname_three')->nullable()->after('Cname_two');
            $table->text('Cname_four')->nullable()->after('Cname_three');
            $table->text('Cname_five')->nullable()->after('Cname_four');
            $table->text('Cname_six')->nullable()->after('Cname_five');
            $table->text('Cname_seven')->nullable()->after('Cname_six');
            $table->text('Cname_eight')->nullable()->after('Cname_seven');
            $table->text('Cname_nine')->nullable()->after('Cname_eight');
            $table->text('Cname_ten')->nullable()->after('Cname_nine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxas', function (Blueprint $table) {
            $table->dropColumn([
                'Cname_two',
                'Cname_three',
                'Cname_four',
                'Cname_five',
                'Cname_six',
                'Cname_seven',
                'Cname_eight',
                'Cname_nine',
                'Cname_ten'
            ]);
        });
    }
};
