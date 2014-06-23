<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;

class MenuDBModel extends Eloquent {



    // Settings
    //----------------------------------------------
	protected $table = 'menus';



    // Relationships
    // ---------------------------------------------
	public function items(){
		return $this->hasMany('Vizioart\Cookbook\Models\DB\MenuItemDBModel', 'menu_id', 'id');
	}
	


    // getters
    // ---------------------------------------------
}