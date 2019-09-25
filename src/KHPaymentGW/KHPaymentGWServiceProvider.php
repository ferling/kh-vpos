<?php

namespace KHPaymentGW;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class KHPaymentGWServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__.'/../config/khpayment.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('khpayment.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('khpayment');
        }


        if ($this->app instanceof LaravelApplication && ! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom($source, 'khpayment');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('KHPaymentGW\KHPaymentGateway', function ($app) {
            $mid = $app['config']['khpayment']['mid'];
            $ccy = $app['config']['khpayment']['currency'];
            $lang = $app['config']['khpayment']['lang'];
            $privateKey  = $app['config']['khpayment']['private_key'];
            $gw = new KHPaymentGW\KHPaymentGateway($mid, $ccy, $lang, $privateKey);
            if ($app['config']['khpayment']['live']) {
                $gw->setLive(true);
            }
            if ($app['config']['khpayment']['check_signature']) {
                $gw->setCheckSignature(true);
            }
            return $gw;
        });
    }
}
