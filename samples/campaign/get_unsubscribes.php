<?php

use CreateSend\Wrapper\Campaigns;

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new Campaigns('Campaign ID to get unsubscribes for', $auth);
$result = $wrap->get_unsubscribes('Get unsubscribes since', 1, 50, 'email', 'asc');
//$result = $wrap->get_unsubscribes(date('Y-m-d', strtotime('-30 days')), page, page size, order field, order direction);

echo "Result of GET /api/v3.1/campaigns/{id}/unsubscribes\n<br />";
if($result->was_successful()) {
    echo "Got unsubscribes\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';