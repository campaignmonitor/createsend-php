<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Client ID to Delete', 'Your API Key');

$result = $wrap->delete();

echo "Result of DELETE /api/v3/clients/{id}\n<br />";
if($result->was_successful()) {
    echo 'Deleted';
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}