<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;

class MenuItemDBModel extends Eloquent {



    // Settings
    //----------------------------------------------
	protected $table = 'menu_items';



    // Relationships
    // ---------------------------------------------
	public function Menu(){
		return $this->belongsTo('Vizioart\Cookbook\Models\DB\MenuDBModel');
	}
	


    // getters
    // ---------------------------------------------
}