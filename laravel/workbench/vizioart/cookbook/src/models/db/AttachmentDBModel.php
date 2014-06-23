<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;

class AttachmentDBModel extends Eloquent {



    // Settings
    //----------------------------------------------
	protected $table = 'attachments';
    protected $fillable = array('parent_type', 'type', 'file_id', 'status', 'name', 'description');



    // Relationships
    // ---------------------------------------------

    public function file(){
        return $this->belongsTo('Vizioart\Cookbook\Models\DB\FileDBModel', 'file_id', 'id');
    }
	


    // getters
    // ---------------------------------------------
}