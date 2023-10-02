<?php

namespace Eptic\Latte;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class LatteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('latte.engine', function (Application $app) {
            $engine = new \Latte\Engine();
            $engine->setTempDirectory($app['config']['view.compiled']);
            $loader = new \Latte\Loaders\FileLoader($app['config']['view.paths'][0]);
            $engine->setLoader($loader);

            return $engine;
        });

        $this->app->bind('view', function (Application $app) {
            return new ViewFactory($app, $app['latte.engine']);
        });
    }
}
