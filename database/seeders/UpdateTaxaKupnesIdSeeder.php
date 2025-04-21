<?php

namespace Database\Seeders;

use App\Models\FobiUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateTaxaKupnesIdSeeder extends Seeder
{
    public function run()
    {
        // Cek admin user
        $adminUser = FobiUser::first();
        if (!$adminUser) {
            $this->command->error('No admin user found! Please create a user first.');
            return;
        }

        try {
            DB::beginTransaction();

            // Ambil semua taxa yang belum punya kupnes_fauna_id dan hanya order Lepidoptera
            $taxas = DB::table('taxas')
                ->whereNull('kupnes_fauna_id')
                ->where('order', 'Lepidoptera')
                ->get();

            $updated = 0;
            $total = $taxas->count();

            $this->command->info("Memulai update untuk $total records Lepidoptera...");

            foreach ($taxas as $taxa) {
                // Cari fauna yang sesuai
                $kupnesFauna = DB::table('faunas_kupnes')
                    ->where('nameLat', $taxa->species)
                    ->first();

                // Hanya update jika fauna ditemukan
                if ($kupnesFauna) {
                    DB::table('taxas')
                        ->where('id', $taxa->id)
                        ->update([
                            'kupnes_fauna_id' => $kupnesFauna->id,
                            'updated_at' => now()
                        ]);

                    $updated++;

                    if ($updated % 100 === 0) {
                        $this->command->info("Updated $updated of $total records...");
                    }
                }
            }

            DB::commit();
            $this->command->info("Update selesai: $updated data Lepidoptera berhasil diupdate");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}
