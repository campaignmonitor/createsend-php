<?php

require_once '../../csrest_clients.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Clients(
    'ClientID to get scheduled campaigns for', 
    $auth);

$result = $wrap->get_scheduled();

echo "Result of /api/v3.1/clients/{id}/scheduled\n<br />";
if($result->was_successful()) {
    echo "Got scheduled campaigns\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';