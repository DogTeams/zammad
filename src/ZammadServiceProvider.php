<?php
/**
 * @package Zammad API Wrapper
 * @author  Jordan GOBLET <jordan.goblet.pro@gmail.com>
 */
namespace Dogteam\Zammad;

use Illuminate\Support\ServiceProvider;

class ZammadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/zammad.php', 'zammad');
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */

    public function boot()
    {
        $this->publishes([__DIR__.'/config/zammad.php' => config_path('zammad.php'),
    ], 'config');
        $this->loadViewsFrom(__DIR__.'/Views', 'test');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
    }
}
