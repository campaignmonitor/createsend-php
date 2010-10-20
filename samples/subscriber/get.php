<?php

require_once '../../csrest_subscribers.php';

$wrap = new CS_REST_Subscribers('Your list ID', 'Your API Key');
$result = $wrap->get('Email address');

echo "Result of GET /api/v3/subscribers/{list id}.{format}?email={email}\n<br />";
if($wrap->was_successful($result)) {
	echo "Got subscriber <pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';