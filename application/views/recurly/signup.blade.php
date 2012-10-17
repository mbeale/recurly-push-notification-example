<html>
	<head>
		<?php echo Asset::styles(); ?>
    	<?php echo Asset::scripts(); ?>
		<script>
		$(function(){
			Recurly.config({
				subdomain: 'greenlantern'
				, currency: 'USD' // GBP | CAD | EUR, etc...
				, country: 'ES'
				, VATPercent: 20
				, enableGeoIp: true
				, locale : {
					errors : {
						invalidCoupon: "Say what? You talkin' crazy"
					}
				}
			});
	
	
			Recurly.buildSubscriptionForm({
				planCode: 'test-plan-340480',
				quantity: 2,
				subscription: {
					quantity: 2
				},
				target: '#recurly-form',
				successURL: 'confirm/<?php echo $referrer;?>',
				signature: '<?php echo $signature;?>',
				account : {
					firstName: 'John',
					email: 'michael.beale+<?php echo rand(0,10000);?>@recurly.com',
					lastName: 'Riggins',
				},
				addressRequirement: 'full',
				enableCoupons: true,
				billingInfo: {
					firstName: 'John',
					lastName: 'Kovčić',
					address1: '1234 Some St',
					city: 'SomeCity',
					zip: '84098',
					state: 'UT',
					cardNumber: '4111111111111111',
					CVV: '123',
					country: 'US'

				},
				subscription: {
					couponCode: 'testplan'
				},
				distinguishContactFromBillingInfo: true,
				collectPhone: true,

			});
		});
		</script>
	</head>
	<body>
		<h1>Test Form</h1>

		<div id="recurly-form"></div>
	</body>
</html>
