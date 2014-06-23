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

use Vizioart\Cookbook\Models\AttachmentModel as Attachment;


/**
 * 
 */
class AttachmentApiController extends BaseController {

	/**
	 *
	 */
	public function getIndex(){

	}

	/**
	 * Get Attachment by id 
	 */
	public function getFetchAttachment($id){
		$result = Attachment::find($id);
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
	 * Create new Attachment end return it (insert)
	 */
	public function getCreateAttachment(){
		$result = Attachment::make('post', 'featured-image');
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
	 * Save Attachment (update)
	 */
	public function postSaveAttachment(){

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

		$attachment = Attachment::find($input['id']);

		if(!$attachment){
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

		if(!$attachment->updateSafe($input)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => $attachment->errors,
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		return Response::json($attachment);
	}

	/**
	 * Delete Attachment
	 */
	public function deleteAttachment($id){
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

		$model = new Attachment();
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


}


