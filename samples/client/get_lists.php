<?php

use CreateSend\Wrapper\Clients;

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new Clients('ClientID to get Lists for', $auth);

$result = $wrap->get_lists();

echo "Result of /api/v3.1/clients/{id}/lists\n<br />";
if($result->was_successful()) {
    echo "Got lists\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';
