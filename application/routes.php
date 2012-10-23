<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post(array('hello', 'world'), function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/

Route::get('/', function()
{
	Asset::add('bootstrapcss', 'css/bootstrap.min.css');
	Asset::add('maincss', 'css/main.css');
	Asset::add('bsjs', 'js/bootstrap.min.js');
	Asset::add('jquery','js/jquery-min.js');
	//get stats
	$today = date('Y-m-d');
	$thisweeknum = date('W');
	$thismonthstart = date('Y-m-01');
	$thismonthend = date('Y-m-t');

	//find the start of week
	$wkday = date('l');
    switch($wkday) {
        case 'Monday': $numDaysToMon = 0; break;
        case 'Tuesday': $numDaysToMon = 1; break;
        case 'Wednesday': $numDaysToMon = 2; break;
        case 'Thursday': $numDaysToMon = 3; break;
        case 'Friday': $numDaysToMon = 4; break;
        case 'Saturday': $numDaysToMon = 5; break;
        case 'Sunday': $numDaysToMon = 6; break;   
    }
    $monday = date('Y-m-d',mktime('0','0','0', date('m'), date('d')-$numDaysToMon, date('Y')));
    //signup queries
	$signups_today = SubscriptionHistory::where('type','=','new')->where('activity_date','>=',$today)->count();	
	$signups_this_month = SubscriptionHistory::where('type','=','new')->where('activity_date','>=',$thismonthstart)->where('activity_date','<=',$thismonthend)->count();	
	$signups_this_week = SubscriptionHistory::where('type','=','new')->where('activity_date','>=',$monday)->count();	
	//cancellations
	$cancelations_today = SubscriptionHistory::where('type','=','canceled')->where('activity_date','>=',$today)->count();	
	$cancelations_this_month = SubscriptionHistory::where('type','=','canceled')->where('activity_date','>=',$thismonthstart)->where('activity_date','<=',$thismonthend)->count();	
	$cancelations_this_week = SubscriptionHistory::where('type','=','canceled')->where('activity_date','>=',$monday)->count();	
	//revenue
	//subtract refunds
	return View::make('dashboard.dashboard', array(
		'su_today'=>$signups_today,
		'su_month' => $signups_this_month,
		'su_week' => $signups_this_week,
		'c_today'=>$cancelations_today,
		'c_month' => $cancelations_this_month,
		'c_week' => $cancelations_this_week,
		)
	);
});

Route::post('confirm/(:any?)', function($referrer = ''){
	$img = '';
	$email = '';
	$response = '';
	$uuid = '';
	if($referrer != '')
	{
		Recurly_Client::$apiKey = 'fad7d9622a9a49489393d4139609f804';
		//get token
		$sub = Recurly_js::fetch($_POST['recurly_token']);
		$uuid = $sub->uuid;
		//find account email
		$account = $sub->account->get();
		$email = $account->email;
		$campaign_id = '447';
		$username = 'recurly';
		$api_key = '7b9ec5edff365fec5be8fd970c225c61';
		$api_url = "https://getambassador.com/api/v2/$username/$api_key/xml/event/record";
		$data = array( 
			'email' => $email,
			'campaign_uid' => $campaign_id,
			'short_code' => $referrer,
			'email_new_ambassador' => 0
			);
		$data = http_build_query($data);
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $api_url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_POST, 1);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($curl_handle);
		curl_close($curl_handle);  
	}
	//save sub uuid -> short_code/email combo
	//save empty as well
	$r = new Referral;
	$r->uuid = $uuid;
	$r->email = $email;
	$r->short_code = $referrer;
	$r->save();
	return View::make('recurly.confirm')->with('response',$response);
});

