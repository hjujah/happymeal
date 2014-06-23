<?php namespace Vizioart\Cookbook\Models;

use DB;
use Vizioart\Cookbook\Models\DB\PostDBModel as PostDBModel;
use Vizioart\Cookbook\Models\DB\LanguageDBModel as LanguageDBModel;
use Vizioart\Cookbook\Models\DB\PostContentDBModel as PostContentDBModel;
use Vizioart\Cookbook\Models\DB\MetaDBModel as MetaDBModel;
use Vizioart\Cookbook\Models\GalleryModel as Gallery;
use Vizioart\Cookbook\Models\AttachmentModel as Attachment;


class PostModel extends PostDBModel {

	public static $errors = array();

	protected $appends = array('permalink', 'date', 'meta');


    // Decorators
    //----------------------------------------------
    public $permalink;
	public $date;
	public $meta;


	public function __construct(){
		parent::__construct();
		self::$errors = array();
	}


    public function getPermalinkAttribute(){
        return url($this->url);
    }

	public function getDateAttribute(){
        if(isset($this->created_at)){
            return date ( 'd-m-Y', strtotime($this->created_at) );
        }else{
            return null;
        } 
    }
    
    public function getMetaAttribute(){
        if(isset($this->meta)){
            return $this->sortMeta($this->meta);
        }else{
            return false;
        } 
    }

	// CRUD
	// ---------------------------------
	public static function make($type = null){
		if(empty($type)){
			$type = 'post';
		}
		$auto_draft = self::where('type', '=', $type)->where('status', '=', 'auto-draft')->with(array('post_contents.meta', 'galleries', 'post_meta'))->first();
		
		if(empty($auto_draft)){
			$auto_draft = new PostModel();
			if($type == 'post'){
				$parent = self::where('type', '=', 'page')->where('status', '=', 'publish')->where('archive_type', '=', 'post')->first();
				if(!$parent){
					self::$errors[] = 'No archive page';
					return false;
				}

				$parent_id = $parent->id;
			}else{
				$parent_id = 0;
			}
			$auto_draft->insert(array(
				'parent_id' => $parent_id,
				'type' => $type,
				'status' => 'auto-draft',
				'view' => ''
			));
		}

		return $auto_draft;
	}

