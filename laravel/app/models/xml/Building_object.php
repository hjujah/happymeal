<?php namespace Xml;

use Eloquent;

class Building_object extends Eloquent {

    protected $table = 'building_objects';

    protected $fillable = array(
    	'xml_id',
    	'job_id',
    	'description'
    );


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

    // Relationships
    // ---------------------------------------------
	public function units(){
        return $this->hasMany('Xml\Unit');
    }

    public function floors(){
        return $this->hasMany('Xml\Building_floor');
    }

    public function xmlimages(){
        return $this->morphMany('Xml\XmlImage', 'imageable', 'imageable_type', 'xml_code', 'xml_id');
    }


    // Methods
    // ---------------------------------------------

    public function __construct(array $attributes = array(), Validator $validator = null) {
        parent::__construct($attributes);
        $this->validator = $validator ?: \App::make('validator');
    }

    /**
     * Listen for save event
     */
    protected static function boot() {
        parent::boot();

        static::saving(function($model) {
            return $model->validate();
        });
    }

    /**
     * Validates current attributes against rules
     */
    public function validate() {

        // temp @CHANGE !!!
        if (empty($this->job_slug)){
            $this->setJobSlug();
        }

        $v = $this->validator->make($this->attributes, static::$rules);

        if ($v->passes()) {
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
    protected function setErrors($errors) {
        $this->errors = $errors;
    }

    /**
     * Retrieve error message bag
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Inverse of wasSaved
     */
    public function hasErrors() {
        return ! empty($this->errors);
    }



    public function setJobSlug(){

        $this->job_slug = $this->sanitize_job_id($this->job_id);
    }


    private function sanitize_job_id( $string ) {
        
        $string = strtolower($string);
        $string = str_replace('/', '-', $string);
        $string = preg_replace('/[^%a-z0-9 _-]/', '', $string);
        $string = preg_replace('/\s+/', '-', $string);
        $string = preg_replace('|-+|', '-', $string);
        $string = trim($string, '-');

        return $string;
    }


}
