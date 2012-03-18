<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists('List ID', 'Your API Key');

$result = $wrap->activate_webhook('Webhook ID');

echo "Result of PUT /api/v3/lists/{ID}/webhooks/{WHID}/activate\n<br />";
if($result->was_successful()) {
    echo "Activated with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}