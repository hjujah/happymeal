<?php
use Xml\Building_object;
use Xml\Building_floor;
use Xml\Floor_code;
use Xml\Room_type;
use Xml\Unit;
use Xml\Unit_floor_area;
use Xml\Unit_type;
use Xml\XmlImage;


class XMLImporterController extends \BaseController {

	protected $xml_v = 'v3';
	protected $xml_files = array(
		'building_objects' 	=> 'MASTER-CZ-BuildingObjects.xml',
		'units' 			=> 'MASTER-CZ-Units.xml',
		'unit_types' 		=> 'MASTER-CZ-UnitTypes.xml',
		'floor_codes' 		=> 'MASTER-CZ-FloorCodes.xml',
		'unit_floor_areas' 	=> 'MASTER-CZ-UnitFloorAreas.xml',
		'room_types' 		=> 'MASTER-CZ-RoomTypes.xml'
	);

	/**
	 * This will be changed, so it could be passed in constructor or by params 
	 */
	protected $filters = array(
		'job_id' => array(
			'PROJEKT-0025/01',
			'PROJEKT-0003/01'
		),
	);

	protected $messages = array();
	protected $debug = array();


	protected $buliding_mapper = array();
	protected $unit_mapper = array();
	protected $unit_type_mapper = array();
	protected $floors_mapper = array();
	protected $room_type_mapper = array();
	protected $unit_floor_area_mapper = array();

	// derived values
	protected $buliding_floors = array();

	public function __construct() {
		// increase script execution time limit 
		set_time_limit(30 * 60);
	}

	public function getTruncateTables(){
		
		foreach ($this->xml_files as $key => $value) {
		   DB::table($key)->truncate();
		}
		// truncate building_floors (derivate) table
		DB::table('building_floors')->truncate();
		
		$this->setMessage('All XML tables truncated successfully.');
		
		return Response::json(array(
			'msg' => $this->messages
		), 200);

	}

	/**
	 * XML Import functions.
	 *
	 * @return Response
	 */
	public function getImport()	{
		// import all XML files into DB

		// $string = 'PROJEKT-0025/01';
		// $string = $this->sanitize_string_with_dashes($string);
		// echo $string;
		// die();
		
		// backup xml files 
		// --------------------------------------------
		$this->backupXMLFiles();
		// --------------------------------------------
		
		try{
			$this->importBuildingObjects();
		} catch (Exception $e) {
			$this->setMessage($e->getMessage() . ' (building_objects)');
		}

		try{
			$this->importFloorCodes();
		} catch (Exception $e) {
			$this->setMessage($e->getMessage() . ' (floor_codes)');
		}

		try{
			$this->importUnitTypes();
		} catch (Exception $e) {
			$this->setMessage($e->getMessage() . ' (unit_types)');
		}

		try{
			$this->importRoomTypes();
		} catch (Exception $e) {
			$this->setMessage($e->getMessage() . ' (room_types)');
		}

		try{
			$this->importUnits();
		} catch (Exception $e) {
			$this->setMessage($e->getMessage() . ' (units)');
		}

		try{
			$this->importUnitFloorAreas();
		} catch (Exception $e) {
			$this->setMessage($e->getMessage() . ' (unit_floor_areas)');
		}


		// @TO_DO - create controller method for returning response
		return Response::json(array(
			'msg' => $this->messages,
			'debug' => $this->debug
		), 200);
	}



