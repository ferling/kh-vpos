<?php 

return [
	'mid' => env('KH_MERCHANT_ID', 0),
	'currency' => env('KH_CURRENCY','HUF'),
	'private_key' => env('KH_PRIVATE_KEY','path/to/privatekey.pem'),
	'live' => env('KH_LIVE',false),
	'check_signature' => env('KH_CHECK_SIGNATURE',false),
	'lang' => env('KH_LANG','HU')
];
