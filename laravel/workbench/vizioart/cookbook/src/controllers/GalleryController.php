<?php namespace Vizioart\Cookbook;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
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
class GalleryController extends AdminPageBaseController {


	public function index(){

		$this->renderPage(array(
			"content_view" => 'cookbook::pages.gallery.index'
		));

	}

	/**
	 * Add new Gallery
	 *
	 */
	public function add(){


		$content_data = array();
		if (!empty($id)){
			//$model = Gallery::where('type', 'page')->find($id);
			if ($model){
				//$conetnt_data['gallery'] = $model;
			} else {
				// 404
			}
		}else{
			// 404
		}

		// Page scripts
		$footer_data['scripts'] = array(
			array(
				'src' => asset('packages/vizioart/cookbook/js/lib/require.min.js'),
				'attrs' => array(
					'data-main' => asset('packages/vizioart/cookbook/js/app/gallery/ps-gallery-main.js')
				),
			)
		);

		$this->renderPage(array(
			"content_view" => 'cookbook::pages.gallery.edit',
			"content_data" => $content_data,
			"footer_data" => $footer_data
		));

	}

	/**
	 * Edit existing Gallery
	 *
	 * @param string|int gallery->id
	 */
	public function edit($id = false){


		$content_data = array();
		if (!empty($id)){
			//$model = Gallery::where('type', 'page')->find($id);
			if ($model){
				//$conetnt_data['gallery'] = $model;
			} else {
				// 404
			}
		}else{
			// 404
		}

		// Page scripts
		$footer_data['scripts'] = array(
			array(
				'src' => asset('packages/vizioart/cookbook/js/lib/require.min.js'),
				'attrs' => array(
					'data-main' => asset('packages/vizioart/cookbook/js/app/gallery/ps-gallery-main.js')
				),
			)
		);

		$this->renderPage(array(
			"content_view" => 'cookbook::pages.gallery.edit',
			"content_data" => $content_data,
			"footer_data" => $footer_data
		));

	}


}