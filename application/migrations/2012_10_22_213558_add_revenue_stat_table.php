<?php

class Add_Revenue_Stat_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('revenue_stats',function($table){
			$table->increments('id');
			$table->string('account_code');
			$table->string('uuid');
			$table->string('invoice_id');
			$table->integer('invoice_number');
			$table->string('subscription_id');
			$table->decimal('amount', 10, 2);
			$table->date('transaction_date');
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