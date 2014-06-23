<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		// meta
		// -------------------------------------------
		Schema::create('meta', function($table) {
			$table->increments('id');
			$table->integer('parent_id');
			$table->string('parent_type', 250);
			$table->string('meta_key', 250); // post status: draft, auto-draft, publish, trash...
			$table->text('meta_value');
			// global post timestamps
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
		Schema::drop('meta');
	}

}
