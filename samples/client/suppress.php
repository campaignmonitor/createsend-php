<?php

use CreateSend\Wrapper\Clients;

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new Clients('Your client ID', $auth);

$emails = array(
  'example@example.com',
  'another@example.com'
);

$result = $wrap->suppress($emails);

echo "Result of PUT /api/v3.1/clients/{id}/suppress\n<br />";
if($result->was_successful()) {
    echo "Updated with Code ".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}