<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedGalleryitemsMarina extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		$id1 =6;
		DB::table('gallery_items')->insert(array(
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina.jpg' ),
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina2.jpg' ),
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina3.jpg' ),
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina4.jpg' )
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
