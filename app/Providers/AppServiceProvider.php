<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Blade::directive('sourceColor', function ($source) {
            return "<?php 
                switch($source) {
                    case 'fobi':
                        echo 'primary';
                        break;
                    case 'burungnesia':
                        echo 'success';
                        break;
                    case 'kupunesia':
                        echo 'warning';
                        break;
                    default:
                        echo 'secondary';
                        break;
                }
            ?>";
        });
    }
}
