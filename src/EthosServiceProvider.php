<?php


namespace MelonSmasher\EthosPHP\Laravel;


use Illuminate\Support\ServiceProvider;
use MelonSmasher\EthosPHP\ErpBackend;

/**
 * Class EthosServiceProvider
 *
 * The Ethos service provider.
 *
 * @package MelonSmasher\EthosLaravel
 * @license https://raw.githubusercontent.com/MelonSmasher/ethos-php/master/LICENSE MIT
 * @author Alex Markessinis
 */
class EthosServiceProvider extends ServiceProvider
{
    /**
     * Boot
     *
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/ethos.php' => config_path('ethos.php'),
        ], 'ethos');
    }

    /**
     * Register
     *
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ethos.php', 'ethos');

        $this->app->singleton('ethos', function ($app) {
            $config = $app->make('config');
            $baseUrl = $config->get('ethos.base_url') ?: 'https://integrate.elluciancloud.com';
            $erpBackend = $config->get('ethos.erp_backend') ?: ErpBackend::COLLEAGUE;
            $secret = $config->get('ethos.secret');
            return new EthosService($secret, $baseUrl, $erpBackend);
        });
    }

    /**
     * Provides
     *
     * Provide the service.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['ethos'];
    }
}
