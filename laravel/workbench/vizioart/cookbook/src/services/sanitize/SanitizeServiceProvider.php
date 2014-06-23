<?php namespace Vizioart\Cookbook\Services\Sanitize;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class SanitizeServiceProvider extends ServiceProvider {

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
    public function register() {

        $this->app['sanitize'] = $this->app->share(function($app) {
            return $app->make('Vizioart\Cookbook\Services\Sanitize\Sanitize', array('locale' => $app->getLocale()));
        });

        // add alias
        $loader = AliasLoader::getInstance();
        $loader->alias('Sanitize', 'Vizioart\Cookbook\Services\Sanitize\Facades\Sanitize');

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(){
        return array();
    }

}