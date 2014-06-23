<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;

class GalleryItemDBModel extends Eloquent {



    // Settings
    //----------------------------------------------
	protected $table = 'gallery_items';
	protected $fillable = array('type', 'status', 'file_id', 'name', 'description', 'content');


    // Relationships
    // ---------------------------------------------
	public function gallery(){
		return $this->belongsTo('Vizioart\Cookbook\Models\DB\GalleryDBModel');
	}

	public function file(){
		return $this->belongsTo('Vizioart\Cookbook\Models\DB\FileDBModel', 'file_id', 'id');
	}



    // getters
    // ---------------------------------------------
	public function get_by_id($id){
		if(empty($id)){
			return false;
		}

		$item = self::where('id', '=', $id)->first();
		if(empty($item)){
			return false;
		}

		return $item;
	}
}