	public function importBuildingObjects(){

		$file = $this->xml_files['building_objects'];
		
		// debug object
		$report = array(
			'xml_items_count' => 0,
			'filtered_items_count' => 0,
			'imported_items_ids' => array(),
			'total_time' => 0
		);
		$reportStart = microtime(true);

		// Callback function 
		// ==============================================
		$iteratorCallback = function($entry) use (&$report) {

			$report['xml_items_count']++;

			// filter - job_id list to be imported 
			// ==============================================
			$doImport = $this->filters['job_id'];
			$xmlProp = (property_exists($entry, 'Job_No')) ? (string)$entry->Job_No : null;
			if (empty($xmlProp) || !in_array($xmlProp, $doImport)) {
				return;
			}

			$report['filtered_items_count']++;

			$modelData = array();
			$map = array(
				'No' => 'xml_id',
				'Job_No' => 'job_id',
				'Description' => 'description'
			);

			foreach ( $map as $key => $value) {
				if (property_exists($entry, $key)){
					$modelData[$value] = (string)$entry->$key;
				}
			}

			$report['imported_items_ids'][] = $modelData['xml_id'];

			$obj = Building_object::where('xml_id', $modelData['xml_id'])->first();
			if (empty($obj)){
				$obj = new Building_object($modelData);
			} else {
				$modified_old = $obj->updated_at;
				$obj->fill($modelData);
			}
			$obj->save();

			$this->buliding_mapper[$obj->xml_id] = $obj->id;

			// get status - insert | update | nochange"
			if (isset($modified_old)){
				if ($modified_old != $obj->updated_at) {
					$status = 'update';
				} else {
					$status = 'nochange';
				}
			} else {
				$status = 'insert';
			}

		};

		// xml iterator 
		// ==============================================
		$this->importXML($file, $iteratorCallback);

		// Delete db row that aren't in new xml
		// ==============================================
		if  (count($report['imported_items_ids']) > 0){
			$delete_start = microtime(true);
			
			Building_object::whereNotIn('xml_id', $report['imported_items_ids'])->delete();

			$delete_end = microtime(true);
			$delete_time = $delete_end - $delete_start;
			$this->setMessage('(building_objects) delete time : ' . $delete_time );
		}

		// Debug object 
		// ==============================================
		$reportEnd = microtime(true);
		$report['total_time'] = $reportEnd - $reportStart;
		$this->setDebug('building_objects', $report);

	}

	public function importUnits(){

		$file = $this->xml_files['units'];

		// debug object
		$report = array(
			'xml_items_count' => 0,
			'filtered_items_count' => 0,
			'imported_items_ids' => array(),
			'imported_items_change_log' => array(
				'new' => 0,
				'update' => 0,
				'none' => 0
			),
			'missing_items_count' => 0,
			'missing_items_update_count' =>0,
			'missing_items_update_ids' => array(),

			'total_time' => 0
		);
		$reportStart = microtime(true);

		// Callback function 
		// ==============================================
		$iteratorCallback = function($entry) use (&$report) {

			$report['xml_items_count']++;

			// filter - job_id list to be imported 
			// ==============================================
			$doImport = $this->filters['job_id'];
			$xmlProp = (property_exists($entry, 'Job_No')) ? (string)$entry->Job_No : null;
			if (empty($xmlProp) || !in_array($xmlProp, $doImport)) {
				return;
			}

			$report['filtered_items_count']++;
			
			$modelData = array();
			$map = array(
				'No'                    => 'xml_id',
				'Job_No'                => 'job_id',
				'Building_Object_No'    => 'building_object_xml_id',
				'Floor'                 => 'floor_code_xml_id',
				'Status'                => 'status',
				'Layout_Variant'        => 'layout_variant',
				'Unit_Sales_Area'       => 'unit_sales_area',
				'Sales_Price'           => 'sales_price',
				'Unit_Type_Code'        => 'unit_type_xml_id',
				'Garden_Area'           => 'garden_area',
				'Terrace_Area'          => 'terrace_area',
				'For_Rent'              => 'for_rent',
				'Rented'                => 'rented',
				'Rental_Price'          => 'rental_price',
				'Note_For_Web'          => 'note_for_web'
			);

			foreach ( $map as $key => $value) {
				if (property_exists($entry, $key)){
					$modelData[$value] = (string)$entry->$key;
				}
			}

			// @EXPLAIN !!!
			if(empty($modelData['layout_variant'])){
				return;
			}

			$report['imported_items_ids'][] = $modelData['xml_id'];


			$modelData['building_object_id'] = $this->buliding_mapper[$modelData['building_object_xml_id']];
			$modelData['unit_type_id'] = $this->unit_type_mapper[$modelData['unit_type_xml_id']];
			$modelData['floor_code_id'] = $this->floor_code_mapper[$modelData['floor_code_xml_id']];


			$building_floor = Building_floor::where('building_object_id', '=', $modelData['building_object_id'])
											->where('floor_code_id', '=', $modelData['floor_code_id'])
											->first();
			if(empty($building_floor)){
				$building_floor = new Building_floor();
				$building_floor->fill(array(
					'building_object_id' => $modelData['building_object_id'],
					'floor_code_id' => $modelData['floor_code_id']
				));
				$building_floor->save();
			}

			$modelData['building_floor_id'] = $building_floor->id;

			$obj = Unit::where('xml_id', $modelData['xml_id'])->first();
			if (empty($obj)){
				$obj = new Unit($modelData);
			} else {
				$modified_old = $obj->updated_at;
				$obj->fill($modelData);
			}
			$obj->save();

			$this->unit_mapper[$obj->xml_id] = $obj->id;

			// get status - insert | update | nochange"
			if (isset($modified_old)){
				if ($modified_old != $obj->updated_at) {
					$status = 'update';
					$report['imported_items_change_log']['update']++;
				} else {
					$status = 'nochange';
					$report['imported_items_change_log']['none']++;
				}
			} else {
				$status = 'insert';
				$report['imported_items_change_log']['new']++;
			}

		};

		// xml iterator 
		// ==============================================
		$this->importXML($file, $iteratorCallback);

		// Delete db row that aren't in new xml
		// ==============================================
		if  (count($report['imported_items_ids']) > 0){
			$delete_start = microtime(true);
			

			//Unit::whereNotIn('xml_id', $report['imported_items_ids'])->delete();

			$report['missing_items_count'] = Unit::whereNotIn('xml_id', $report['imported_items_ids'])->count();

			Unit::whereNotIn('xml_id', $report['imported_items_ids'])
				->update(array('status' => 3));

			$delete_end = microtime(true);
			$delete_time = $delete_end - $delete_start;
			$this->setMessage('(units) delete time : ' . $delete_time );
		}

		// Debug object 
		// ==============================================
		$reportEnd = microtime(true);
		$report['total_time'] = $reportEnd - $reportStart;
		$this->setDebug('units', $report);

	}

