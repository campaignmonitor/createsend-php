<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists('List ID', 'Your API Key');

$result = $wrap->update_field_options('[CustomFieldKey]',
    array('Option 1', 'Option 2'), true);

echo "Result of PUT /api/v3/lists/{ID}/customfields/{fieldkey}/options\n<br />";
if($result->was_successful()) {
    echo "Updated with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}