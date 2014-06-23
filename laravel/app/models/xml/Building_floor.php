<?php
namespace Xml;

use Eloquent;

class Building_floor extends Eloquent {

    protected $table = 'building_floors';

    protected $fillable = array(
        'building_object_id',
        'floor_code_id'
    );

    public function building_object(){
        return $this->belongsTo('Xml\Building_object');
    }

    public function floor_code(){
        return $this->belongsTo('Xml\Floor_code');
    }

    public function units(){
        return $this->hasMany('Xml\Unit');
    }

    public function xmlimages(){
        return $this->morphMany('Xml\XmlImage', 'imageable', 'imageable_type', 'imageable_id', 'id');
    }

}
