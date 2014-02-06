<?php

require_once '../../csrest_subscribers.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Subscribers('Your list ID', $auth);
$result = $wrap->get_history('Email address');

echo "Result of GET /api/v3.1/subscribers/{list id}/history.{format}?email={email}\n<br />";
if($result->was_successful()) {
    echo "Got subscriber history <pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';