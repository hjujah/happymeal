<?php
namespace Xml;

use Eloquent;

class Unit_floor_area extends Eloquent {

    protected $table = 'unit_floor_areas';

    protected $fillable = array(
        'unit_id',
        'unit_xml_id',
        'room_no',
        'room_id',
        'room_xml_id',
        'room_type',
        'floor_area',
    );

    // Relationships
    // ---------------------------------------------
    public function unit()
    {
        return $this->belongsTo('Xml\Unit');
    }

    public function room_type()
    {
        return $this->belongsTo('Xml\Room_type', 'room_id', 'id');
    }

    // getters
    // ---------------------------------------------
    public static function get_by_unit($id) {

        $unitFloorAreas = self::with('room_type')->where('unit_id', $id)->get();
        if (empty($unitFloorAreas)){
            return false;
        }
        $unitFloorAreas->toArray();
        return $unitFloorAreas;
    }


}
