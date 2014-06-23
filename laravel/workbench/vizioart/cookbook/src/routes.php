<?php

// -------------------------------------------------
// FRONTEND API routes
// -------------------------------------------------
Route::group(array('prefix' => 'api'), function(){

	// setup
	Route::get('setup', 'Vizioart\Cookbook\FrontApiController@getSetup');
	//Route::get('routes', 'Vizioart\Cookbook\FrontApiController@getRoutes');

	// PAGES
	// -------------------------------------------------
	Route::get('page/{lang_code}/{page_name?}', 'Vizioart\Cookbook\FrontApiController@getPage')
		->where('page_name', '(.*)');

	// NEWS
	// -------------------------------------------------
	Route::get('news/{lang_code?}', 'Vizioart\Cookbook\FrontApiController@getNews');


	// menu
	Route::get('menu/{lang_code?}/{slug?}', 'Vizioart\Cookbook\FrontApiController@getMenu')
		->where('slug', '(.*)');



});


Route::get('sanitize/{title?}', function($title = null){

	if (empty($title)){
		$title = 'ceník bytů ve výstavbě';
	}

	$original_title = $title;
	$sanitized_title = Sanitize::sanitize_title($title);


	$res = array(
		'title' => $original_title,
		'seems_utf8' => Sanitize::seems_utf8($title),
		'remove_accents' => Sanitize::remove_accents($title),
		'sanitize_title' => Sanitize::sanitize_title($title),
		'sanitize_title_with_dashes' => Sanitize::sanitize_title_with_dashes($title),
		'url_decode' => urldecode($sanitized_title),
	);


	return Response::json($res, 200);



});



// AUTH routes
// ---------------------------------------

Route::group(array('prefix' => 'account'), function() {

	App::setLocale('en');
	
	Route::get( 'create',                 'Vizioart\Cookbook\AuthController@create');
	Route::post('/',                      'Vizioart\Cookbook\AuthController@store');
	Route::get( 'login',                  'Vizioart\Cookbook\AuthController@login');
	Route::post('login',                  'Vizioart\Cookbook\AuthController@do_login');
	Route::get( 'confirm/{code}',         'Vizioart\Cookbook\AuthController@confirm');
	Route::get( 'forgot_password',        'Vizioart\Cookbook\AuthController@forgot_password');
	Route::post('forgot_password',        'Vizioart\Cookbook\AuthController@do_forgot_password');
	Route::get( 'reset_password/{token}', 'Vizioart\Cookbook\AuthController@reset_password');
	Route::post('reset_password',         'Vizioart\Cookbook\AuthController@do_reset_password');
	Route::get( 'logout',                 'Vizioart\Cookbook\AuthController@logout');
});


