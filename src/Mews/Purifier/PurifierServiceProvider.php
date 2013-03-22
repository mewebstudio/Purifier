<?php namespace Mews\Purifier;

use Illuminate\Support\ServiceProvider;

class PurifierServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('mews/purifier');

		$app = $this->app;

	    $this->app->finish(function() use ($app)
	    {

	    });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	    $this->app['purifier'] = $this->app->share(function($app)
	    {
	        return new Purifier($app['view'], $app['config']);
	    });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('purifier');
	}

}