<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// ===============================================
// API SECTION
// ===============================================

Route::get('test', 'Vizioart\Cookbook\FrontController@getIndex');

Route::group(array('prefix' => 'api'), function () {

	// import
	//Route::controller('xml', 'XMLImporterController');

	// XML OBJECTS
	// -------------------------------------------------
	Route::get('navigator/{phase_name}', 'NavigatorController@getPhase');
	Route::controller('navigator', 'NavigatorController');

	Route::controller('buildings', 'BuildingController');
	
	Route::get('unit/{unit_id?}', 'UnitController@getUnit')
		 ->where('unit_id', '(.*)');

	// FORMS 
	// -------------------------------------------------
	Route::post('contact', function(){
		
		$input = Input::get();
		//$array = array('name' => $input['name'], 'email' => $input['email'], 'message' => $input['message']);
		$array = $input['form'];

		Mail::send('emails.contact', $array, function($message) {
			$message->to('sales@crestyl.com', 'CRESTYL')->subject('Zpráva z kontaktního formuláře');
		});
		
		return Response::json(array('success' => true));
	});
	
	Route::post('flatForm', function(){
		
		$input = Input::get();
		$array = $input['form'];		

		Mail::send('emails.flatForm', $array, function($message) {
			$message->to('sales@crestyl.com', 'CRESTYL')->subject('Nová rezervace');
		});
		
		Mail::send('emails.flatFormConfirmation', $array, function($message) {
			$ar = Input::get();
			$form = $ar['form'];
			$message->to($form['email'], $form['name'])->subject('Konfirmační email');
		});
		
		return Response::json(array('success' => true));
	});

	// API ROUTE ERROR handling
	// -------------------------------------------------
	Route::get('/{else}', function(){
		// return 400 - bad request
		return Response::json(array(
			'error' => array(
				'message' => "Bad Request", 
				'type' => "BadRequest"
			)
		), 400);
	})->where('else', '(.*)');
	
});
// ----------------------------------------------


// ===============================================
// FRONT
// ===============================================

Route::get('/{route?}', 'Vizioart\Cookbook\FrontController@resolveRoute')
	->where('route', '(.*)');

App::missing(function($exception){
    //return Response::view('errors.missing', array(), 404);
	// echo '<pre>';
	//print_r($exception);
	// echo '</pre>';
	// die();

	$view_data = array(
		'meta_title' => 'Page Not Found - Dock',
	);

	// --------------------------------------
    $model = new Vizioart\Cookbook\Models\MenuModel;
    $menus = $model->get_all(app::getLocale());
    $template_data = array(
        'data' => json_decode($menus->toJson()),
    );
    $view_data['navigation'] = View::make('hbs::navigation', $template_data);
    // --------------------------------------

    // page template (handlebars)
    $view_data['content'] = View::make('hbs::404');

    return Response::view('layouts.error', $view_data, 404);
});




