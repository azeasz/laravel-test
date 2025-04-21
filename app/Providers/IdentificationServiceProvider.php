<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Identification\FobiIdentificationService;
use App\Services\Identification\BirdIdentificationService;
use App\Services\Identification\ButterflyIdentificationService;

class IdentificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('identification.service', function ($app) {
            $source = request()->header('X-Source', 'fobi');

            return match($source) {
                'burungnesia' => new BirdIdentificationService(),
                'kupunesia' => new ButterflyIdentificationService(),
                default => new FobiIdentificationService(),
            };
        });
    }

    public function boot()
    {
        //
    }
}
