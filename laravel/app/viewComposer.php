<?php


// all templates
View::composer('hbs::*', function($view) {

	// add common data
	$view->with(array(
		'baseUrl' => url(),
		'lang' => App::getLocale(),
		'ie' => \BrowserDetect::isIE(),
		'ie11' => \BrowserDetect::isIEVersion(11),
	));


	// get data (assume page)
	if ($view->offsetExists('data')){
		$page = $view->offsetGet('data');	
		// page name
		if ($page && is_object($page) && property_exists($page, 'name')){
			$view->with('page',	$page->name);	
		} 
	}


	$viewName = $view->offsetExists('viewName') ? $view->offsetGet('viewName') : '';
	$view->with('viewName',	$viewName);

});


// all templates
View::composer('hbs::navigation', function($view) {

	$routes = $view->offsetGet('data');

	$data = new stdClass;
	$data->desktop = $routes[0];
	$data->mobile = $routes[1];

	$view->with(array(
		'data' => $data,
	));

});



// home.hbs
View::composer( array('hbs::home','hbs::homeIE'), function($view) {

	// page data
	$page = $view->offsetGet('data');

	$viewName = $view->offsetGet('viewName');
	
	$lang = app::getLocale();
	
	$headline = array(
		'cs' => 'DOCKonalý domov',
		'en' => 'Perfect home',
		'ru' => 'Идеальный дом'
	);
	
	
	View::share('imageSrc', '0.jpg');
	View::share('headline', $headline[$lang]);

});

// navigationPage.hbs
View::composer('hbs::navigationPage', function($view) {
	
	// get page data 
	$page = $view->offsetGet('data');

	$colSize = 1;
	$children = $page->children;
	
	if (!empty($children) && is_array($children)){
		$colNum = (count($children) <= 4) ? count($children) : 3;
		$colSize = 12/$colNum;
	}

	// add data
	$view->with(array(
		'colSize' => $colSize,
		'dataFit' => $colSize*4
	));

});

// page.hbs
View::composer('hbs::page', function($view) {

	$viewName = $view->offsetExists('viewName') ? $view->offsetGet('viewName') : '';

	// add data
	$view->with(array(
		'view' => $viewName,
	));
});

// pageSlider.hbs
View::composer('hbs::pageSlider', function($view) {

	$page = $view->offsetGet('data');

	$param = false;
	if(property_exists($page, 'parent_title')) {
		$param = true;
	}

	// add data
	$view->with(array(
		'counter' => null,
		'param' => $param,
	));
});

// news.hbs
View::composer('hbs::news', function($view) {

	// get page data
	$page = $view->offsetGet('data');

	// get articles
	$posts = $page->children;

	// add data
	$view->with(array(
		'data' => $posts,
		'page' => $view->offsetGet('viewName'),
		'param' => false,
	));

});

// article.hbs
View::composer('hbs::article', function($view) {

	// get viewName
	$viewName = $view->offsetGet('viewName');

	// add data
	$view->with(array(
		'page' => $viewName,
		'view' => $viewName,
		'param' => false, // @CHANGE 
	));

});

// gallery.hbs
View::composer('hbs::gallery', function($view) {

	// page data
	$page = $view->offsetGet('data');

	// gallery data
	$data = $page->galleries[0];

	// parent url 
	$link = $page->permalink;
	$perent_url = substr( $link , 0 , strrpos($link, "/") );


	$viewName = $view->offsetGet('viewName');
	$pageName = $page->name;

	// add data
	$view->with(array(
		'data' => $data,
		'page' => $viewName,
		'param' => $pageName, // @CHANGE 
		'link' => $perent_url,
	));

});

// photo.hbs
View::composer('hbs::photo', function($view) {


	// page data
	$page = $view->offsetGet('data');
	$active_item = $view->offsetGet('active_item');
	$pageParams = $view->offsetGet('pageParams');
	
	// expand data
	$page->thumbnails = $page->galleries[0]->items;
			
	// serialize
	$active_item = json_decode($active_item->toJson());
	$page->active_item = $active_item;
	
	// set page data
	$view->offsetSet('data', $page);
	
	View::share('data', $page);
	View::share('page', 'gallery');
	View::share('param', $page->name);
	View::share('subparam', $pageParams[0]);


});


