<?php

use Xml\Unit;
use Xml\Unit_floor_area;

class UnitController extends \BaseController {

    /**
     *  
     */
    public function getIndex(){

    }

    public function getUnit($id = '') {

        $model = new NavigatorModel;
        $unit = $model->getUnit($id);
        
        if($unit){
            return Response::json($unit, 200);
        }else{
            return Response::json(
                array(
                    'error' => array(
                        'message' => "Please specify valid unit id.", // error msg (ie. 'You dont have a permission to...')
                        'type' => "InvalidParameters", // type of error (ie. 'AuthException')
                        'code' => 100 // error code, we'll define this later...
                    )
                ),
                400 // serve it with appropriate http headers code
            );
        }

    }


    /**
     *
     */
    public function getFloorAreas($id = null){

        if (!empty($id)) {
            // try to get by unit id
            $model = new Unit_floor_area();
            if($unitFloorArea = $model->get_by_unit($id)){
                return Response::json($unitFloorArea, 200);
            }else{
                return Response::json(
                    array(
                        'error' => array(
                            'message' => "Please specify unit id.", // error msg (ie. 'You dont have a permission to...')
                            'type' => "InvalidParameters", // type of error (ie. 'AuthException')
                            'code' => 100 // error code, we'll define this later...
                        )
                    ),
                    400 // serve it with appropriate http headers code
                );
            }
        } else {
            // no $id specified - return appropriate error
            return Response::json(
                array(
                    'error' => array(
                        'message' => "Please specify unit id.", // error msg (ie. 'You dont have a permission to...')
                        'type' => "InvalidParameters", // type of error (ie. 'AuthException')
                        'code' => 100 // error code, we'll define this later...
                    )
                ),
                400 // serve it with appropriate http headers code
            );
        }

    }

    /**
     *
     */
    public function getBuilding($id = null){

        if (!empty($id)) {

            $model = new Unit();

            if($units = $model->where('building_object_id', $id)->get()){
                $units = $units->toArray();
                return Response::json($units, 200);
            }else{
                return Response::json(
                    array(
                        'error' => array(
                            'message' => "Please specify building id.", // error msg (ie. 'You dont have a permission to...')
                            'type' => "InvalidParameters", // type of error (ie. 'AuthException')
                            'code' => 100 // error code, we'll define this later...
                        )
                    ),
                    400 // serve it with appropriate http headers code
                );
            }
        } else {
            // no $id specified - return appropriate error
            return Response::json(
                array(
                    'error' => array(
                        'message' => "Please specify building id.", // error msg (ie. 'You dont have a permission to...')
                        'type' => "InvalidParameters", // type of error (ie. 'AuthException')
                        'code' => 100 // error code, we'll define this later...
                    )
                ),
                400 // serve it with appropriate http headers code
            );
        }
    }


}