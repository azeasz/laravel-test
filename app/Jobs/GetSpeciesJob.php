<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GetSpeciesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $checklist_id;

    public function __construct($checklist_id)
    {
        $this->checklist_id = $checklist_id;
    }

    public function handle()
    {
        try {
            $burungnesiaSpecies = DB::connection('second')->table('checklist_fauna')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->join('checklists', 'checklist_fauna.checklist_id', '=', 'checklists.id')
                ->where('checklist_fauna.checklist_id', $this->checklist_id)
                ->select('faunas.nameLat', 'checklists.latitude', 'checklists.longitude')
                ->get();

            $kupunesiaSpecies = DB::connection('third')->table('checklist_fauna')
                ->join('faunas', 'checklist_fauna.fauna_id', '=', 'faunas.id')
                ->join('checklists', 'checklist_fauna.checklist_id', '=', 'checklists.id')
                ->where('checklist_fauna.checklist_id', $this->checklist_id)
                ->select('faunas.nameLat', 'checklists.latitude', 'checklists.longitude')
                ->get();

            // Simpan atau proses data yang diambil sesuai kebutuhan
        } catch (\Exception $e) {
            // Tangani kesalahan
        }
    }
}
