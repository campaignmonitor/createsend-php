<?php

require_once '../../csrest_lists.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Lists('List ID', $auth);

$result = $wrap->get_webhooks();

echo "Result of GET /api/v3.1/lists/{ID}/webhooks\n<br />";
if($result->was_successful()) {
    echo "Got list webhooks\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';