<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedGalleryitems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	 
	 
	 //POST_IDS => 12 - marina 13 - residence 14 - locality
	 // GALL IDS 6 - marina 7 - residence 8 - locality
	 
	 
	public function up()
	{
		/*
$id1 =6;
		DB::table('gallery_items')->insert(array(
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina1.jpg' );
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina2.jpg' )
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina3.jpg' )
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina4.jpg' )
		   array( 'gallery_id' => $id1, 'type' => 'image', 'status' => 'publish', 'url' => 'marina5.jpg' )
		));
		
*/
		/*
$id2 = 7;
		DB::table('gallery_items')->insert(array(
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residence1.jpg' );
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residence2.jpg' )
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residence3.jpg' )
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residence4.jpg' )
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'residence5.jpg' )
		));
*/
		
		/*
$id2 = 8;
		DB::table('gallery_items')->insert(array(
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'lokalita.jpg' ),
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'lokalita1.jpg' ),
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'lokalita2.jpg' ),
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'lokalita3.jpg' ),
		   array( 'gallery_id' => $id2, 'type' => 'image', 'status' => 'publish', 'url' => 'lokalita4.jpg' )
		));
*/
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
