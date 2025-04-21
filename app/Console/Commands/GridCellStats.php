<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GridCellStats extends Command
{
    protected $signature = 'grid:stats';
    protected $description = 'Show statistics of generated grid cells';

    public function handle()
    {
        $stats = DB::table('grid_cells')
            ->select('area_name', DB::raw('count(*) as total'))
            ->groupBy('area_name')
            ->orderBy('total', 'desc')
            ->get();

        $this->info("\nGrid cells per province:");
        $this->table(
            ['Province', 'Total Cells'],
            $stats->map(fn($item) => [$item->area_name, $item->total])
        );

        $total = $stats->sum('total');
        $this->info("\nTotal grid cells: " . $total);
    }
}
