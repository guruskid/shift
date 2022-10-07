<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapApiV2Routes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();

        $this->mapTradeNairaRoutes();

        // $this-> mapTradeNairaRoutesV2();

        $this->mapTradeNairaWebRoutes();

        $this->mapApiV2OtherRoutes();

        $this->mapApiAdminRoutes();

        $this->mapApiCustomerHappinessRoutes();

        $this->mapApiManagerRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapApiV2Routes()
    {
        Route::prefix('api_v2')
            ->middleware('api')
            ->namespace($this->namespace . '\ApiV2')
            ->group(base_path('routes/api_v2.php'));
    }

    protected function mapApiV2OtherRoutes()
    {
        Route::prefix('api_v2')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_v2_other.php'));
    }

    /**
     * Maps admin route for our application
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
            ->middleware(['web', 'auth', 'admin'])
            ->namespace($this->namespace . '\Admin')
            ->group(base_path('routes/admin.php'));
    }

    public function mapTradeNairaRoutes()
    {
        Route::prefix('api_v2/trade_naira_api')
            ->middleware(['api'])
            ->namespace($this->namespace . '\Api\TradeNaira')
            ->group(base_path('routes/trade_naira.php'));
    }


    // public function mapTradeNairaRoutesV2()
    // {
    //     Route::prefix('api_v2')
    //         ->middleware(['api'])
    //         ->namespace($this->namespace . '\Api\TradeNaira')
    //         ->group(base_path('routes/trade_naira.php'));
    // }

    public function mapTradeNairaWebRoutes()
    {
        Route::prefix('trade_naira_web')
            ->middleware(['web', 'auth'])
            ->namespace($this->namespace . '\Api\TradeNaira')
            ->group(base_path('routes/trade_naira.php'));
    }

    protected function mapApiAdminRoutes()
    {
        Route::prefix('api_admin')
            ->middleware(['api', 'admin'])
            ->namespace($this->namespace . '\ApiV2\Admin')
            ->group(base_path('routes/api_admin.php'));
    }

    protected function mapApiCustomerHappinessRoutes()
    {
        Route::prefix('api_v2')
            ->middleware(['api', 'customerHappiness'])
            ->namespace($this->namespace . '\ApiV2\Customerhappiness')
            ->group(base_path('routes/customerhappy.php'));
    }

    protected function mapApiManagerRoutes()
    {
        Route::prefix('api_v2')
            ->middleware(['api', 'manager'])
            ->namespace($this->namespace . '\ApiV2\Manager')
            ->group(base_path('routes/manager.php'));
    }
}
