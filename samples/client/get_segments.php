<?php

require_once '../../csrest_clients.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Clients(
    'ClientID to get segments for', 
    $auth);

$result = $wrap->get_segments();

echo "Result of /api/v3.1/clients/{id}/segments\n<br />";
if($result->was_successful()) {
    echo "Got segments\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';