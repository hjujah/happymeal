<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXmlTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('units', function(Blueprint $table){
            $table->increments('id');
            $table->string("xml_id")->unique();
            $table->string("job_id");
            $table->integer("building_object_id");
            $table->string("building_object_xml_id");
            $table->integer("floor_code_id");
            $table->string("floor_code_xml_id");
            $table->integer("building_floor_id");
            $table->string("status");
            $table->string("layout_variant");
            $table->string("unit_sales_area");
            $table->string("sales_price");
            $table->integer("unit_type_id");
            $table->string("unit_type_xml_id");
            $table->string("garden_area");
            $table->string("terrace_area");
            $table->string("for_rent");
            $table->string("rented");
            $table->string("rental_price");
            $table->string("note_for_web");
            $table->timestamps();

		});


		Schema::create('unit_types', function(Blueprint $table){
			$table->increments('id');
            $table->string("xml_id")->unique();
            $table->string("description");
            $table->timestamps();
		});


		Schema::create('unit_floor_areas', function(Blueprint $table){
            $table->increments("id");
            $table->integer("unit_id");
            $table->string("unit_xml_id");
            $table->string("room_no");
            $table->integer("room_id");
            $table->string("room_xml_id");
            $table->string("room_code");
            $table->string("floor_area");
            $table->timestamps();
        });


        Schema::create('room_types', function(Blueprint $table){
			$table->increments('id');
            $table->string("xml_id")->unique();
            $table->string("description");
            $table->timestamps();
		});


		Schema::create('floor_codes', function(Blueprint $table){
            $table->increments('id');
            $table->string("xml_id")->unique();
            $table->string("description");
            $table->timestamps();
        });


		Schema::create('building_objects', function(Blueprint $table){
            $table->increments('id');
            $table->string("xml_id")->unique();
            $table->string("job_id");
            $table->string("description");
            $table->timestamps();

        });

        Schema::create('building_floors', function(Blueprint $table){
            $table->increments('id');
            $table->integer("building_object_id");
            $table->integer("floor_code_id");
            $table->timestamps();

        });

        Schema::create('xmlimages', function(Blueprint $table){
            $table->increments("id");
            $table->string("svg");
            $table->string("img");
            $table->integer("imageable_id");
            $table->string("imageable_type");
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
        Schema::dropIfExists('units');
        Schema::dropIfExists('unit_types');
        Schema::dropIfExists('unit_floor_areas');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('floor_codes');
        Schema::dropIfExists('building_objects');
        Schema::dropIfExists('xmlimages');
	}

}