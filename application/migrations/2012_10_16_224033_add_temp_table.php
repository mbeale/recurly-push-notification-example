<?php

class Add_Temp_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('notifications', function($table){
			$table->increments('id');
			$table->string('uuid');
			$table->integer('revenue');
			$table->timestamps();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}