	public function importUnitTypes(){

		$file = $this->xml_files['unit_types'];
		
		// debug object
		$report = array(
			'xml_items_count' => 0,
			'filtered_items_count' => 0,
			'imported_items_ids' => array(),
			'total_time' => 0
		);
		$reportStart = microtime(true);

		// Callback function 
		// ==============================================
		$iteratorCallback = function($entry) use (&$report) {

			$report['xml_items_count']++;

			$modelData = array();
			$map = array(
				'Code' => 'xml_id',
				'Description' => 'description'
			);

			foreach ( $map as $key => $value) {
				if (property_exists($entry, $key)){
					$modelData[$value] = (string)$entry->$key;
				}
			}

			$report['imported_items_ids'][] = $modelData['xml_id'];

			$obj = Unit_type::where('xml_id', '=', $modelData['xml_id'])->first();
			if (empty($obj)){
				$obj = new Unit_type($modelData);
			} else {
				$modified_old = $obj->updated_at;
				$obj->fill($modelData);
			}
			$obj->save();

			$this->unit_type_mapper[$obj->xml_id] = $obj->id;

			// get status - insert | update | nochange"
			if (isset($modified_old)){

				if ($modified_old != $obj->updated_at) {
					$status = 'update';
				} else {
					$status = 'unchanged';
				}
			} else {
				$status = 'insert';
			}

		};

		// xml iterator 
		// ==============================================
		$this->importXML($file, $iteratorCallback);

		// Delete db row that aren't in new xml
		// ==============================================
		if  (count($report['imported_items_ids']) > 0){
			$delete_start = microtime(true);
			
			Unit_type::whereNotIn('xml_id', $report['imported_items_ids'])->delete();

			$delete_end = microtime(true);
			$delete_time = $delete_end - $delete_start;
			$this->setMessage('(unit_types) delete time : ' . $delete_time );
		}

		// Debug object 
		// ==============================================
		$reportEnd = microtime(true);
		$report['total_time'] = $reportEnd - $reportStart;
		$this->setDebug('unit_types', $report);

	}

