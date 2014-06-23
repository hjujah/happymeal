<?php namespace Vizioart\HandlebarsL4;

use Illuminate\View\Engines\EngineInterface;
use Illuminate\Filesystem\Filesystem;
use Handlebars\Handlebars;
use Handlebars\Helpers;

class HandlebarsEngine implements EngineInterface {

	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}
	
	public function get($path, array $data = array())
	{
		$view = $this->files->get($path);
		$app = app();

		$options = array();
		$handlebars_config = $app['config']->get('handlebars-l4::config');
		

		if ( isset($handlebars_config['partials_loader']) ) {
            $options['partials_loader'] = $handlebars_config['partials_loader'];
        }

		if ( isset($handlebars_config['helpers']) && is_array($handlebars_config['helpers']) ){
			$options['helpers'] = new Helpers($handlebars_config['helpers']);
		}

		$engine = new Handlebars( $options );

 
 		$data = array_map(function($item){
			return (is_object($item) && method_exists($item, 'toArray')) ? $item->toArray() : $item;
		}, $data);
 
		return $engine->render($view, $data);
	}

}
