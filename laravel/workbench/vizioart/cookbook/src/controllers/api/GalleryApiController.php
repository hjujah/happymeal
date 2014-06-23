<?php namespace Vizioart\Cookbook;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelBaseController as BaseController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator as LValidator;

use Vizioart\Cookbook\Models\GalleryModel as Gallery;


/**
 * 
 */
class GalleryApiController extends BaseController {

	/**
	 *
	 */
	public function getIndex(){

	}

	/**
	 * Get gallery by id 
	 */
	public function getFetchGallery($id){
		$result = Gallery::find($id);
		if (empty($result)){
            return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request.",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}
		return Response::json($result);
	}

	/**
	 * Create new gallery end return it (insert)
	 */
	public function getCreateGallery(){
		$result = Gallery::make('post-gallery');
		if(empty($result)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request.",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}
		return Response::json($result);
	}

	/**
	 * Save gallery (update)
	 */
	public function postSaveGallery(){

		$input = Input::get();

		if(empty($input['id'])){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. Error",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		$gallery = Gallery::find($input['id']);

		if(!$gallery){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. Error",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		if(!$gallery->updateSafe($input)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => $gallery->errors,
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		return Response::json($gallery);
	}

	/**
	 * Delete gallery
	 */
	public function deleteGallery($id){
		if(empty($id)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. No Item",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		$model = new Gallery();
		$success = $model->deleteSafe($id);
		if(!$success){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => $model->errors,
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		return Response::json(array('deleted' => true), 200);
	}

	/**
	 * Get gallery items
	 */
	public function getFetchItems($id){

		$result = Gallery::fetchItems($id);
		if(empty($result)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. Error",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}
		return Response::json($result);
	}

	/**
	 * Add item to gallery (insert)
	 */
	public function postAddItem(){
		$input = Input::get();

		if(empty($input['gallery_id'])){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. Error",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		$gallery = Gallery::find($input['gallery_id']);

		if(!$gallery){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. Error",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}
		$result = $gallery->insertItem($input);
		if(!$result){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => $gallery->errors,
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		return Response::json($result);
	}

	/**
	 * Delete gallery item
	 */
	public function deleteItem($id){
		if(empty($id)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. No Item",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}


		$item = Gallery::fetchItemById($id);

		if(!$item){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request.",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		$success = Gallery::deleteItems($id);
		if(!$success){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => $model->errors,
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		return Response::json(array('deleted' => true), 200);
	}
}


