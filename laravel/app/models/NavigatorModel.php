<?php 

use Xml\Building_object as Building_object;
use Xml\Unit as Unit;
use Xml\Unit_floor_area;
use Xml\XmlImage;
use Xml\Floor_code;
use Xml\Room_type;
use Xml\Unit_type;


class NavigatorModel {

	// building_object model 
	protected $building_object;

	// unit model 
	protected $unit;



	// public function __construct(Building_object $building_object){
	// 	$this->building_object = $building_object;
	// }
	public function __construct(){
		$this->building_object = new Building_object;
		$this->unit = new Unit;
	}

	/**
	 *
	 */
	public function getBuildings($params = array()){

		// filters
		$filters = array(
			'job_id' 		=> array(),
			'exclude_job_ids'	=> array(),
		);

		// @TO_DO - validation
		$filters = array_merge($filters, $params);


		$query = $this->building_object->newQuery();
		
		$query = $query->select('*')
			  ->with('xmlimages')
			  ->with(array('floors' => function($query){
			  		$query->with(array('floor_code', 'xmlimages'))
			  			  ->with(array('units' => function($query){
								$query->with('unit_type', 'unit_floor_areas', 'xmlimages');
							}));
			  	}));

		if(!empty($filters['job_id'])){
			$query = $query->whereIn('job_id', $filters['job_id']);
		}
		
		if(!empty($filters['exclude_job_ids'])){
			$query = $query->whereNotIn('xml_id', $filters['exclude_job_ids']);
		}

		return $query->get();
	}


	public function getUnit($id){
        // check languages
        if (empty($id)){
            return $this->unit->get_all();
        }

        if(intval($id) > 0){
            return $this->unit->get_by_id($id);
        }else{
            return $this->unit->get_by_xml_id($id);
        }

	}



}