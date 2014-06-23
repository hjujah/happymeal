<?php

class Post extends Eloquent {

    protected $table = 'posts';
    protected $appends = array('children', 'date');


    // should be placed in some settings table
    const FEATURED_IMAGE_SLUG   = 'featured-image';
    const GALLERY_SLUG          = 'post-gallery';
    const POST_TYPE             = 'post';
    const PAGE_TYPE             = 'page';
    const GALLERY_PAGE_TYPE     = 'gallery-page';

    public $children;
    public $date;

    public function getChildrenAttribute(){
        if(isset($this->children)){
            if(is_object($this->children) && method_exists($this->children, 'toArray')){
                return $this->children->toArray();
            }else{
                return false;
            }
        }else{
            return false;
        } 
    }

    public function getDateAttribute(){
        if(isset($this->created_at)){
            return date ( 'd-m-Y', strtotime($this->created_at) );
        }else{
            return null;
        } 
    }

    // Relationships
    // ---------------------------------------------
    public function post_contents()
    {
        return $this->hasMany('Post_content');
    }

    public function featured_image(){
        return $this->hasOne('Attachment', 'parent_id')->where('type', '=', self::FEATURED_IMAGE_SLUG);
    }

    public function galleries(){
        return $this->belongsToMany('Gallery', 'galleries_posts', 'post_id', 'gallery_id');
    }

    // getters
    // ---------------------------------------------


    public function get_all_news($lang_code = null){
        $query = self::get_page_query();

        if(empty($lang_code)){
            $lang_code = 'cs';
        }

        $query->where('languages.id', '=', self::get_lang_by_code($lang_code));

        $query->where('posts.type', '=', self::POST_TYPE);
        $query->orderBy('post_contents.created_at', 'desc');

        $news = $query->get();

        return $news;
    }

    public function get_page_by_name($name, $lang_code = null){
        if(empty($name)){
            return false;
        }

        $query = self::get_page_query();

        $query->where('post_contents.name', '=', $name);

        if(!empty($lang_code)){
            $query->where('languages.id', '=', self::get_lang_by_code($lang_code));
        }

        $query->with('galleries.items');

        $page = $query->first();
        if (empty($page)){
            return false;
        }

        $children = self::get_page_children($page->id, $lang_code);
        $page->children = $children;

        return $page;
    }

    public function get_page_by_url($url, $lang_code = null){
        if(empty($url)){
            return false;
        }

        if(empty($lang_code)){
            $lang_code = 'cs';
        }

        $query = self::get_page_query();
        $query->where('post_contents.url', '=', $lang_code . '/' . $url);

        /* 
        if(!empty($lang_code)){
            $query->where('languages.id', '=', self::get_lang_by_code($lang_code));
        }
        */

        $query->with('galleries.items');

        $page = $query->first();

        //$queries = DB::getQueryLog();
        if (empty($page)){
            return false;
        }

        $children = $this->get_page_children($page->id, $lang_code);
        $page->children = $children;

        $siblings = $this->get_page_siblings($page->id, $lang_code);
        $page->siblings = $siblings;

        return $page;
    }

    public function get_by_id($id, $lang_code = 'cs'){
        $query = self::query();

        $lang_id = $this->get_lang_by_code($lang_code);
        if (empty($lang_id)){
            return false;
        }

        $query->with(array(
            'post_contents' => function($query) use ($lang_id){
                $query->where('language_id', '=', $lang_id )->with(array(
                    'language'
                ));
            }
        ));

        $query->where('id', $id);
        $post = $query->first();

        if(empty($post)){
            return false;
        }

        $post = $post->toArray();
        $post['children'] = $this->get_page_children($post['id'], $lang_code);

        return $post;
    }

    public function get_page_by_id($id, $lang_code = 'cs'){        
        $query = self::query();

        $lang_id = $this->get_lang_by_code($lang_code);
        if (empty($lang_id)){
            return false;
        }

        $query->with(array(
            'post_contents' => function($query) use ($lang_id){
                $query->where('language_id', '=', $lang_id )->with(array(
                    'language'
                ));
            }
        ));
        $query->where('type', 'page');
        $query->where('id', $id);
        $page = $query->first();



        if(empty($page)){
            return false;
        }

        $page = $page->toArray();
        $page['children'] = $this->get_page_children($page['id'], $lang_code);

        return $page;
    }


    public function get_page_children($id, $lang_code = null){


        if(empty($id)){
            return false;
        }

        $query = self::get_page_query();

        $query->where('posts.parent_id', '=', $id);

        if(!empty($lang_code)){
            $query->where('languages.id', '=', self::get_lang_by_code($lang_code));
        }

        $pages = $query->get();

        return $pages;

    }

    public function get_page_siblings($post, $lang_code = null){


        if(empty($post)){
            return false;
        }

        if (is_array($post)) {
            $_post = $post;
        } else {
            $_post = $this->get_by_id($post);
        }

        if (!$_post){
            return false;
        }

        $parent_id = $_post['parent_id'];
        if (!empty($parent_id)){
            $siblings = $this->get_page_children($parent_id, $lang_code);
        } else {
            $siblings = array();
        }

        // set active page
        foreach ($siblings as $page) {
            if ($_post['id'] == $page['id']){
                $page['active'] = true;
            }
        }

        return $siblings;

    }

    public function get_routes(){
        $query = self::query();

        $query->join('post_contents', 'posts.id', '=', 'post_contents.post_id');
        $query->select(
            'posts.id as id',
            'post_contents.id as content_id',
            'posts.type as type',
            'posts.view as view',
            'post_contents.url as url',
            'post_contents.language_id as language_id',
            'post_contents.name as name',
            'posts.status as post_status',
            'post_contents.status as content_status'
        );

        $routes = $query->get();

        if(empty($routes)){
            return false;
        }
        return $routes;
    }

    public function get_by_url($url){
        if(empty($url)){
            return false;
        }


        $url = rtrim($url, '/');

        $query = self::get_page_query();
        $query->where('post_contents.url', '=', $url);

        $post = $query->first();


        if($post){
            
            return $post;
        }
        return false;

    }

    private static function get_lang_by_code($code){
        $language = array(
            'cs' => 1,
            'en' => 2,
            'ru' => 3
        );
        if (array_key_exists($code, $language)){
            return $language[$code];
        } else {
            return 0;
        }
    }

    private static function get_page_query(){
        $query = self::query();

        $query->join('post_contents', 'posts.id', '=', 'post_contents.post_id');
        $query->join('languages', 'languages.id', '=', 'post_contents.language_id');
        $query->select(
            'posts.id as id',
            'posts.parent_id as parent_id',
            'posts.type as type',
            'posts.status as post_status',
            'posts.view as view',
            'posts.created_at as created_at',
            'posts.updated_at as updated_at',
            'post_contents.id as content_id',
            'post_contents.language_id as language_id',
            'post_contents.user_id as user_id',
            'post_contents.url as url',
            'post_contents.name as name',
            'post_contents.title as title',
            'post_contents.content as content',
            'post_contents.status as content_status',
            'languages.code as language_code',
            'languages.description as language'
        );

        $query->with('featured_image');

        return $query;
    }

}
