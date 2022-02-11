<?php

require_once '../../csrest_clients.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Clients(
    'ClientID to get Tags for',
    $auth);

$result = $wrap->get_tags();

echo "Result of /api/v3.3/clients/{id}/tags\n<br />";
if($result->was_successful()) {
    echo "Got tags\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';