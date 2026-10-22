<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Pagination bergaya Bootstrap (tampilan memakai Bootstrap 5)
        Paginator::useBootstrapFive();

        // @rupiah($angka)  =>  Rp 1.250.000
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float) ({$expression}), 0, ',', '.'); ?>";
        });
    }
}
