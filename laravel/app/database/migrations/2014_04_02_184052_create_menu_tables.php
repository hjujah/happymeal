<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
		// menu
		// -------------------------------------------
		Schema::create('menus', function($table) {
			$table->increments('id');
			$table->string('type', 100); 						// menu type: ... no idea
			$table->string('postition', 100)->default(''); 		// menu position: main-nav, side-nav...
			$table->string('slug', 250); 						// slug of the menu
			$table->string('name', 250)->nullable();			// name of the menu
			$table->string('description', 500)->nullable();		// description
			$table->timestamps();
		});

		// menu_item
		// -------------------------------------------
		Schema::create('menu_items', function($table) {
			$table->increments('id');
			$table->integer('menu_id')->unsigned();
			$table->integer('language_id');
			$table->string('label', 250); 						// menu item label: Home, About us...
			$table->integer('order'); 							// menu item order in menu
			$table->string('url', 1000); 						// menu item url
			$table->string('slug', 250); 						// menu item slug
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('menus');
		Schema::drop('menu_items');
	}

}
