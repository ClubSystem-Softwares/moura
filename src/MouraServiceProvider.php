<?php

namespace CSWeb\Moura;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/**
 * Class MouraServiceProvider
 *
 * @author  Matheus Lopes Santos <fale_com_lopez@hotmail.com>
 * @version 1.0.0
 * @package CSWeb\Moura
 */
class MouraServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/moura.php' => config_path('moura.php')
        ]);
    }

    public function register()
    {
        $this->app->singleton(Moura::class, function ($app) {
            $host     = Config::get('moura.host');
            $username = Config::get('moura.username');
            $password = Config::get('moura.password');

            return new Moura($host, $username, $password);
        });
    }
}