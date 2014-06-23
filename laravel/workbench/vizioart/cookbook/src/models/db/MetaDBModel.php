<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;

class MetaDBModel extends Eloquent {



    // Settings
    //----------------------------------------------
	protected $table = 'meta';
	protected $fillable = array('meta_key', 'meta_value');


    // Relationships
    // ---------------------------------------------
	public function parent(){
		return $this->morphTo();
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