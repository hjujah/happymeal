<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class XmlTablesAlter extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
		if (Schema::hasTable('building_objects')){
		    Schema::table('building_objects', function($table) {

			    $table->string('job_slug')->after('job_id')->nullable();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasTable('building_objects')){
		    Schema::table('building_objects', function($table) {

			    $table->dropColumn('job_slug');
			});
		}
	}

}
