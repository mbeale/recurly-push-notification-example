<?php

class Create_Link_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('referrals', function($table)
		{
			 $table->increments('id');
			 $table->string('uuid');
			 $table->string('email');
			 $table->string('short_code');
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