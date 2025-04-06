<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http;

use Route;

class RouteServiceProvider extends \Weiran\Framework\Application\RouteServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            $this->mapBackendRoutes();
        });
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapBackendRoutes(): void
    {
        Route::group([
            'prefix'     => $this->prefix . '/sms',
            'middleware' => 'backend-auth',
        ], function () {
            require_once __DIR__ . '/Routes/backend.php';
        });
    }
}