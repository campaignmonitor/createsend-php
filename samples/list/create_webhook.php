<?php

require_once '../../csrest_lists.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Lists('List ID', $auth);

/*
 * The Events array must contain a combination of 
 * CS_REST_LIST_WEBHOOK_SUBSCRIBE
 * CS_REST_LIST_WEBHOOK_DEACTIVATE
 * CS_REST_LIST_WEBHOOK_UPDATE
 * 
 * The payload format must be one of 
 * CS_REST_WEBHOOK_FORMAT_JSON or
 * CS_REST_WEBHOOK_FORMAT_XML
 */
$result = $wrap->create_webhook(array(
    'Events' => array(CS_REST_LIST_WEBHOOK_SUBSCRIBE, CS_REST_LIST_WEBHOOK_DEACTIVATE),
    'Url' => 'http://www.example.com/webhook_receiver.php',
    'PayloadFormat' => CS_REST_WEBHOOK_FORMAT_JSON
));

echo "Result of POST /api/v3.1/lists/{ID}/webhooks\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}