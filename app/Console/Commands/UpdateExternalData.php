<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchBurungnesiaTaxa;
use App\Jobs\FetchKupunesiaTaxa;

class UpdateExternalData extends Command
{
    protected $signature = 'data:update-external';
    protected $description = 'Update data from external APIs';

    public function handle()
    {
        FetchBurungnesiaTaxa::dispatch();
        FetchKupunesiaTaxa::dispatch();

        $this->info('Update jobs dispatched successfully');
    }
}
