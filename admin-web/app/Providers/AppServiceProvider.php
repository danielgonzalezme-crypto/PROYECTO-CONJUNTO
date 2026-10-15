<?php

namespace App\Providers;

use App\Services\Propietarios\NodePropietariosApi;
use App\Services\Propietarios\PropietariosApi;
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

        $this->app->bind(PropietariosApi::class, fn () => new NodePropietariosApi(
            config('services.propietarios.url'),
            (int) config('services.propietarios.timeout'),
        ));
    }

    public function boot(): void
    {
        //
    }
}
