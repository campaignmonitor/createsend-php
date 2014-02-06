<?php

require_once '../../csrest_clients.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Clients('Your client ID', $auth);

$result = $wrap->unsuppress('Email address to unsuppress');

echo "Result of PUT /api/v3.1/clients/{id}/unsuppress\n<br />";
if($result->was_successful()) {
    echo "Updated with Code ".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}