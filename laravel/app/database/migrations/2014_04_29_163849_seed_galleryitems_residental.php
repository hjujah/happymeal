<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedGalleryitemsResidental extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		$id2 = 7;
		DB::table('gallery_items')->insert(array(
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential2.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential3.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential4.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential5.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential6.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential7.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential8.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential9.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential10.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential11.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential12.jpg' ),
			array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residential13.jpg' )
		));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
