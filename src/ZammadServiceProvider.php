<?php

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
        $this->app->bind('zammad', function(){
            return new Dogteam\Zammad\Zammad;
        });
    }
}
