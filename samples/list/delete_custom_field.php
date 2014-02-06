<?php

require_once '../../csrest_lists.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Lists('List ID', $auth);

$result = $wrap->delete_custom_field('Custom field key');

echo "Result of DELETE /api/v3.1/lists/{ID}/{Key}\n<br />";
if($result->was_successful()) {
    echo "Deleted with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}