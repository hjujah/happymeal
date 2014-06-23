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
use Symfony\Component\HttpFoundation\File\File as SFile;
use Illuminate\Support\Facades\Validator as LValidator;

use Vizioart\Cookbook\Models\PostModel as Post;


/**
 * 
 */
class PageApiController extends BaseController {

	/**
	 *
	 */
	public function getIndex(){

		$query = Post::query();

		$query->with(array("post_contents", "featured_image.file"));

		$query->where('type', '=', 'post');

		// @CHANGE
		$status = 'publish';

		$query->where('status', '=', $status);

		// @CHANGE
		$order_by = 'created_at';
		$order = 'desc';

		$query->orderBy($order_by, $order);

		$result = $query->get();

		return Response::json($result);
	}

	/**
	 *
	 */
	public function getPagesDatatables(){

		$pages = Post::where('type', 'page')
					 ->whereNotIn('status', array('trash', 'auto-draft'));

		$total = $pages->count();

		$result = $pages->with("post_contents")
						->get()
						->toArray();


		return Response::json(array(
			"sEcho" => 1,
			"iTotalRecords" => $total,
			"iTotalDisplayRecords" => $total,
			"aaData" => $result
		));
	}

	/**
	 * Get page by id 
	 */
	public function getPage($id){
		$result = Post::with(array("post_contents.meta", "post_meta", "galleries", "featured_image.file"))->find($id);
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
	 * Get page parents choice (All published pages)
	 */
	public function getParents(){
		$result = Post::with(array("post_contents" => function($query){
			$query->where('status', '=', 'publish');
		}))->where('type', '=', 'page')->where('status', '=', 'publish')->get();
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

		$pages = $result->toArray();
		$drilled = array();

		$this->drill_children($drilled, $pages);

		$drilled = array_values($drilled);

		return Response::json($drilled);
	}

	protected function drill_children(&$drilled, $data, $parent = null, $level = 0){
		foreach ($data as $obj) {
			if(!array_key_exists($obj['id'], $drilled)){
				if(empty($parent) || (!empty($parent) && $parent['id'] == $obj['parent_id'])){
					$obj['level'] = $level;
					$drilled[$obj['id']] = $obj;
					$newLevel = $level + 1;
					$this->drill_children($drilled, $data, $obj, $newLevel);
				}
			}
		}
	}


	public function getCreatePage(){

		$result = Post::make('page');
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

	public function getContent($id, $language_code){
		if(empty($language_code)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. No language",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		$post = Post::find($id);
		if(empty($post)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. No Post",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}


		$result = $post->get_content($language_code);
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

	public function deleteContent($id){
		if(empty($id)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => "Bad request. No Content",
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		$model = new Post();
		$success = $model->delete_content($id);
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

	public function postSave(){
		//die("nesto");
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

		$post = Post::find($input['id']);

		if(!$post){
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

		if(!$post->update_safe($input)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => $post->errors,
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		$post->load('galleries', 'featured_image.file');

		return Response::json($post);
	}

	public function getAttachGallery($id, $galleryId){
		if(empty($id) || empty($galleryId)){
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

		$post = Post::find($id);

		if(!$post){
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

		if(!$post->attach_gallery($galleryId)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => Post::$errors[0],
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		return Response::json(array('attached' => true), 200);
	}

	public function getAttachAttachment($id, $attachmentId){
		if(empty($id) || empty($attachmentId)){
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

		$post = Post::find($id);

		if(!$post){
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

		if(!$post->attach_attachment($attachmentId)){
			return Response::json( 
            	array(
		            'error' => array(
		                'message' => Post::$errors[0],
		                'type' => "InvalidParameters", 
		                'code' => 100
		            )
                ), 400 // bad request
            );
		}

		return Response::json(array('attached' => true), 200);
	}

	public function getIsUrlUnique($url){

		$model = new Post();
		$isUnique = $model->is_url_unique($url);
		return Response::json( array('isUnique' => $isUnique), 200 );
	}
}

