<?php

use CreateSend\Wrapper\Lists;

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new Lists('List ID', $auth);

$result = $wrap->get_custom_fields();

echo "Result of GET /api/v3.1/lists/{ID}/customfields\n<br />";
if($result->was_successful()) {
    echo "Got custom fields\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';