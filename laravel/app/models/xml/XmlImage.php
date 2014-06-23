<?php
namespace Xml;
use Eloquent;

class XmlImage extends Eloquent {

    protected $table = 'xmlimages';


    // Relationships
    // ---------------------------------------------

    public function imageable() {
        return $this->morphTo();
    }

}
