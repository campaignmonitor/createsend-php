<?php

use CreateSend\Wrapper\General;

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new General($auth);


$result = $wrap->get_clients();


echo "Result of /api/v3.1/clients\n<br />";
if($result->was_successful()) {
    echo "Got clients\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';