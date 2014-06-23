<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;


class FileDBModel extends Eloquent {



    // Settings
    //----------------------------------------------
	protected $table = 'files';
	protected $fillable = array('type', 'name', 'size', 'url', 'user_id', 'extension');




    // Relationships
    // ---------------------------------------------

	

    // getters
    // ---------------------------------------------
}