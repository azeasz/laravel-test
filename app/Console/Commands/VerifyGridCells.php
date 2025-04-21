<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyGridCells extends Command
{
    protected $signature = 'grid:verify';
    protected $description = 'Verify generated grid cells';

    public function handle()
    {
        $this->info('Verifying grid cells...');

        // 1. Cek total records
        $total = DB::table('grid_cells')->count();
        $this->info("Total grid cells: {$total}");

        if ($total === 0) {
            $this->error('No grid cells found! Please run grid:from-geojson first.');
            return;
        }

        // 2. Cek distribusi per area type
        $distribution = DB::table('grid_cells')
            ->select('area_type', DB::raw('count(*) as total'))
            ->groupBy('area_type')
            ->get();

        $this->info("\nDistribution by area type:");
        foreach ($distribution as $dist) {
            $this->info("- {$dist->area_type}: {$dist->total} cells");
        }

        // 3. Cek koordinat valid
        $invalidCoords = DB::table('grid_cells')
            ->where('min_lat', '<', -90)
            ->orWhere('max_lat', '>', 90)
            ->orWhere('min_lng', '<', -180)
            ->orWhere('max_lng', '>', 180)
            ->count();

        if ($invalidCoords > 0) {
            $this->error("\nFound {$invalidCoords} cells with invalid coordinates!");
        } else {
            $this->info("\nAll coordinates are valid");
        }

        // 4. Cek grid coverage untuk Indonesia
        $indonesiaCoverage = DB::table('grid_cells')
            ->whereBetween('min_lat', [-11, 6])
            ->whereBetween('min_lng', [95, 141])
            ->count();

        if ($total > 0) {
            $coveragePercent = round(($indonesiaCoverage / $total) * 100, 2);
            $this->info("\nIndonesia coverage: {$coveragePercent}% of total cells");
        }

        // 5. Cek duplikat grid cells
        $duplicates = DB::table('grid_cells')
            ->select('cell_x', 'cell_y', 'area_type', DB::raw('count(*) as count'))
            ->groupBy('cell_x', 'cell_y', 'area_type')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->count() > 0) {
            $this->error("\nFound duplicate grid cells!");
            foreach ($duplicates as $dup) {
                $this->error("- Cell ({$dup->cell_x}, {$dup->cell_y}) type {$dup->area_type}: {$dup->count} duplicates");
            }
        } else {
            $this->info("\nNo duplicate grid cells found");
        }
    }
}
