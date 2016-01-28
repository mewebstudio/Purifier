<?php namespace Mews\Purifier;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class PurifierServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return null
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication) {
            $this->publishes([
                __DIR__ . '/../config/purifier.php' => config_path('purifier.php')
            ]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('purifier');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/purifier.php', 'mews.purifier'
        );

        $this->app->bind('purifier', function ($app) {
            return new Purifier($app['files'], $app['config']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['purifier'];
    }

}
