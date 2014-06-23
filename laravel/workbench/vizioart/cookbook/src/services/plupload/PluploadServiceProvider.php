<?php namespace Vizioart\Cookbook\Services\Plupload;

use Illuminate\Support\ServiceProvider;

class PluploadServiceProvider extends ServiceProvider {

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

        $this->app['plupload'] = $this->app->share(function($app) {
            return $app->make('Vizioart\Cookbook\Services\Plupload\Manager', array('request' => $app['request']));
        });

        // add alias
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Plupload', 'Vizioart\Cookbook\Services\Plupload\Facades\Plupload');

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