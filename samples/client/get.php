<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Your clients ID', 'Your API Key');
$result = $wrap->get();

echo "Result of GET /api/v3/clients/{id}\n<br />";
if($result->was_successful()) {
    echo "Got client <pre>";
    var_dump($result->response);

    $access_level = $result->response->AccessDetails->AccessLevel;
    if($access_level & CS_REST_CLIENT_ACCESS_CREATESEND === CS_REST_CLIENT_ACCESS_CREATESEND) {
        echo 'Client has Create Send access';
    }
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';