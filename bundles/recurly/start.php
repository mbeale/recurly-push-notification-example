<?php

Autoloader::map(array(
	'Recurly' => __DIR__.'/lib/recurly.php',
));

// include
require_once(__DIR__.'/lib/recurly.php');

// load config
$config = Config::get('recurly');

Recurly_Client::$apiKey = $config['api_key'];
if(isset($config['private_key'])){
    Recurly_js::$privateKey = $config['private_key'];
}
