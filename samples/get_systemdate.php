<?php

require_once '../csrest_general.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_General($auth);


$result = $wrap->get_systemdate();

echo "Result of /api/v3.1/systemdate\n<br />";
if($result->was_successful()) {
    echo "Got system date\n<br />".$result->response->SystemDate;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}