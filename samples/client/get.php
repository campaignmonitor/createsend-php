<?php

require_once '../../csrest_clients.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Clients('Your client ID', $auth);
$result = $wrap->get();

echo "Result of GET /api/v3.1/clients/{id}\n<br />";
if($result->was_successful()) {
    echo "Got client <pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';