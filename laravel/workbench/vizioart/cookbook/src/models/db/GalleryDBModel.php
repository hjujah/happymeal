<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;

class GalleryDBModel extends Eloquent {



    // Settings
    //----------------------------------------------
	protected $table = 'galleries';
	protected $fillable = array('type', 'status', 'name', 'description');


    // Relationships
    // ---------------------------------------------
	public function post_owners(){
		return $this->belongsToMany('Post', 'galleries_posts', 'gallery_id', 'post_id');
	}

	public function items(){
		return $this->hasMany('Vizioart\Cookbook\Models\DB\GalleryItemDBModel', 'gallery_id', 'id');
	}



    // getters
    // ---------------------------------------------
	public function get_by_id($id){
		if(empty($id)){
			return false;
		}

		$gallery = self::with('items')->where('id', '=', $id)->first();
		if(empty($gallery)){
			return false;
		}

		return $gallery;
	}
}