	public function importFloorCodes(){

		$file = $this->xml_files['floor_codes'];

		// debug object
		$report = array(
			'xml_items_count' => 0,
			'filtered_items_count' => 0,
			'imported_items_ids' => array(),
			'total_time' => 0
		);
		$reportStart = microtime(true);

		// Callback function 
		// ==============================================
		$iteratorCallback = function($entry) use (&$report) {

			$report['xml_items_count']++;

			$modelData = array();
			$map = array(
				'Code' => 'xml_id',
				'Description' => 'description_cs'
			);
			foreach ( $map as $key => $value) {
				if (property_exists($entry, $key)){
					$modelData[$value] = (string)$entry->$key;
				}
			}

			$report['imported_items_ids'][] = $modelData['xml_id'];

			$obj = Floor_code::where('xml_id', $modelData['xml_id'])->first();
			if (empty($obj)){
				$obj = new Floor_code($modelData);
			} else {
				$modified_old = $obj->updated_at;
				$obj->fill($modelData);
			}
			$obj->save();

			$this->floor_code_mapper[$obj->xml_id] = $obj->id;

			// get status - insert | update | nochange"
			if (isset($modified_old)){

				if ($modified_old != $obj->updated_at) {
					$status = 'update';
				} else {
					$status = 'unchanged';
				}
			} else {
				$status = 'insert';
			}

		};

		// xml iterator 
		// ==============================================
		$this->importXML($file, $iteratorCallback);

		// Delete db row that aren't in new xml
		// ==============================================
		if  (count($report['imported_items_ids']) > 0){
			$delete_start = microtime(true);
			
			Floor_code::whereNotIn('xml_id', $report['imported_items_ids'])->delete();

			$delete_end = microtime(true);
			$delete_time = $delete_end - $delete_start;
			$this->setMessage('(floor_codes) delete time : ' . $delete_time );
		}

		// Debug object 
		// ==============================================
		$reportEnd = microtime(true);
		$report['total_time'] = $reportEnd - $reportStart;
		$this->setDebug('floor_codes', $report);

	}

	public function importUnitFloorAreas(){

		$file = $this->xml_files['unit_floor_areas'];

		// debug object
		$report = array(
			'xml_items_count' => 0,
			'filtered_items_count' => 0,
			'imported_items_ids' => array(),
			'total_time' => 0
		);
		$reportStart = microtime(true);

		$units_count =  DB::table('units')->count();
		if (!$units_count){
			$this->setMessage('No units in db, skiped importing unit_floor_areas');
			return;
		}

		// Callback function 
		// ==============================================
		$iteratorCallback = function($entry) use (&$report) {

			$report['xml_items_count']++;

			// filter 
			// ==============================================
			$doImport =  DB::table('units')->lists('xml_id');
			$xmlProp = (property_exists($entry, 'Unit_No')) ? (string)$entry->Unit_No : null;
			if (empty($xmlProp) || !in_array($xmlProp, $doImport)) {
				return;
			}

			$report['filtered_items_count']++;

			$modelData = array();
			$map = array(
				'Unit_No' => 'unit_xml_id',
				'Room_No' => 'room_no',
				'Room_Code' => 'room_code',
				'Room_Type' => 'room_xml_id',
				'Floor_Area' => 'floor_area',
			);

			foreach ( $map as $key => $value) {
				if (property_exists($entry, $key)){
					$modelData[$value] = (string)$entry->$key;
				}
			}

			if(array_key_exists($modelData['unit_xml_id'], $this->unit_mapper)){
				$modelData['unit_id'] = $this->unit_mapper[$modelData['unit_xml_id']];
			}else{
				return;
			}

			if(array_key_exists($modelData['room_xml_id'], $this->room_type_mapper)){
				$modelData['room_id'] = $this->room_type_mapper[$modelData['room_xml_id']];
			}

			$obj = Unit_floor_area::where( 'unit_xml_id', $modelData['unit_xml_id'])->where('room_no', $modelData['room_no'])->first();
			if (empty($obj)){
				$obj = new Unit_floor_area($modelData);
			} else {
				$modified_old = $obj->updated_at;
				$obj->fill($modelData);
			}
			$obj->save();

			$report['imported_items_ids'][] = $obj->id;

			// get status - insert | update | nochange"
			if (isset($modified_old)){
				if ($modified_old != $obj->updated_at) {
					$status = 'update';
				} else {
					$status = 'nochange';
				}
			} else {
				$status = 'insert';
			}

		};

		// xml iterator 
		// ==============================================
		$this->importXML($file, $iteratorCallback);

		// Delete db row that aren't in new xml
		// ==============================================
		// if (count($report['imported_items_ids']) > 0){
		// 	$delete_start = microtime(true);
			
		// 	Unit_floor_area::whereNotIn('id', $report['imported_items_ids'])->delete();

		// 	$delete_end = microtime(true);
		// 	$delete_time = $delete_end - $delete_start;
		// 	$this->setMessage('(unit_floor_areas) delete time : ' . $delete_time );
		// }

		// Debug object 
		// ==============================================
		$reportEnd = microtime(true);
		$report['total_time'] = $reportEnd - $reportStart;
		$this->setDebug('unit_floor_areas', $report);

	}

