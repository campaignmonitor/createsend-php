<?php

use CreateSend\Wrapper\Clients;

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new Clients('Client ID to Delete', $auth);

$result = $wrap->delete();

echo "Result of DELETE /api/v3.1/clients/{id}\n<br />";
if($result->was_successful()) {
    echo 'Deleted';
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}