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
	return View::make('home.index');
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
	if($notification->type == 'successful_payment_notification')
	{
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