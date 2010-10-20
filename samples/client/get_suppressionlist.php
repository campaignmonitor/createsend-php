<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients(
	'ClientID to get the suppression list of', 
    'Your API Key');

$result = $wrap->get_suppressionlist();

echo "Result of /api/v3/clients/{id}/suppressionlist\n<br />";
if($wrap->was_successful($result)) {
	echo "Got suppression list\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';