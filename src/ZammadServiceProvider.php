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
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('zammad', function(){
            return new Dogteam\Zammad\Zammad;
        });
    }
}
