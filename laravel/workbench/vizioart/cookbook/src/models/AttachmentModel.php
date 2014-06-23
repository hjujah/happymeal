<?php namespace Vizioart\Cookbook\Models;

use Vizioart\Cookbook\Models\DB\AttachmentDBModel as AttachmentDB;
use Vizioart\Cookbook\Models\FileModel as FileModel;
use Vizioart\Cookbook\Models\PostModel as Post;
use DB;

class AttachmentModel extends AttachmentDB {

	const DEFAULT_PARENT = 'post';
	const POST_ITEM_PARENT = 'post';

	const DEFAULT_TYPE = 'attachment';
	const POST_ITEM_TYPE = 'featured-image';

	/**
	 * @name make
	 *
	 * Create new attachment from scratch
	 * 
	 * @param string $parent_type [optional] - Type of the attachment parent
	 * @param string $type [optional] - Type of the attachment that is created
	 * @return bool|array - returns Eloquent model of attachment or FALSE if there was an error
	 * @static
	 */
	public static function make($parent_type = self::DEFAULT_PARENT, $type = self::DEFAULT_TYPE){
		$attachment_params = array(
			'parent_type', $parent_type,
			'type' => $type,
			'file_id' => 0,
			'status' => 'publish',
			'name' => '',
			'description' => ''
		);

		$attachment = new AttachmentModel();

		$attachment->fill($attachment_params);

		try{
			
			DB::beginTransaction();
			
			if(!$attachment->save()){
				DB::rollBack();
				$this->errors[] = 'Failed to create attachment';
				return false;
			}
			
			DB::commit();
			return $attachment;	
			
		}catch(PDOException $e){
			DB::rollBack();    
			$this->errors[] = 'Fatal error' . $e->message;    
			return false;
		}
	}

	/**
	 * @name updateSafe
	 *
	 * Save attachment (update)
	 * NOTE: attachment should be instantiated in $this
	 * 
	 * @param array $params - Array of values for attachment update
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
				$this->errors[] = 'Failed to save attachment';
				return false;
			}
			
			DB::commit();
			$this->load('file');
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
	 * Delete attachment (delete)
	 * 
	 * @param int $id - ID of attachment that will be deleted
	 * @return bool - returns TRUE on success or FALSE if there was an error
	 * @static
	 */
	public static function deleteSafe($id){
		if(empty($id)){
			return false;
		}

		try{
			
			DB::beginTransaction();
			$file_id = false;

			$attachment = self::find($id);

			if(!$attachment){
				DB::rollBack();
				$this->errors[] = 'Invalid attachment Item ID';
				return false;
			}

			if($attachment->file_id){
				$file_id = $attachment->file_id;
			}

			if(!$attachment->delete()){
				DB::rollBack();
				$this->errors[] = 'Failed to delete attachment';
				return false;
			}

			if($file_id){
				$file_model = new FileModel();
				if(!$file_model->delete_safe($file_id)){
					DB::rollBack();
					$this->errors[] = 'Failed to delete Files';
					return false;
				}
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
	 * Attach attachment to post by post ID
	 * NOTE: attachment should be instantiated in $this
	 * 
	 * @param int $post_id - ID of post to which it will be attached
	 * @return bool - returns TRUE on success or FALSE if there was an error
	 */
	public function attachToPost($post_id){
		
		$post = Post::find($post_id);

		if(!$post){
			$this->errors[] = 'No such post';
			return false;
		}

		$this->fill(array(
			'parent_id' => $post_id,
			'parent_type' => self::POST_ITEM_PARENT
		));

		try{
			
			DB::beginTransaction();
			
			if(!$this->save()){
				DB::rollBack();
				$this->errors[] = 'Failed to save attachment';
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
}