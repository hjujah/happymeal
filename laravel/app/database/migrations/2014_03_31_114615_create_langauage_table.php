<?php

use Illuminate\Database\Migrations\Migration;

class CreateLangauageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('languages', function($table)
		{
			$table->increments('id');
			
			$table->string('code', 2); // post type: post, revision... (CPT)
			$table->string('description', 250); // post status: draft, auto-draft, publish, trash...

			// global post timestamps
			$table->timestamps();
		});	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('languages');
	}

}