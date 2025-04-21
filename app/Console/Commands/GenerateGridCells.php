<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GridSystemService;

class GenerateGridCells extends Command
{
    protected $signature = 'grid:generate {area_type}';
    protected $description = 'Generate grid cells for specified area type';

    public function handle()
    {
        $areaType = $this->argument('area_type');
        $gridService = new GridSystemService();

        // Ambil data boundaries dari database atau file
        $boundaries = \DB::table('area_boundaries')
            ->where('type', $areaType)
            ->get();

        $gridService->generateGridForArea($areaType, $boundaries);

        $this->info("Grid cells generated for {$areaType}");
    }
}
