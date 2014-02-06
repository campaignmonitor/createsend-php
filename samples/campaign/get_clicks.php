<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns('Campaign ID to get clicks for', $auth);
$result = $wrap->get_clicks('Get clicks since', 1, 50, 'email', 'asc');
//$result = $wrap->get_clicks(date('Y-m-d', strtotime('-30 days')), page, page size, order field, order direction);

echo "Result of GET /api/v3.1/campaigns/{id}/clicks\n<br />";
if($result->was_successful()) {
    echo "Got clicks\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';