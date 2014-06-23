<?php
namespace Xml;

use Eloquent;

class Unit extends Eloquent {

    protected $table = 'units';

    protected $fillable = array(
        'xml_id',
        'job_id',
        'building_object_id',
        'building_object_xml_id',
        'floor_code_id',
        'floor_code_xml_id',
        'building_floor_id',
        'status',
        'layout_variant',
        'unit_sales_area',
        'sales_price',
        'unit_type_id',
        'unit_type_xml_id',
        'garden_area',
        'terrace_area',
        'for_rent',
        'rented',
        'rental_price',
        'note_for_web'
    );


    // Relationships
    // ---------------------------------------------
    public function unit_type(){
        return $this->belongsTo('Xml\Unit_type');
    }

    public function floor_code(){
        return $this->belongsTo('Xml\Floor_code');
    }

    public function unit_floor_areas(){
        return $this->hasMany('Xml\Unit_floor_area');
    }

    public function floor(){
        return $this->belongsTo('Xml\Building_floor');
    }

    public function building_object(){
        return $this->belongsTo('Xml\Building_object');
    }

    public function xmlimages(){
        return $this->morphMany('Xml\XmlImage', 'imageable', 'imageable_type', 'xml_code', 'xml_id');
    }

    // getters
    // ---------------------------------------------
    
    public function get_all(){
        $query = self::with(array( 'unit_type', 'floor_code','unit_floor_areas.room_type', 'xmlimages'))
                     ->get();

        return $query;
    }

    public function get_by_job_id($job_id){
        $query = self::select('*')
                     ->where('job_id', $job_id)
                     ->where('unit_type_xml_id', 'R-BYT')
                     ->where('status', 0)
                     ->with(array('unit_type', 'floor_code', 'unit_floor_areas.room_type', 'xmlimages'))
                     ->get();

        return $query;
    }

    public function get_by_building_id($id){
        $query = self::with(array('unit_type', 'floor_code', 'unit_floor_areas.room_type', 'xmlimages'))
                     ->where('building_object_id', $id)
                     ->get();

        return $query;   
    }

    public function get_by_id($id){
        $query = self::with(array('unit_type', 'floor_code', 'unit_floor_areas.room_type', 'xmlimages'))
                     ->where('id', $id)
                     ->first();
        
        return $query;
    }

    public function get_by_xml_id($id){
        $query = self::with(array('unit_type', 'floor_code', 'unit_floor_areas.room_type', 'xmlimages'))
                     ->where('xml_id', $id)
                     ->first();

        return $query;
    }
    
}
