<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients(
	'ClientID to get drafts for', 
    'Your API Key');

$result = $wrap->get_drafts();

echo "Result of /api/v3/clients/{id}/drafts\n<br />";
if($wrap->was_successful($result)) {
	echo "Got drafts\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';