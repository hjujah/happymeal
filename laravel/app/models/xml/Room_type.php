<?php
namespace Xml;

use Eloquent;

class Room_type extends Eloquent {

    protected $table = 'room_types';

    protected $fillable = array(
        'xml_id',
        'description'
    );

    // Relationships
    // ---------------------------------------------
    public function unit_floor_area()
    {
        return $this->hasMany('Xml\Unit_floor_area');
    }

}
