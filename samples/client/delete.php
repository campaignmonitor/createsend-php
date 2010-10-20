<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Client ID to Delete', 'Your API Key');

$result = $wrap->delete();

echo "Result of DELETE /api/v3/clients/{id}\n<br />";
if($wrap->was_successful($result)) {
	echo 'Deleted';
} else {
	echo 'Failed to delete with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
	echo '</pre>';
}