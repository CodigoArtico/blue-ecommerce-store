<?php

namespace App\Providers;

use App\Models\Carrito;
use App\Models\Empresa;
use App\Models\PageWeb;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Compartir el nombre de page_web con todas las vistas si la tabla existe
        if (Schema::hasTable('page_web')) {
            $pageWeb = PageWeb::first();
            View::share('pageWeb', $pageWeb);
        }

        // Verificar si la tabla 'empresa' existe
        if (Schema::hasTable('empresa')) {
            // Obtener el primer registro de la tabla 'empresa' donde 'img_account' no sea nulo
            $empresa = Empresa::whereNotNull('img_account')->first();

            // Si se encontró un registro con 'img_account' no nulo, concatenar y compartir el valor con todas las vistas
            if ($empresa && isset($pageWeb)) {
                $imgAccount = $pageWeb->url_sistem .'/storage/img_account_empresa/'. $empresa->img_account;
                View::share('imgAccount', $imgAccount);
            }
        }

        // Usar un View Composer para compartir la cantidad de productos en el carrito
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $cartCount = Carrito::where('user_web_id', Auth::id())->count();
                $view->with('cartCount', $cartCount);
            }
        });
    }
}
