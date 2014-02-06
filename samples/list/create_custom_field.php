<?php

require_once '../../csrest_lists.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Lists('List ID', $auth);

/*
 * The DataType parameter must be one of
 * CS_REST_CUSTOM_FIELD_TYPE_TEXT
 * CS_REST_CUSTOM_FIELD_TYPE_NUMBER
 * CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE
 * CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTMANY
 * CS_REST_CUSTOM_FIELD_TYPE_DATE
 * CS_REST_CUSTOM_FIELD_TYPE_COUNTRY
 * CS_REST_CUSTOM_FIELD_TYPE_USSTATE
 *
 */
$result = $wrap->create_custom_field(array(
    'FieldName' => 'Custom field name',
    'DataType' => CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE,
    'Options' => array('First option', 'Second Option')
));

echo "Result of POST /api/v3.1/lists/{ID}/customfields\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}