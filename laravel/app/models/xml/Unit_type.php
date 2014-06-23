<?php
namespace Xml;
use Eloquent;

class Unit_type extends Eloquent {

    protected $table = 'unit_types';

    protected $fillable = array(
        'xml_id',
        'description'
    );

    // Relationships
    // ---------------------------------------------
    public function unit()
    {
        return $this->belongsTo('Xml\Unit', 'id', 'unit_type_code');
    }
}
