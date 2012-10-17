## Recurly Bundle V 1.0

Recurly is Enterprise-class recurring billing management for your business

Install using Artisan CLI:

	php artisan bundle:install recurly

You must add the auto-load line to bundles.php for the Recurly bundle:

	return array(
		'recurly' => array('auto'=>true)
	);


Example Usage
    
    Recurly_Account::get('account_code_here');



## Recurly Information
- Homepage:      https://recurly.com/
- Documentation: https://docs.recurly.com/
- Bugs:          https://github.com/recurly/recurly-client-php/issues
- Repository:    https://github.com/recurly/recurly-client-php