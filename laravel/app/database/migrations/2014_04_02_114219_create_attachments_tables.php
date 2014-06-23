<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Attachment
		// -------------------------------------------
		Schema::create('attachments', function($table) {
			$table->increments('id');
			$table->integer('parent_id'); 							// attachment parent/owner ID
			$table->string('parent_type', 250)->default('Post'); 	// type if attachment parent/owner: Post, Page, Product...
			$table->string('type', 100); 							// attachment type: featured-image, downloadable (CPT)
			$table->string('status', 100)->default('publish'); 		// attachment status: draft, auto-draft, publish, trash...
			$table->string('url', 1000);							// url for attachment file
			$table->string('name', 250)->nullable(); 				// name of the attachment
			$table->string('description', 500)->nullable();			// attachment description
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
		// drop attachments table
		Schema::drop('attachments');
	}

}
