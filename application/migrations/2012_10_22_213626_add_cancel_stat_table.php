<?php

class Add_Cancel_Stat_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		/*Schema::create('expired_subscription_stats',function($table){
			$table->increments('id');
			$table->string('account_code');
			$table->string('uuid');
			$table->string('plan_code');
			$table->string('plan_name');
			$table->integer('quantity');
			$table->float('amount');
			$table->date('expired_date');
			$table->date('cancelled_date');
			$table->date('activated_date');
			$table->timestamps();
		});*/

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