<?php

require_once '../../csrest_lists.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Lists('List ID', $auth);

$result = $wrap->get_active_subscribers('Added since', 1, 50, 'email', 'asc');
//$result = $wrap->get_active_subscribers(date('Y-m-d', strtotime('-30 days')), 
//  page number, page size, order by, order direction);

echo "Result of GET /api/v3.1/lists/{ID}/active\n<br />";
if($result->was_successful()) {
    echo "Got subscribers\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';