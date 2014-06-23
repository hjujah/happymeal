<?php
use XML\Building_object;

class BuildingController extends \BaseController {

    /**
     *
     */
    public function getIndex(){

    }

    /**
     *
     */
    public function getBuilding($id = null){

        if (!empty($id)) {

            $model = new Building_object();

            if($res = $model->find($id)){
                return Response::json($res, 200);
            }else{
                return Response::json(
                    array(
                        'error' => array(
                            'message' => "Please specify building id.", 
                            'type' => "InvalidParameters", 
                            'code' => 100 
                        )
                    ),
                    400
                );
            }
        } else {
            // no $id specified - return appropriate error
            return Response::json(
                array(
                    'error' => array(
                        'message' => "Please specify building id.", 
                        'type' => "InvalidParameters", 
                        'code' => 100 
                    )
                ),
                400
            );
        }
    }
    
    /**
     *
     */
    public function getUnits($id = null){
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