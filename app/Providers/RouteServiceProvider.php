<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Caminho padrão para o seu namespace de controladores.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Registre os serviços de rotas do aplicativo.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            // Registro das rotas API
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Registro das rotas Web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
