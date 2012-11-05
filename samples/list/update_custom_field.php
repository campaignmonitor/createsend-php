<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists('List ID', 'Your API Key');

$result = $wrap->update_custom_field(
    '[CustomFieldKey]',
    array(
        'FieldName' => 'new field name',
        'VisibleInPreferenceCenter' => true
    )
);

echo "Result of PUT /api/v3/lists/{ID}/customfields/{fieldkey}\n<br />";
if($result->was_successful()) {
    echo "Updated with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}