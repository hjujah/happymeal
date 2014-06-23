<?php
namespace Xml;

use Eloquent;

class Floor_code extends Eloquent {

    protected $table = 'floor_codes';

    protected $fillable = array(
        'xml_id',
        'description'
    );

}
