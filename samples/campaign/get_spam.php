<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns('Campaign ID to get spam complaints for', $auth);
$result = $wrap->get_spam('Get spam complaints since', 1, 50, 'email', 'asc');
//$result = $wrap->get_spam(date('Y-m-d', strtotime('-30 days')), page, page size, order field, order direction);

echo "Result of GET /api/v3.1/campaigns/{id}/spam\n<br />";
if($result->was_successful()) {
    echo "Got spam complaints\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';