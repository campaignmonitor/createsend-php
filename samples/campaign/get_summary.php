<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns('Campaign ID to get the summary of', $auth);
$result = $wrap->get_summary();

echo "Result of GET /api/v3.1/campaigns/{id}/summary\n<br />";
if($result->was_successful()) {
    echo "Got summary\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';