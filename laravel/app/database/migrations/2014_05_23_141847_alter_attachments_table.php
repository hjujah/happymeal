<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAttachmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('attachments', function($table) {
			$table->integer('file_id')->nullable()->after('status');
			$table->dropColumn('url');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		//
		Schema::table('attachments', function($table){
			$table->dropColumn('file_id');
			$table->string('url', 1000)->after('status'); // image url, ie 
		});
	}

}
