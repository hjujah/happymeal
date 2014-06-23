<?php namespace Vizioart\Cookbook\Models\DB;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Validation\Validator;

class PostContentDBModel extends Eloquent {

    // Settings
    //----------------------------------------------
    protected $table = 'post_contents';
    
    protected $fillable = array('language_id', 'user_id', 'url', 'name', 'type', 'status', 'title', 'content');

    // ---------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------
    // @TO_DO : Abstract this to base model 
    // ---------------------------------------------------------------------------------------    

    /**
     * Error message bag
     * 
     * @var Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Validation rules
     * 
     * @var Array
     */
    protected static $rules = array();

    /**
     * Validator instance
     * 
     * @var Illuminate\Validation\Validators
     */
    protected $validator;


    public function __construct(array $attributes = array(), Validator $validator = null) {
        parent::__construct($attributes);

        $this->validator = $validator ?: \App::make('validator');
    }

    /**
     * Listen for save event
     */
    protected static function boot(){
        parent::boot();

        static::saving(function($model) {
            return $model->validate();
        });
    }


    /**
     * Validates current attributes against rules
     */
    public function validate()
    {
        $v = $this->validator->make($this->attributes, static::$rules);

        if ($v->passes())
        {
            return true;
        }

        $this->setErrors($v->messages());

        return false;
    }

    /**
     * Set error message bag
     * 
     * @var Illuminate\Support\MessageBag
     */
    protected function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Retrieve error message bag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Inverse of wasSaved
     */
    public function hasErrors()
    {
        return ! empty($this->errors);
    }

    // ---------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------



    // Relationships
    // ---------------------------------------------
    public function post() {
        return $this->belongsTo('Vizioart\Cookbook\Models\DB\PostDBModel', 'post_id', 'id');
    }

    public function language() {
        return $this->belongsTo('Vizioart\Cookbook\Models\DB\LanguageDBModel');
    }

    public function meta(){
        return $this->morphMany('Vizioart\Cookbook\Models\DB\MetaDBModel', 'parent');
    }



    // getters
    // ---------------------------------------------



    // ACCESSORS AND MUTATORS


    public function toArray(){
        $array = parent::toArray();
        if(array_key_exists('meta', $array)){
            $array['meta'] = $this->sortMeta($array['meta']);
        }else{
            $array['meta'] = false;
        }
        
        return $array;
    }

    protected function sortMeta($meta){
        if(!empty($meta)){
            if(is_array($meta)){
                $meta_object = array();
                foreach ($meta as $meta_row) {
                    # code...
                    if(array_key_exists($meta_row['meta_key'], $meta_object)){
                        if(!is_array($meta_object[$meta_row['meta_key']])){
                            $meta_object[$meta_row['meta_key']] = array($meta_object[$meta_row['meta_key']], $meta_row['meta_value']);
                        }else{
                            $meta_object[$meta_row['meta_key']][] = $meta_row['meta_value'];
                        }
                    }else{
                        $meta_object[$meta_row['meta_key']] = $meta_row['meta_value'];
                    }
                }
            }else if(is_object($meta)){
                $meta_object = new \stdClass();
                foreach ($meta as $meta_row) {
                    # code...
                    if(property_exists($meta_object, $meta_row->meta_key)){
                        if(!is_array($meta_object->$meta_row->meta_key)){
                            $meta_object->$meta_row->meta_key = array($meta_object->$meta_row->meta_key, $meta_row->meta_value);
                        }else{
                            $meta_object->$meta_row->meta_key[] = $meta_row->meta_value;
                        }
                    }else{
                        $meta_object->$meta_row->meta_key = $meta_row->meta_value;
                    }
                }
            }else{
                $meta_object = false;
            }

            return $meta_object;

        }else{
            return false;
        }
    }


}