Route::post('recurly-notification',function(){
	//need to handle 
	$post_xml = file_get_contents ("php://input");
	$notification = new Recurly_PushNotification($post_xml);
	$raw = new RawNotification;
	$raw->type = $notification->type;
	$raw->xml = $post_xml;
	$raw->save();
	if($notification->type == 'successful_payment_notification')
	{
		//check if need to have sent commission event
		if($notification->transaction->subscription_id != '' && $notification->transaction->subscription_id != null)
		{
			$r = Referral::where('uuid', '=', $notification->transaction->subscription_id)->first();
			if($r == null)
			{
				//save to process later
				$n = new Notification;
				$n->uuid = $notification->transaction->subscription_id;
				$n->revenue = $notification->transaction->amount_in_cents;
				$n->save();
			}
			else
			{
				$rev = $notification->transaction->amount_in_cents / 100.00;
				$campaign_id = '447';
				$username = 'recurly';
				$api_key = '7b9ec5edff365fec5be8fd970c225c61';
				$api_url = "https://getambassador.com/api/v2/$username/$api_key/xml/event/record";
				$data = array( 
					'email' => $r->email,
					'campaign_uid' => $campaign_id,
					'short_code' => $r->short_code,
					'email_new_ambassador' => 0,
					'revenue' => $rev,
					);
				$data = http_build_query($data);
				$curl_handle = curl_init();
				curl_setopt($curl_handle, CURLOPT_URL, $api_url);
				curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl_handle, CURLOPT_POST, 1);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
				$response = curl_exec($curl_handle);
				curl_close($curl_handle);  
			}
		}
		//add to revenue stats
		$r = new Revenue;
		$r->account_code = $notification->account->account_code;
		$r->uuid = $notification->transaction->id;
		$r->invoice_id = $notification->transaction->invoice_id;
		$r->invoice_number = $notification->transaction->invoice_number;
		$r->subscription_id = $notification->transaction->subscription_id;
		$r->amount = $notification->transaction->amount_in_cents / 100.00;
		$r->transaction_date = $notification->transaction->date;
		$r->notification_reference = $raw->id;
		$r->save();
	}
	else if($notification->type == 'successful_refund_notification')
	{
	}
	else if($notification->type == 'failed_payment_notification')
	{
	}
	else if($notification->type == 'void_payment_notification')
	{
	}
	else if($notification->type == 'new_subscription_notification')
	{
		$sub = new SubscriptionHistory;
		//TODO: Flag if inserted with trial
		//TODO: Load add ons for parsing
		//TODO: add expected revenue using the current period ends_at date
		$sub->account_code = $notification->account->account_code;
		$sub->type = 'new';
		$sub->uuid = $notification->subscription->uuid;
		$sub->amount = $notification->subscription->total_amount_in_cents / 100.00;
		$sub->quantity = $notification->subscription->quantity;
		$sub->activity_date = $notification->subscription->activated_at;
		$sub->plan_code = $notification->subscription->plan->plan_code;
		$sub->plan_name =$notification->subscription->plan->name;
		$sub->notification_reference = $raw->id;
		$sub->save();

	}
	else if($notification->type == 'updated_subscription_notification')
	{
		//TODO: add expected revenue using the current period ends_at date
		$sub = new SubscriptionHistory;
		$sub->type = 'updated';
		$sub->account_code = $notification->account->account_code;
		$sub->uuid = $notification->subscription->uuid;
		$sub->amount = $notification->subscription->total_amount_in_cents / 100.00;
		$sub->quantity = $notification->subscription->quantity;
		$sub->activity_date = $notification->subscription->current_period_started_at;
		$sub->plan_code = $notification->subscription->plan->plan_code;
		$sub->plan_name =$notification->subscription->plan->name;
		$sub->notification_reference = $raw->id;
		$sub->save();
	}
	else if($notification->type == 'renewed_subscription_notification')
	{
		//TODO: add expected revenue using the current period ends_at date
		$sub = new SubscriptionHistory;
		$sub->type = 'renewal';
		$sub->account_code = $notification->account->account_code;
		$sub->uuid = $notification->subscription->uuid;
		$sub->amount = $notification->subscription->total_amount_in_cents / 100.00;
		$sub->quantity = $notification->subscription->quantity;
		$sub->activity_date = $notification->subscription->current_period_started_at;
		$sub->plan_code = $notification->subscription->plan->plan_code;
		$sub->plan_name =$notification->subscription->plan->name;
		$sub->notification_reference = $raw->id;
		$sub->save();

	}
	else if($notification->type == 'canceled_subscription_notification')
	{
		//TODO: remove from expected revenue
		$sub = new SubscriptionHistory;
		$sub->type = 'canceled';
		$sub->account_code = $notification->account->account_code;
		$sub->uuid = $notification->subscription->uuid;
		$sub->amount = $notification->subscription->total_amount_in_cents / 100.00;
		$sub->quantity = $notification->subscription->quantity;
		$sub->activity_date = $notification->subscription->canceled_at;
		$sub->plan_code = $notification->subscription->plan->plan_code;
		$sub->plan_name =$notification->subscription->plan->name;
		$sub->notification_reference = $raw->id;
		$sub->save();
	}
	else if($notification->type == 'expired_subscription_notification')
	{
		$sub = new SubscriptionHistory;
		$sub->type = 'expire';
		$sub->account_code = $notification->account->account_code;
		$sub->uuid = $notification->subscription->uuid;
		$sub->amount = $notification->subscription->total_amount_in_cents / 100.00;
		$sub->quantity = $notification->subscription->quantity;
		$sub->activity_date = $notification->subscription->expires_at;
		$sub->plan_code = $notification->subscription->plan->plan_code;
		$sub->plan_name =$notification->subscription->plan->name;
		$sub->notification_reference = $raw->id;
		$sub->save();

		//if cancel is not saved, save as cancel
		$sh = SubscriptionHistory::where('uuid','=',$notification->subscription->uuid)->where("type",'=','canceled')->first();
		if($sh == null)
		{
			//TODO: remove from expected revenue
			$sub = new SubscriptionHistory;
			$sub->type = 'canceled';
			$sub->account_code = $notification->account->account_code;
			$sub->uuid = $notification->subscription->uuid;
			$sub->amount = $notification->subscription->total_amount_in_cents / 100.00;
			$sub->quantity = $notification->subscription->quantity;
			$sub->activity_date = $notification->subscription->canceled_at;
			$sub->plan_code = $notification->subscription->plan->plan_code;
			$sub->plan_name =$notification->subscription->plan->name;
			$sub->notification_reference = $raw->id;
			$sub->save();
		}
	}
	else if($notification->type == 'new_account_notification')
	{
		//important to note that if the account is reopened a new account notification is sent
	}
	else if($notification->type == 'billing_info_updated_notification')
	{
	}
	else if($notification->type == 'closed_account_notification')
	{
	}
	else if($notification->type == 'reactivated_account_notification')
	{
		//TODO: add expected revenue using the current period ends_at date
		$s = SubscriptionHistory::where('uuid','=',$notification->subscription->uuid)->where("type",'=','canceled')->first();
		if($s != null)
		{
			$s->type = "reactivated_cancel";
			$sub->notification_reference = $raw->id;
			$s->save();
		}
		else
		{
			//TODO: only possible scenario is that the canceled not failed to be received first, doubtful
		}
	}
});

Route::get('load-existing-subs',function(){
	//go through existing subs and make sure that they are blanked out
});

Route::get('signup', function(){
	Asset::add('jquery','js/jquery-min.js');
	Asset::add('recurly.js','js/recurly.min.js');
	Asset::add('default','default/recurly.css');
	Recurly_js::$privateKey = 'e44b36f13c92465eb519d70e24b4054c';

	$referrer = Input::get('referrer','');
	$signature = Recurly_js::sign(array('account'=>array('account_code'=>'referral_' . rand()),'subscription' => array('plan_code' => 'instant','currency'=>'USD')));
	return View::make('recurly.signup')->with('signature',$signature)->with('referrer',$referrer);
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});
// Event::listen('laravel.query', function($sql, $bindings, $time) {
// 	echo "sql:$sql bindings:";
// 	var_dump($bindings);
// });
/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});