<?php

class Add_Type_And_Activity_Date {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('subscription_history',function($table)
		{
			$table->drop_column('activation_date');
			$table->string('type');
			$table->date('activity_date');
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