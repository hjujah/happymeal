<?php
//View Composers

// Page edit view
View::composer('cookbook::pages.page.edit', function($view){

	$viewdata= $view->getData();
	// set default vars
	if ( ! isset($viewdata['page_title']) || empty($viewdata['page_title']) ) {
		$view->with('page_title', 'Page');
	}
	
});

// Article edit view
View::composer('cookbook::pages.article.edit', function($view){

	$viewdata= $view->getData();
	// set default vars
	if ( ! isset($viewdata['page_title']) || empty($viewdata['page_title']) ) {
		$view->with('page_title', 'Article');
	}
	
});

// Gallery edit view
View::composer('cookbook::pages.gallery.edit', function($view){

	$viewdata= $view->getData();
	// set default vars
	if ( ! isset($viewdata['page_title']) || empty($viewdata['page_title']) ) {
		$view->with('page_title', 'Gallery');
	}
	
});

// page footer scripts
View::composer('cookbook::partials.scripts_footer', function($view){

	$viewdata= $view->getData();
	if ( ! isset($viewdata['scripts']) || empty($viewdata['scripts']) ) {
		$view->with('scripts', array());
	}
	
});

