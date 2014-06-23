<?php

//Filters

Route::filter('adminauth', function(){
	if (Auth::guest()) {
		return Redirect::guest('account/login');
	}
});
