<?php
use Xml\Building_object;
use Xml\Floor_code;
use Xml\Room_type;
use Xml\Unit;
use Xml\Unit_floor_area;
use Xml\Unit_type;
use Xml\XmlImage;

class NavigatorController extends \BaseController {

	public function __construct()
	{
		set_time_limit(30 * 60);
		// $this->$arrayOfId = array();
	}

	public function getIndex(){

		// filters
		$params = array(
			'job_id'			=> array('PROJEKT-0025/01'),
			'exclude_job_ids'	=> array('DOCK Z1-3 NAKLADOVY'),
		);

		$navigatorModel = new NavigatorModel;
		$buildings = $navigatorModel->getBuildings($params);

		if (empty($buildings)){
			// return error
			return Response::json('No buildings found', 404);
		}
		$buildings = $buildings->toArray();

		return Response::json($buildings, 200);
	}

	public function getPhase($phase_name){

		$debug = false;

		// phase name def
        $phase_names_arr = array(
            'riverwatch' => array(
                'phase' => 2,
                'job_id' => 'PROJEKT-0025/01'
            ),
            'marinaview' => array(
                'phase' => 1,
                'job_id' => 'PROJEKT-0003/01'
            ),
        );

        $job_id = false;
        if (!empty($phase_name)){
            if (array_key_exists($phase_name, $phase_names_arr)){
                $job_id = $phase_names_arr[$phase_name]['job_id'];
            }
        }

        if (!$job_id){
            return Response::json(
                array(
                    'error' => array(
                        'message' => "Please specify correct phase name.", // error msg (ie. 'You dont have a permission to...')
                        'type' => "BadRequest", // type of error (ie. 'AuthException')
                        'code' => 100 // error code, we'll define this later...
                    )
                ),
                400 // serve it with appropriate http headers code
            );
        }
        $model = new Unit;
		$units = $model->get_by_job_id($job_id, $debug);
		return Response::json($units, 200);
	}


}