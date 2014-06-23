<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleryTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// gallery
		// -------------------------------------------
		Schema::create('galleries', function($table) {
			$table->increments('id');

			$table->string('type', 100); // gallery type: post, page, gallery (CPT)
			$table->string('status', 100); // gallery status: draft, auto-draft, publish, trash...
			$table->string('name', 250); // name of the gallery
			$table->string('description', 500)->nullable();
			$table->timestamps();
		});

		// gallery_item
		// -------------------------------------------
		Schema::create('gallery_items', function($table) {
			$table->increments('id');
			$table->integer('gallery_id')->unsigned();
			$table->string('type', 100); // gallery type: post, page, gallery (CPT)
			$table->string('status', 100); // gallery status: draft, auto-draft, publish, trash...
			$table->string('url', 1000); // image url, ie 
			$table->string('name', 250)->nullable(); // name of the gallery
			$table->string('description', 500)->nullable();
			$table->text('content')->nullable(); // could be used for long description of image, or adding gallery item other then img
			$table->timestamps();
		});

		// galleries_posts
		// -------------------------------------------
		// only add this table if posts table exists
		if (Schema::hasTable('posts')){
			Schema::create('galleries_posts', function($table) {
				$table->increments('id');
				$table->integer('gallery_id')->unsigned();
				$table->integer('post_id')->unsigned();
				$table->string('type', 100)->nullable(); // used to define type of relation, 

			});
		}

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
		Schema::drop('galleries');
		Schema::drop('gallery_items');
		if (Schema::hasTable('galleries_posts')){
			Schema::drop('galleries_posts');
		}
	}

}
