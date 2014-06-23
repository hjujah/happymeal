<?php

use Illuminate\Database\Migrations\Migration;

class CreatePostsTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		// posts
		// -------------------------------------------
		Schema::create('posts', function($table) {
			$table->increments('id');
			$table->integer('parent_id');
			$table->string('type', 100); // post type: post, revision... (CPT)
			$table->string('status', 100); // post status: draft, auto-draft, publish, trash...
			// global post timestamps
			$table->timestamps();
		});

		// post_contents
		// -------------------------------------------
		Schema::create('post_contents', function($table) {
			$table->increments('id');
			// relations
			$table->integer('post_id'); // language id
			$table->integer('language_id'); // language id
			$table->integer('user_id'); // author id
			// local fields
			$table->string('url', 1000);
			$table->string('name', 250);
			$table->string('type', 100); // post type: post, revision... (CPT)
			$table->string('status', 100); // post status: draft, auto-draft, publish, trash...
			// post content
			$table->string('title', 250);
			$table->text('content');
			// timestamps
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
		Schema::drop('posts');
		Schema::drop('posts_content');
	}

}