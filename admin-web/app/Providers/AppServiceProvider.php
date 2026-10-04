<?php

namespace App\Providers;

use App\Services\Residentes\ResidentesApi;
use App\Services\Residentes\SpringResidentesApi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ResidentesApi::class, fn () => new SpringResidentesApi(
            config('services.residentes.url'),
            (int) config('services.residentes.timeout'),
        ));
    }

    public function boot(): void
    {
        //
    }
}