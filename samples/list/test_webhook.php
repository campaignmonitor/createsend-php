<?php

require_once '../../csrest_lists.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Lists('List ID', $auth);

$result = $wrap->test_webhook('Webhook ID');

echo "Result of POST /api/v3.1/lists/{ID}/webhooks/{WHID}/test\n<br />";
if($result->was_successful()) {
    echo "Test was successful";
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}