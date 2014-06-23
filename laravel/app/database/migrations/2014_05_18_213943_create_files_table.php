<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
		// images
		// -------------------------------------------
		Schema::create('files', function($table) {
			$table->increments('id');
			// relations
			$table->integer('user_id')->default(0);
			// file fields
			$table->string('url', 500); // relative url to file
			$table->string('name', 500)->nullable();
			$table->string('extension', 50)->nullable(); // jpg | png | ...
			$table->string('type', 100); // file type: image, document...
			$table->integer('size'); // file size in bytes.
			
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
		Schema::drop('files');
	}

}
