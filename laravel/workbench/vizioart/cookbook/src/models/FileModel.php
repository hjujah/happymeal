<?php namespace Vizioart\Cookbook\Models;

use Vizioart\Cookbook\Models\DB\FileDBModel as FileDB;
use Illuminate\Support\Facades\File;
use DB;


class FileModel extends FileDB {

	const DEFAULT_TYPE = 'image';

	public function insert($params){
		
		$default_params = array(
			'type' => self::DEFAULT_TYPE,
			'name' => null,
			'size' => 0,
			'url' => null,
			'user_id' => 1, // @CHANGE
			'extension' => ''
		);

		$params = array_replace_recursive($default_params, $params);

		$this->fill($params);

		try{
			
			DB::beginTransaction();
			if(!$this->save()){
				DB::rollBack();
				$this->errors[] = 'Failed to insert File';
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

	public function delete_safe($ids){
		if(empty($ids)){
			return false;
		}

		try{
			
			DB::beginTransaction();

			if(!is_array($ids)){
				$ids = array($ids);
			}
			foreach ($ids as $id) {
				$file = self::find($id);
				if(!$file){
					DB::rollBack();
					$this->errors[] = 'Invalid File ID';
					return false;
				}

				if(!$file->delete()){
					DB::rollBack();
					$this->errors[] = 'Failed to delete File';
					return false;
				}
			}

			if(File::exists(public_path() . '/uploads/test/' . $file->url)){
				File::delete(public_path() . '/uploads/test/' . $file->url);
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