<?php

class Options_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('options',function($table){
			$table->increments('id');
			$table->boolean('activate_pn');
			$table->string('basic_name');
			$table->string('basic_pass');
			$table->timestamps();
		});

		$o = new Option;
		$o->activate_pn = 0;
		$o->save();
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('options');
	}

}