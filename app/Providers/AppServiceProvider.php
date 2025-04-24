<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');

        // Set timezone
        date_default_timezone_set(config('app.timezone'));
    }
}
