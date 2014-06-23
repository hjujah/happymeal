<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PostDBModel extends Eloquent {



    // Settings
    //----------------------------------------------
    protected $table = 'posts';
    protected $fillable = array('parent_id', 'type', 'status', 'view', 'archive_type');


    // Relationships
    // ---------------------------------------------
    public function post_contents()
    {
        return $this->hasMany('Vizioart\Cookbook\Models\DB\PostContentDBModel', 'post_id', 'id');
    }

    public function featured_image(){
        return $this->hasOne('Vizioart\Cookbook\Models\DB\AttachmentDBModel', 'parent_id', 'id')->where('parent_type', '=', 'post')->where('type', '=', 'featured-image');
    }

    public function galleries(){
        return $this->belongsToMany('Vizioart\Cookbook\Models\DB\GalleryDBModel', 'galleries_posts', 'post_id', 'gallery_id');
    }

    public function post_meta(){
        return $this->morphMany('Vizioart\Cookbook\Models\DB\MetaDBModel', 'parent');
    }



    // getters
    // ---------------------------------------------


}
