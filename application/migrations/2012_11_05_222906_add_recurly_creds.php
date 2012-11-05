<?php

class Add_Recurly_Creds {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('options', function($table){
			$table->string('recurly_private');
			$table->string('recurly_public');
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
		Schema::table('options', function($table){
			$table->drop_columns(array('recurly_private','recurly_public'));
		});
	}

}