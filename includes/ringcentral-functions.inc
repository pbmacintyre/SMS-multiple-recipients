<?php
/** Copyright (C) 2019-2025 Paladin Business Solutions */

/* ================= */
/* Generic functions */
/* ================= */

function app_name() {
	return "SMS to Multiple Recipients Blog Demo App";
}

/* ================== */
/* Get RingCental SDK */
/* ================== */
function ringcentral_sdk() {
	// Include Libraries
	require('includes/vendor/autoload.php');

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$jwt_key = $_ENV['RC_JWT_KEY'];

	$sdk = new RingCentral\SDK\SDK(
		$_ENV['RC_APP_CLIENT_ID'],
		$_ENV['RC_APP_CLIENT_SECRET'],
		$_ENV['RC_SERVER_URL']);

	$platform = $sdk->platform();

	// Login via API
	if (!$sdk->platform()->loggedIn()) {
		try {
			$platform->login(["jwt" => $jwt_key]);
		} catch (\RingCentral\SDK\Http\ApiException $e) {
			$sdk = 0;
			// exit("<br/><br/>Unable to authenticate to platform. Check your RingCentral credentials. <br/><br/>") ;
		}
	}
	$controller = array('SDK' => $sdk, 'platform' => $platform);
	return $controller;
}

function send_sms($to_mobiles, $message) {
	$controller = ringcentral_sdk();
	$platform = $controller['platform'];

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$from_mobile = $_ENV['RC_MOBILE_SENDING_NUMBER'];

	$message .= " Reply STOP to opt out of future communication from this mobile number.";
	$api_endpoint = '/restapi/v1.0/account/~/extension/~/sms';

	foreach ($to_mobiles as $to_mobile) {
		try {
			$apiResponse = $platform->post($api_endpoint,
				array('from' => array('phoneNumber' => $from_mobile),
					'to' => array(array('phoneNumber' => $to_mobile)),
					'text' => $message,)
			);
		} catch (\RingCentral\SDK\Http\ApiException $e) {
			// craft a friendly message here.
			echo $e->getMessage();
		}
	}
}
