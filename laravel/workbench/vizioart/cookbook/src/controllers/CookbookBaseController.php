<?php namespace Vizioart\Cookbook;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelBaseController as BaseController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\File\File as SFile;
use Illuminate\Support\Facades\Validator as LValidator;

/**
 * 
 */
class CookbookBaseController extends BaseController {

	protected $layout = "cookbook::layouts.default";

	protected $scritps = array();

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout() {
		if ( ! is_null($this->layout)) {


			$cb_js_var = array(
				'site_url' => url('/'),
				'admin_url' => url('/admin'),
				'admin_assets_url' => asset('packages/vizioart/cookbook/'),
				'admin_api_url' => url('/admin/api')
			);


			$this->layout = View::make($this->layout, array(
				'cb_js_var' => $cb_js_var
			));
		}
	}
}