// ADMIN
// ---------------------------------------
Route::group(array('prefix' => Config::get('cookbook::cookbook.uri'), 'before' => 'adminauth'), function() {
	

	App::setLocale('en');

	//Admin Dashboard
	Route::get('/', array(
		'as' => 'admin_dashboard',
		'uses' => 'Vizioart\Cookbook\DashboardController@index',
	));


	// -------------------------------------------------
	// Page
	// -------------------------------------------------
	Route::get('/pages', array(
		'as' => 'page_index',
		'uses' => 'Vizioart\Cookbook\PageController@index',
	));
	Route::get('/pages/add', array(
		'as' => 'page_add',
		'uses' => 'Vizioart\Cookbook\PageController@add',
	));
	Route::get('/pages/edit/{id?}', array(
		'as' => 'page_edit',
		'uses' => 'Vizioart\Cookbook\PageController@edit',
	));


	// -------------------------------------------------
	// Article
	// -------------------------------------------------
	Route::get('/articles', array(
		'as' => 'article_index',
		'uses' => 'Vizioart\Cookbook\ArticleController@index',
	));
	Route::get('/articles/add', array(
		'as' => 'article_add',
		'uses' => 'Vizioart\Cookbook\ArticleController@add',
	));
	Route::get('/articles/edit/{id?}', array(
		'as' => 'article_edit',
		'uses' => 'Vizioart\Cookbook\ArticleController@edit',
	));


	// -------------------------------------------------
	// Gallery
	// -------------------------------------------------
	Route::get('/galleries', array(
		'as' => 'gallery_index',
		'uses' => 'Vizioart\Cookbook\GalleryController@index',
	));
	Route::get('/galleries/add', array(
		'as' => 'gallery_add',
		'uses' => 'Vizioart\Cookbook\GalleryController@add',
	));
	Route::get('/galleries/edit/{id?}', array(
		'as' => 'gallery_edit',
		'uses' => 'Vizioart\Cookbook\GalleryController@edit',
	));


	// -------------------------------------------------
	// ADMIN API routes
	// -------------------------------------------------
	Route::group(array('prefix' => 'api'), function(){


		Route::get('/', function(){
			$api_info = array(
				"version" => '1.0.0'
			);
			return Response::json($api_info, 200);
		});


		// PAGE
		// -------------------------------------------------
		// datatable specal result
		Route::get('dt/pages', 'Vizioart\Cookbook\PageApiController@getPagesDatatables');

		Route::get('page', 'Vizioart\Cookbook\PageApiController@getIndex');

		Route::get('page/parents', 'Vizioart\Cookbook\PageApiController@getParents');
		Route::get('page/create', 'Vizioart\Cookbook\PageApiController@getCreatePage');
		Route::get('page/{id}', 'Vizioart\Cookbook\PageApiController@getPage');
		Route::get('page/{id}/content/{language_code}', 'Vizioart\Cookbook\PageApiController@getContent');
		Route::get('page/{id}/attach-gallery/{galleryId}', 'Vizioart\Cookbook\PageApiController@getAttachGallery');
		Route::get('page/{id}/attach-attachment/{attachmentId}', 'Vizioart\Cookbook\PageApiController@getAttachAttachment');
		Route::get('page/unique-url/{url}', 'Vizioart\Cookbook\PageApiController@getIsUrlUnique')->where('url', '(.*)');

		Route::post('page', 'Vizioart\Cookbook\PageApiController@postSave');
		Route::delete('page/content/{id}', 'Vizioart\Cookbook\PageApiController@deleteContent');

		// ARTICLE
		// -------------------------------------------------
		// datatable specal result
		Route::get('dt/articles', 'Vizioart\Cookbook\ArticleApiController@getPagesDatatables');

		Route::get('article', 'Vizioart\Cookbook\ArticleApiController@getIndex');

		Route::get('article/parents', 'Vizioart\Cookbook\ArticleApiController@getParents');
		Route::get('article/create', 'Vizioart\Cookbook\ArticleApiController@getCreateArticle');
		Route::get('article/{id}', 'Vizioart\Cookbook\ArticleApiController@getArticle');
		Route::get('article/{id}/content/{language_code}', 'Vizioart\Cookbook\ArticleApiController@getContent');
		Route::get('article/{id}/attach-gallery/{galleryId}', 'Vizioart\Cookbook\ArticleApiController@getAttachGallery');
		Route::get('article/{id}/attach-attachment/{attachmentId}', 'Vizioart\Cookbook\ArticleApiController@getAttachAttachment');
		Route::get('article/unique-url/{url}', 'Vizioart\Cookbook\ArticleApiController@getIsUrlUnique')->where('url', '(.*)');

		Route::post('article', 'Vizioart\Cookbook\ArticleApiController@postSave');

		Route::delete('article/content/{id}', 'Vizioart\Cookbook\ArticleApiController@deleteContent');


		// GALLERY
		// -------------------------------------------------

		//Route::get('gallery', 'Vizioart\Cookbook\GalleryApiController@getIndex');

		Route::get('gallery/create', 'Vizioart\Cookbook\GalleryApiController@getCreateGallery');
		Route::get('gallery/{id}', 'Vizioart\Cookbook\GalleryApiController@getFetchGallery');
		Route::get('gallery/{id}/items', 'Vizioart\Cookbook\GalleryApiController@getFetchItems');

		Route::post('gallery', 'Vizioart\Cookbook\GalleryApiController@postSaveGallery');
		Route::post('gallery/item', 'Vizioart\Cookbook\GalleryApiController@postAddItem');

		Route::delete('gallery/{id}', 'Vizioart\Cookbook\GalleryApiController@deleteGallery');
		Route::delete('gallery/item/{id}', 'Vizioart\Cookbook\GalleryApiController@deleteItem');



		// ATTACHMENT
		// -------------------------------------------------

		//Route::get('gallery', 'Vizioart\Cookbook\GalleryApiController@getIndex');

		Route::get('attachment/create', 'Vizioart\Cookbook\AttachmentApiController@getCreateAttachment');
		Route::get('attachment/{id}', 'Vizioart\Cookbook\AttachmentApiController@getFetchAttachment');

		Route::post('attachment', 'Vizioart\Cookbook\AttachmentApiController@postSaveAttachment');

		Route::delete('attachment/{id}', 'Vizioart\Cookbook\AttachmentApiController@deleteAttachment');


		
		// LANGUAGE
		// -------------------------------------------------
		Route::get('language', 'Vizioart\Cookbook\LanguageApiController@getIndex');

		
		// FILE
		// -------------------------------------------------
		Route::post('file/upload', 'Vizioart\Cookbook\FileApiController@postUpload');
		Route::delete('file/{id}', 'Vizioart\Cookbook\FileApiController@deleteFile');


		// ADMIN API ROUTE ERROR handling
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


	// ADMIN ROUTE ERROR handling
	// -------------------------------------------------
	Route::get('/{any}', function(){
		return 'admin route - not resolved error';
	})->where('any', '(.*)');

});

