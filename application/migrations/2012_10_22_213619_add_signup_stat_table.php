<?php

class Add_Signup_Stat_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		/*Schema::create('subscription_signup_stats',function($table){
			$table->increments('id');
			$table->string('account_code');
			$table->string('uuid');
			$table->string('plan_code');
			$table->string('plan_name');
			$table->integer('quantity');
			$table->float('amount');
			$table->date('activation_date');
			$table->timestamps();
		});*/

		Schema::create('subscription_history',function($table){
			$table->increments('id');
			$table->string('uuid');
			$table->string('account_code');
			$table->string('plan_code');
			$table->string('plan_name');
			$table->integer('quantity');
			$table->decimal('amount', 10, 2);
			$table->date('activation_date');
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