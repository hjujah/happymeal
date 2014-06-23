<?php namespace Vizioart\HandlebarsL4;

use Illuminate\Support\ServiceProvider;

class HandlebarsL4ServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('vizioart/handlebars-l4');

		$app = $this->app;

		$app->extend('view.engine.resolver', function($resolver, $app) {

			/**
			 * Register a new engine resolver.
			 * The engine string typically corresponds to a file extension.
			 *
			 * $resolver - Class Illuminate\View\Engines\EngineResolver
			 * 		-> public void register(string $engine, Closure $resolver)
			 */
			$resolver->register('handlebars', function() use($app) {
				return $app->make('Vizioart\HandlebarsL4\HandlebarsEngine');
			});
			return $resolver;
		});


		$app->extend('view', function($env, $app){
			
			/**
			 * Register a valid view extension and its engine.
			 *
			 * $env - Class Illuminate\View\Environment
			 * 		-> public void addExtension(string $extension, string $engine, Closure $resolver = null)
			 */
			$env->addExtension('hbs', 'handlebars');
			return $env;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('handlebars-l4');
	}

}