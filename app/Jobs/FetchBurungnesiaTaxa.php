<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FetchBurungnesiaTaxa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            $allData = [];
            $page = 1;
            $hasMoreData = true;

            while ($hasMoreData) {
                $response = Http::get('https://burungnesia.org/api/checklist', [
                    'page' => $page
                ]);

                $data = $response->json();
                if (empty($data['data'])) {
                    $hasMoreData = false;
                } else {
                    $allData = array_merge($allData, $data['data']);
                    $page++;
                }

                usleep(100000);
            }

            Cache::put('burungnesia_all_data', $allData, now()->addHours(24));
            Cache::put('burungnesia_last_updated', now(), now()->addHours(24));

        } catch (\Exception $e) {
            \Log::error('Burungnesia API Error: ' . $e->getMessage());
        }
    }
}
