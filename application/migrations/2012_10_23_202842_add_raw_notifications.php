<?php

class Add_Raw_Notifications {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('revenue_stats', function($table){
			$table->integer('notification_reference');
		});
		Schema::create('raw_notifications',function($table){
			$table->increments('id');
			$table->string('type');
			$table->text('xml');
			$table->timestamps();
		});
		Schema::table('subscription_history', function($table){
			$table->integer('notification_reference');
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