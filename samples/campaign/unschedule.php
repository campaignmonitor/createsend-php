<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns('Campaign ID to unschedule', $auth);

$result = $wrap->unschedule();

echo "Result of POST /api/v3.1/campaigns/{id}/unschedule\n<br />";
if($result->was_successful()) {
    echo "Scheduled with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}