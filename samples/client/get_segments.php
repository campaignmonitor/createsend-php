<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients(
	'ClientID to get segments for', 
    'Your API Key');

$result = $wrap->get_segments();

echo "Result of /api/v3/clients/{id}/segments\n<br />";
if($wrap->was_successful($result)) {
	echo "Got segments\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';