	public function insert($params){

		$default_params = array(
			'parent_id' => null,
			'type' => '',
			'status' => '',
			'view' => ''
		);

		$params = array_replace_recursive($default_params, $params);

		$this->fill($params);

		try{
			
			DB::beginTransaction();
			if(!$this->save()){
				DB::rollBack();
				$this->errors[] = 'Failed to insert Post';
				return false;
			}
			
			DB::commit();
			return $this->id;	
			
		}catch(PDOException $e){
			DB::rollBack();    
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	public function update_safe($params){

		$default_params = array(
			'parent_id' => null,
			'type' => '',
			'status' => '',
			'view' => ''
		);

		$params = array_replace_recursive($default_params, $params);

		if(empty($this->id)){
			$this->errors[] = 'Invalid params';
			return false;
		}

		$parent_changed = false;

		if($this->parent_id != $params['parent_id']){
			$old_parent_id = $this->parent_id;
			$parent_changed = true;
		}

		$this->fill($params);

		try{
			
			DB::beginTransaction();

			if(!empty($params['post_contents']) && is_array($params['post_contents'])){
				foreach ($params['post_contents'] as $content) {
					if($this->status == 'auto-draft'){
						$this->status = $content['status'];
					}else if($this->status == 'draft' && $content['status'] == 'publish'){
						$this->status = $content['status'];
					}

					if(!$this->update_content($content)){
						DB::rollBack();
						return false;
					}
					$content_id = $content['id'];
					$this->load(array('post_contents' => function($query) use($content_id){
						$query->where('id', '=', $content_id)->with('meta');
					}));
				}
			}
			
			if(!$this->save()){
				DB::rollBack();
				$this->errors[] = 'Failed to update Post';
				return false;
			}

			if($parent_changed){
				if(!empty($this->parent_id)){
					$parent = self::find($this->parent_id);
					if($parent){
						// @TO-DO set parent url for all contents
					}
				}
			}

			if(!empty($params['galleries']) && is_array($params['galleries'])){
				foreach ($gallerise as $gallery) {
					if(!empty($gallery['id'])){
						if(!$this->attach_gallery($gallery['id'])){
							DB::rollBack();
							return false;
						}
					}
				}
				
			}
			/*
			if(!empty($params['meta']) && is_array($params['meta'])){
				foreach ($params['meta'] as $meta_key => $meta_value) {
					$meta = MetaDBModel::where('parent_id', '=', $this->id)->where('parent_type', '=', get_class($this))->where('meta_key', '=', $meta_key)->first();
					if($meta){
						$meta->meta_value = $meta_value;
						if(!$meta->save()){
							DB::rollBack();
							$this->errors[] = 'Cant save post meta';
							return false;
						}
					}else{
						$meta = new MetaDBModel();
						$meta->meta_key = $meta_key;
						$meta->meta_value = $meta_value;

						if(!$this->meta()->save($meta)){
							DB::rollBack();
							$this->errors[] = 'Cant save post meta';
							return false;
						}
					}
				}
			}
			*/
			DB::commit();
			return $this->id;	
			
		}catch(PDOException $e){
			DB::rollBack();    
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	public function insert_content($params){

		$default_params = array(
			'language_id' => 0,
			'user_id' => 0,
			'url' => '',
			'name' => '',
			'type' => '',
			'status' => '',
			'title' => '',
			'content' => ''
		);

		$params = array_replace_recursive($default_params, $params);

		$content = new PostContentDBModel();
		$content->fill($params);

		try{
			
			DB::beginTransaction();
			
			if(!$this->post_contents()->save($content)){
				DB::rollBack();
				$this->errors[] = 'Failed to insert Post Content';
				return false;
			}

			
			DB::commit();
			return $content;	
			
		}catch(PDOException $e){
			DB::rollBack();    
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	public function update_content($params){

		$default_params = array(
			'id' => 0,
			'language_id' => 0,
			'user_id' => 0,
			'url' => '',
			'name' => '',
			'type' => '',
			'status' => '',
			'title' => '',
			'content' => ''
		);

		$params = array_replace_recursive($default_params, $params);

		if(empty($params['id'])){
			$this->errors[] = 'Invalid params';
			return false;
		}

		$content = PostContentDBModel::find($params['id']);
		if(!$content){
			$this->errors[] = 'Invalid params';
			return false;
		}

		$content->fill($params);

		try{
			
			DB::beginTransaction();
			
			if(!$this->post_contents()->save($content)){
				DB::rollBack();
				$this->errors[] = 'Failed to update Post Content';
				return false;
			}

			if(!empty($params['meta']) && is_array($params['meta'])){
				foreach ($params['meta'] as $meta_key => $meta_value) {
					$meta = MetaDBModel::where('parent_id', '=', $content->id)->where('parent_type', '=', get_class($content))->where('meta_key', '=', $meta_key)->first();
					if($meta){
						$meta->meta_value = $meta_value;
						if(!$meta->save()){
							DB::rollBack();
							$this->errors[] = 'Cant save post meta';
							return false;
						}
					}else{
						$meta = new MetaDBModel();


						$meta->meta_key = $meta_key;
						$meta->meta_value = $meta_value;

						if(!$content->meta()->save($meta)){
							DB::rollBack();
							$this->errors[] = 'Cant save post meta';
							return false;
						}

					}
				}
			}
			
			DB::commit();
			return $content;	
			
		}catch(PDOException $e){
			DB::rollBack();    
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	public static function delete_content($content_id){
		$content = PostContentDBModel::find($content_id);
		if(!$content){
			self::$errors[] = 'Invalid content ID';
			return false;
		}

		$post = self::with('post_contents')->find($content->post_id);

		$change_to_draft = true;
		foreach ($post->post_contents as $post_content) {
			if($post_content->id != $content->id && $post_content->status == 'publish'){
				$change_to_draft = false;
			}
		}

		try{
			DB::beginTransaction();

			if(!$content->delete()){
				DB::rollBack();
				self::$errors[] = 'Failed to delete Post Content';
				return false;
			}

			if($change_to_draft){
				$post->status = 'draft';

				if(!$post->save()){
					DB::rollBack();
					self::$errors[] = 'Failed to update Post status';
					return false;
				}
			}

			DB::commit();
			return true;
		}catch(PDOException $e){
			DB::rollBack();    
			self::$errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	//
	public function attach_gallery($gallery_id){
		$gallery = Gallery::find($gallery_id);

		if(!$gallery){
			$this->errors[] = 'No such gallery';
			return false;
		}

		if($this->galleries->contains($gallery_id)){
			return true;
		}

		$this->galleries()->attach($gallery_id, array('type' => Gallery::POST_ITEM_TYPE));

		return true;

	}

	//
	public function attach_attachment($attachment_id){
		$attachment = Attachment::find($attachment_id);

		if(!$attachment){
			$this->errors[] = 'No such attachment';
			return false;
		}

		$this->featured_image()->save($attachment);

		return true;

	}

	public function get_content($language_code){
		$language = LanguageDBModel::where('code', '=', $language_code)->first();

		if(!$language){
			return false;
		}

		$id = $this->id;
		$language_id = $language->id;

		$content = PostContentDBModel::with('meta')->where('post_id', '=', $id)->where('language_id', '=', $language_id)->first();
		
		if(!$content){
			$content = $this->insert_content(array(
				'user_id' => 1,
				'language_id' => $language_id,
				'type' => $this->type,
				'status' => 'auto-draft'
			));
		}

		return $content;
	}

	public static function is_url_unique($url){

		$post = PostContentDBModel::where('url', '=', $url)->first();
		return !$post;
	}


	// FRONTENT API CALLS GETTERS
	// -----------------------------------------------------------------------

	public function get_all_news($lang_code = null){
        $query = self::get_page_query();

        if(empty($lang_code)){
            $lang_code = 'cs';
        }

        $query->where('languages.id', '=', self::get_lang_by_code($lang_code));

        $query->where('posts.type', '=', 'post');

        $query->orderBy('post_contents.created_at', 'desc');

        $news = $query->get();

        return $news;
    }

    // @CHANGE !!!
	public function get_page_by_url($url, $lang_code = null){
        if(empty($url)){
            return false;
        }

        if(empty($lang_code)){
            $lang_code = 'cs';
        }

        $query = self::get_page_query();
        $query->where('post_contents.url', '=', $lang_code . '/' . $url);

        /* 
        if(!empty($lang_code)){
            $query->where('languages.id', '=', self::get_lang_by_code($lang_code));
        }
        */

        $query->with('galleries.items');

        $page = $query->first();

        //$queries = DB::getQueryLog();
        if (empty($page)){
            return false;
        }

        $children = $this->get_page_children($page->id, $lang_code);
        $page->children = $children;

        $siblings = $this->get_page_siblings($page->id, $lang_code);
        $page->siblings = $siblings;

        return $page;
    }

	public function get_by_id($id, $lang_code = 'cs'){
        $query = self::query();

        $lang_id = $this->get_lang_by_code($lang_code);
        if (empty($lang_id)){
            return false;
        }

        $query->with(array(
            'post_contents' => function($query) use ($lang_id){
                $query->where('language_id', '=', $lang_id )->with(array(
                    'language'
                ));
            }
        ));

        $query->where('id', $id);
        $post = $query->first();

        if(empty($post)){
            return false;
        }

        $post = $post->toArray();
        $post['children'] = $this->get_page_children($post['id'], $lang_code);

        return $post;
    }

	public function get_page_by_id($id, $lang_code = 'cs'){        
        $query = self::query();

        $lang_id = $this->get_lang_by_code($lang_code);
        if (empty($lang_id)){
            return false;
        }

        $query->with(array(
            'post_contents' => function($query) use ($lang_id){
                $query->where('language_id', '=', $lang_id )->with(array(
                    'language'
                ));
            }
        ));
        $query->where('type', 'page');
        $query->where('id', $id);
        $page = $query->first();



        if(empty($page)){
            return false;
        }

        $page = $page->toArray();
        $page['children'] = $this->get_page_children($page['id'], $lang_code);

        return $page;
    }

	public function get_page_children($id, $lang_code = null){


        if(empty($id)){
            return false;
        }

        $query = self::get_page_query();

        $query->where('posts.parent_id', '=', $id);

        if(!empty($lang_code)){
            $query->where('languages.id', '=', self::get_lang_by_code($lang_code));
        }

        $pages = $query->get();

        return $pages;

    }

	public function get_page_siblings($post, $lang_code = null){


        if(empty($post)){
            return false;
        }

        if (is_array($post)) {
            $_post = $post;
        } else {
            $_post = $this->get_by_id($post);
        }

        if (!$_post){
            return false;
        }

        $parent_id = $_post['parent_id'];
        if (!empty($parent_id)){
            $siblings = $this->get_page_children($parent_id, $lang_code);
        } else {
            $siblings = array();
        }

        // set active page
        foreach ($siblings as $page) {
            if ($_post['id'] == $page['id']){
                $page['active'] = true;
            }
        }

        return $siblings;

    }

    /**
	 *	@TO_DO
	 */
	public static function get_parent_title($parent_id = '', $lang_code = ''){
		
		if (empty($parent_id)){
			return false;
		}

		// get parent id 

		// get parent title
		$query = self::query();
		$query->join('post_contents', 'posts.id', '=', 'post_contents.post_id')
			  ->select('post_contents.title')
			  ->where('posts.id', '=', $parent_id);

		if (!empty($lang_code)){
			if ( $language_id = self::get_lang_by_code($lang_code) ){
				$query->where('post_contents.language_id', '=', $language_id);
			} else {
				// throw error since this shouldnt be possible
			}
		}
			 
		$res = $query->first();

		if (isset($res['title'])){
			$res = $res['title'];
		} else {
			$res = false;
		}
		
		return $res;
	}

	public function get_routes(){
        $query = self::query();

        $query->join('post_contents', 'posts.id', '=', 'post_contents.post_id');
        $query->select(
            'posts.id as id',
            'post_contents.id as content_id',
            'posts.type as type',
            'posts.view as view',
            'post_contents.url as url',
            'post_contents.language_id as language_id',
            'post_contents.name as name',
            'posts.status as post_status',
            'post_contents.status as content_status'
        );

        $routes = $query->get();

        if(empty($routes)){
            return false;
        }
        return $routes;
    }

	public function get_by_url($url){
        if(empty($url)){
            return false;
        }

        $url_segments = explode('/', $url);
        if(sizeof($url_segments) > 0){
        	$lang_code = $url_segments[0];
        }else{
        	$lang_code = 'cs';
        }

        $url = rtrim($url, '/');

        $query = self::get_page_query();
        $query->where('post_contents.url', '=', $url);
        $query->with('galleries.items');

        $post = $query->first();

        if($post){
        	// get post_content
        	$content = PostContentDBModel::with('meta')->find($post->content_id);

        	// post_content meta
        	$content_meta = $content->meta;
        	$content_meta = $content_meta->toArray();

        	// get post meta
        	$meta = $post->post_meta;
        	$meta = $meta->toArray();
        	$meta = array_merge($meta, $content_meta);

        	// merge post meta and post_content meta 
        	$post->meta = $meta;

        	// get children
        	$children = $this->get_page_children($post->id, $lang_code);
	        $post->children = $children;

	        // get siblings (same lvl)
	        $siblings = $this->get_page_siblings($post->id, $lang_code);
	        $post->siblings = $siblings;

	        // get parent title
            if ($post->parent_id){
                $parent_title = self::get_parent_title($post->parent_id, $lang_code);
                $post->parent_title = $parent_title;
            }

            return $post;
        }
        return false;

    }

	private static function get_lang_by_code($code){
        $language = array(
            'cs' => 1,
            'en' => 2,
            'ru' => 3
        );
        if (array_key_exists($code, $language)){
            return $language[$code];
        } else {
            return 0;
        }
    }

	private static function get_page_query(){
        $query = self::query();

        $query->join('post_contents', 'posts.id', '=', 'post_contents.post_id');
        $query->join('languages', 'languages.id', '=', 'post_contents.language_id');
        $query->select(
            'posts.id as id',
            'posts.parent_id as parent_id',
            'posts.type as type',
            'posts.status as post_status',
            'posts.view as view',
            'posts.created_at as created_at',
            'posts.updated_at as updated_at',
            'post_contents.id as content_id',
            'post_contents.language_id as language_id',
            'post_contents.user_id as user_id',
            'post_contents.url as url',
            'post_contents.name as name',
            'post_contents.title as title',
            'post_contents.content as content',
            'post_contents.status as content_status',
            'languages.code as language_code',
            'languages.description as language'
        );

        $query->with('featured_image', 'post_meta')
        	  ->where('posts.status', '=', 'publish');
        

        return $query;
    }
    
    protected function sortMeta($meta){
        
        if(!empty($meta)){
        	if(is_array($meta[0])){
        		$meta_object = array();
        	}else{
        		$meta_object = new \stdClass();
        	}
        	
        	foreach ($meta as $meta_row) {
        		
            	if(is_array($meta_row)){

                    if(array_key_exists($meta_row['meta_key'], $meta_object)){
                        if(!is_array($meta_object[$meta_row['meta_key']])){
                            $meta_object[$meta_row['meta_key']] = array($meta_object[$meta_row['meta_key']], $meta_row['meta_value']);
                        }else{
                            $meta_object[$meta_row['meta_key']][] = $meta_row['meta_value'];
                        }
                    }else{
                        $meta_object[$meta_row['meta_key']] = $meta_row['meta_value'];
                    }
                }else if(is_object($meta_row)){
                	
                	$key = $meta_row->meta_key;
                	$value = $meta_row->meta_value;
                    if(property_exists($meta_object, $key)){
                    	
                        if(!is_array($meta_object->$key)){
                            $meta_object->$key = array($meta_object->$key, $value);
                        }else{
                            array_push($meta_object->$key, $value);
                        }
                    }else{
                        $meta_object->$key = $value;
                    }
	            }else{
	                $meta_object = false;
	            }
	        }

            return $meta_object;

        }else{
            return false;
        }
    }
}