	public function importRoomTypes(){

		$file = $this->xml_files['room_types'];


		// debug object
		$report = array(
			'xml_items_count' => 0,
			'filtered_items_count' => 0,
			'imported_items_ids' => array(),
			'total_time' => 0
		);
		$reportStart = microtime(true);
		
		// Callback function 
		// ==============================================
		$iteratorCallback = function($entry) use (&$report) {

			$report['xml_items_count']++;

			$modelData = array();
			$map = array(
				'Code' => 'xml_id',
				'Description' => 'description_cs'
			);

			foreach ( $map as $key => $value) {
				if (property_exists($entry, $key)){
					$modelData[$value] = (string)$entry->$key;
				}
			}

			$report['imported_items_ids'][] = $modelData['xml_id'];

			$obj = Room_type::where('xml_id', '=', $modelData['xml_id'])->first();
			if (empty($obj)){
				$obj = new Room_type($modelData);
			} else {
				$modified_old = $obj->updated_at;
				$obj->fill($modelData);
			}
			$obj->save();

			$this->room_type_mapper[$obj->xml_id] = $obj->id;

			// get status - insert | update | nochange"
			if (isset($modified_old)){

				if ($modified_old != $obj->updated_at) {
					$status = 'update';
				} else {
					$status = 'unchanged';
				}
			} else {
				$status = 'insert';
			}

		};

		// xml iterator 
		// ==============================================
		$this->importXML($file, $iteratorCallback);

		// Delete db row that aren't in new xml
		// ==============================================
		if  (count($report['imported_items_ids']) > 0){
			$delete_start = microtime(true);
			
			Room_type::whereNotIn('xml_id', $report['imported_items_ids'])->delete();

			$delete_end = microtime(true);
			$delete_time = $delete_end - $delete_start;
			$this->setMessage('(room_types) delete time : ' . $delete_time );
		}

		// Debug object 
		// ==============================================
		$reportEnd = microtime(true);
		$report['total_time'] = $reportEnd - $reportStart;
		$this->setDebug('room_types', $report);

	}

	// Importer
	// -----------------------------------------------------------

	/**
	 * @return bool
	 */
	public function importXML($file, $callback){
		//import XML files into DB
		$success = true;
		$msg = '';
		$start = microtime(true);

		$data = $this->loadXML($file);
		if (empty($data)){
			$msg = sprintf('Couldn\'t load xml file or file is empty, %s.', $file);
			$success = false;
		} else {
			foreach ($data as $entry) {

				call_user_func($callback, $entry);

			}

			$msg = sprintf('Successfully imported xml file %s.', $file);
		}

		$end = microtime(true);
		$time = $end - $start;
		$msg .= ' Total time - ' . $time;
		$this->setMessage($msg);
		
		return $success;
	}


	// Helpers
	// -----------------------------------------------------------

	/**
	 * @param $file
	 * @return bool|SimpleXMLElement
	 */
	private function loadXML($file){
		$file = $this->getXMLFilePath($file);
		if (file_exists($file)) {
			return simplexml_load_file($file);
		}
		return false;
	}

	private function getXMLFilePath($file_name){
		return public_path() . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . $this->xml_v . DIRECTORY_SEPARATOR . $file_name;
	}

	private function setMessage($msg){
		$this->messages[] = $msg;
	}

	private function setDebug($key, $obj){
		$this->debug[$key] = $obj;
	}



	private function backupXMLFiles(){
		$date = date('Y_m_d'); // 2014_05_09
		$dir = storage_path('backup/xml') . DIRECTORY_SEPARATOR . $date . '_' .  time();

		if (!file_exists($dir)) {
		    mkdir($dir, 0775, true);
		}

		foreach ($this->xml_files as $key => $relative_path) {
			$file = $this->getXMLFilePath($relative_path);
			$target = $dir. DIRECTORY_SEPARATOR . $relative_path;
			File::copy($file, $target);
		}
	}






}