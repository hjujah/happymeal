<?php namespace Vizioart\Cookbook\Models;

use Vizioart\Cookbook\Models\DB\GalleryDBModel as GalleryDB;
use Vizioart\Cookbook\Models\DB\GalleryItemDBModel as GalleryItemDB;
use Vizioart\Cookbook\Models\FileModel as FileModel;
use Vizioart\Cookbook\Models\PostModel as Post;
use DB;

class GalleryModel extends GalleryDB {

	const DEFAULT_TYPE = 'image-gallery';
	const POST_ITEM_TYPE = 'post-gallery';

	/**
	 * @name make
	 *
	 * Create new gallery from scratch
	 * 
	 * @param string $type [optional] - Type of the gallery that is created
	 * @return bool|array - returns Eloquent model of gallery or FALSE if there was an error
	 * @static
	 */
	public static function make($type = self::DEFAULT_TYPE){
		$gallery_params = array(
			'type' => $type,
			'status' => 'publish',
			'name' => '',
			'description' => ''
		);

		$gallery = new GalleryModel();

		$gallery->fill($gallery_params);

		try{
			
			DB::beginTransaction();
			
			if(!$gallery->save()){
				DB::rollBack();
				$this->errors[] = 'Failed to create Gallery';
				return false;
			}
			
			DB::commit();
			return $gallery;	
			
		}catch(PDOException $e){
			DB::rollBack();    
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	/**
	 * @name updateSafe
	 *
	 * Save gallery (update)
	 * NOTE: gallery should be instantiated in $this
	 * 
	 * @param array $params - Array of values for gallery update
	 * @return bool - returns TRUE on success or FALSE if there was an error
	 */
	public function updateSafe($params){
		$defaultParams = array(
			'type' => self::DEFAULT_TYPE,
			'status' => '',
			'name' => null,
			'description' => null
		);

		$params = array_replace_recursive($defaultParams, $params);

		$this->fill($params);

		try{
			
			DB::beginTransaction();
			
			if(!$this->save()){
				DB::rollBack();
				$this->errors[] = 'Failed to save Gallery';
				return false;
			}
			
			DB::commit();
			return true;	
			
		}catch(PDOException $e){
			DB::rollBack();    
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}


	/**
	 * @name deleteSafe
	 *
	 * Delete gallery (delete)
	 * 
	 * @todo should delete all items and files (File I/O also)
	 */
	public function deleteSafe($params){

	}


	/**
	 * @name fetchItems
	 *
	 * Fetch gallery items by gallery ID
	 * 
	 * @param int $id - ID of gallery wich items will be fetched
	 * @return bool|array - returns Eloquent collection of gallery items or FALSE if there was an error
	 * @static
	 */
	public static function fetchItems($id){
		if(empty($id)){
			$this->errors[] = 'Invalid Params';
			return false;
		}

		$gallery = self::with('items.file')->find($id);

		if(!$gallery){
			$this->errors[] = 'Invalid Params';
			return false;
		}

		$items = $gallery->items;

		if(!$items){
			return array();
		}
		return $items;
	}

	/**
	 * @name insertItem
	 *
	 * Insert gallery item (insert)
	 * NOTE: gallery should be instantiated in $this
	 * 
	 * @param array $params - Array of values for gallery item insert
	 * @return bool|array - returns Eloquent model of gallery item or FALSE if there was an error
	 */
	public function insertItem($params){
		$defaultParams = array(
			'type' => '',
			'status' => '',
			'file_id' => null,
			'name' => null,
			'description' => null,
			'content' => null
		);

		$params = array_replace_recursive($defaultParams, $params);

		$item = new GalleryItemDB();
		$item->fill($params);

		try{
			
			DB::beginTransaction();
			
			if(!$this->items()->save($item)){
				DB::rollBack();
				$this->errors[] = 'Failed to insert Item';
				return false;
			}
			
			DB::commit();
			$item->load('file');
			return $item;

		}catch(PDOException $e){
			DB::rollBack();
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	/**
	 * @name deleteItems
	 *
	 * Delete gallery item/items by their ids (delete)
	 * 
	 * @param array|int $ids - Array of IDs or single ID of item/items for delete
	 * @return bool - returns TRUE on success or FALSE if there was an error
	 * @static
	 */
	public static function deleteItems($ids){
		if(empty($ids)){
			return false;
		}

		try{
			
			DB::beginTransaction();

			if(!is_array($ids)){
				$ids = array($ids);
			}
			$file_ids = array();
			foreach ($ids as $id) {
				$gallery_item = GalleryItemDB::find($id);
				if(!$gallery_item){
					DB::rollBack();
					$this->errors[] = 'Invalid Gallery Item ID';
					return false;
				}

				if($gallery_item->file_id){
					$file_ids[] = $gallery_item->file_id;
				}

				if(!$gallery_item->delete()){
					DB::rollBack();
					$this->errors[] = 'Failed to delete Gallery Item';
					return false;
				}
			}

			$file_model = new FileModel();
			if(!$file_model->delete_safe($file_ids)){
				DB::rollBack();
				$this->errors[] = 'Failed to delete Files';
				return false;
			}

			DB::commit();
			return true;
		}catch(PDOException $e){
			DB::rollBack();    
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	/**
	 * @name attachToPost
	 *
	 * Attach gallery to post by post ID
	 * NOTE: gallery should be instantiated in $this
	 * 
	 * @param int $post_id - Array of IDs or single ID of item/items for delete
	 * @return bool - returns TRUE on success or FALSE if there was an error
	 */
	public function attachToPost($post_id){
		
		$post = Post::find($post_id);

		if(!$post){
			$this->errors[] = 'No such post';
			return false;
		}

		if(!$this->post_owners()->attach($post_id, array('type' => self::POST_ITEM_TYPE))){
			$this->errors[] = 'Failed to attach gallery to post';
			return false;
		}

		return true;

	}

	/**
	 * @name fetchItemById
	 *
	 * Fetch gallery item by its ID
	 * 
	 * @param int $post_id - Array of IDs or single ID of item/items for delete
	 * @return bool - returns TRUE on success or FALSE if there was an error
	 * @static
	 */
	public static function fetchItemById($id){
		if(empty($id)){
			return false;
		}

		return GalleryItemDB::with('file')->find($id);
	}

	/**
	 * @name get_item_by_id
	 *
	 * Fetch gallery item by its ID
	 * 
	 * @param int $post_id - Array of IDs or single ID of item/items for delete
	 * @return bool - returns TRUE on success or FALSE if there was an error
	 * @static
	 * @deprecated
	 */
	public static function get_item_by_id($id){
		if(empty($id)){
			return false;
		}

		return GalleryItemDB::find($id);
	}
}