// list.hbs 
View::composer('hbs::list', function($view) {

	// page data
	$page = $view->offsetGet('data');
	
	$phase1 = ($view->offsetExists('phase1')) ? $view->offsetGet('phase1') : false;
	$showButton = ($phase1) ? false : true;

	// Phase data
	$job_id = ($phase1) ? 'PROJEKT-0003/01' : 'PROJEKT-0025/01';
	$stageName = ($phase1) ? "Marina<br/>View" : "River<br/>Watch";

	// get units data
	$model = new \Xml\Unit;
	$units = $model->get_by_job_id($job_id);
	
	// unset page data
	$view->offsetSet('data', $units);
	
	// filter data
	$flatTypes = array();
	$statuses = array();
	$flatStatuses = array();
	foreach ($units as $unit) {
		if( ! array_key_exists($unit['layout_variant'], $flatTypes) ) {
			$flatTypes[$unit['layout_variant']] = $unit;
		}
		if( ! in_array($unit['status'], $statuses) ) {
			$statuses[] = $unit['status'];
		}
	}
	ksort($flatTypes);
	for ($i=0; $i < count($statuses); $i++) { 
		switch ($statuses[$i]) {
			case '0':
				$flatStatuses[] = Lang::get('dock.available');
				break;
			case '1':
				$flatStatuses[] = Lang::get('dock.prereserved');
				break;
			case '2':
				$flatStatuses[] = Lang::get('dock.reserved');
				break;
			case '3':
				$flatStatuses[] = Lang::get('dock.sold');
				break;
		}
	}

	// add shared data
	View::share('data', $units);
	View::share('types', $flatTypes);
	View::share('statuses', $flatStatuses);
	View::share('count', count($units));

	View::share('page', $view->offsetGet('viewName'));
	View::share('stageName', $stageName);
	
	View::share('param', 'list');
	View::share('showButton', $showButton);

});


// navigator.hbs
View::composer('hbs::navigator', function($view) {


	// page data
	$page = $view->offsetGet('data');
	$pageParams = $view->offsetGet('pageParams');


	// get navigator data
	$filters = array(
		'job_id'			=> array('PROJEKT-0025/01'),
		'exclude_job_ids'	=> array('DOCK Z1-3 NAKLADOVY'),
	);

	$navigatorModel = new NavigatorModel;
	$navigatorData = $navigatorModel->getBuildings($filters);

	//serilaize
	$navigatorData = json_decode($navigatorData->toJson());

	// set page data
	$view->offsetSet('data', $navigatorData);

	// page params
	$subparam = false;
	$secondsubparam = false;
	$flat_id = false;
	if (!empty($pageParams) && is_array($pageParams) ){
		$subparam = $pageParams[0];
		$secondsubparam = isset($pageParams[1]) ? $pageParams[1] : false;
		$flat_id = isset($pageParams[2]) ? $pageParams[2] : false;
	}


	$nbBuilding = intval($subparam) - 1;
	$currentFloor = intval($secondsubparam) - 1;

	$floors = ($secondsubparam) ? $navigatorData[$nbBuilding-1]->floors : null;


	$img = 'fazeII.jpg';
	if ($secondsubparam) {
		$img = $navigatorData[$subparam-2]->floors[$secondsubparam-1]->xmlimages[0]->img;
		//$img = $navigatorData[$subparam-2];
	} else if ($subparam) {
		$img = $navigatorData[$subparam-2]->xmlimages[0]->img;
	}
	$img = url('/img/navigator/' . $img);


	$flatData = false;
    if ($flat_id) {
        // get unit data
        if ($unitData = $navigatorModel->getUnit($flat_id)){
			$flatData = json_decode($unitData->toJson());
        }
    }

	//self.price = (self.dataFlat) && self.dataFlat.sales_price.replace(/(\d)(?=(\d{3})+$)/g, ) + ' ' + App.Translations[App.lang].czk;
	$price = false;
	if ($flatData){
		$price = sprintf('%1$s %2$s',
			preg_replace('/(\d)(?=(\d{3})+$)/i', '$1 ', $flatData->sales_price),
			Lang::get('dock.czk')
		);
	}

	View::share('data', $navigatorData);
	View::share('floors', $floors);
	View::share('currentFloor', $currentFloor);
	View::share('flat', $flatData);

	View::share('page', 'apartments');
	View::share('param', 'navigator');
	View::share('subparam', $subparam);
	View::share('secondsubparam', $secondsubparam);
	View::share('currentFloor', $secondsubparam);

	View::share('nbBuilding', $nbBuilding);
	View::share('price', $price);

	View::share('img', $img);

});

// orderFlat.hbs
View::composer('hbs::orderFlat', function($view) {


	$page = $view->offsetGet('data');
	$pageParams = false;
	if ($view->offsetExists('pageParams')){
		$pageParams = $view->offsetGet('pageParams');
		dd($pageParams);
	}

	$generalInquiry = true;
	$subparam = null;
	$unitData = null;
	
	if (!empty($pageParams) && is_array($pageParams)){
		$generalInquiry = false;
		$subparam = $pageParams[0];
		// get unit data
		$navigatorModel = new NavigatorModel;
		if ($unit = $navigatorModel->getUnit($flat_id)){
			$unitData = json_decode($unit->toJson());

			dd($unitData);
		}
	}

	// self.data = self.generalInquiry ? null : self.dataCollection[0];

	$img = ($subparam) ? 'phase2.jpg' : 'phase1.jpg';

	// add data
	$view->with(array(
		'data' => $unitData,
		'page' => 'apartments',
		'param' => 'orderFlat',
		'general' => $generalInquiry,
		'img' => $img,
	));


});
