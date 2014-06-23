<?php namespace Vizioart\Cookbook\Services\Plupload;

use Illuminate\Config\Repository as Config;

class Plupload {

        /**
         * Config Instance
         *
         * @var Illuminate\Config\Repository
         */
        protected $config;

        /**
         * Constructor
         *
         * @param  Illuminate\Config\Repository $config
         * @return void
         */
        public function __construct(Config $config)
        {
                $this->config = $config;
        }

    /**
     * Get a plupload configuration option
     *
     * @param  string $option
     * @return mixed
     */
    public function getConfigOption($option)
    {
        return $this->config->get("cookbook::plupload.{$option}");
    }

    public function getDefaultView() {
        return $this->getConfigOption('view');
    }
}