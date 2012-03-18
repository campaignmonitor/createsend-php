<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients(
	'ClientID to get segments for', 
    'Your API Key');

$result = $wrap->get_segments();

echo "Result of /api/v3/clients/{id}/segments\n<br />";
if($result->was_successful()) {
    echo "Got segments\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';