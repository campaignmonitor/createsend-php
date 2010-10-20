<?php

require_once '../../csrest_subscribers.php';

$wrap = new CS_REST_Subscribers('Your list ID', 'Your API Key');
$result = $wrap->unsubscribe('Email address');

echo "Result of GET /api/v3/subscribers/{list id}/unsubscribe.{format}?email={email}\n<br />";
if($wrap->was_successful($result)) {
	echo "Unsubscribed with code ".$response['code'];
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
    echo '</pre>';
}