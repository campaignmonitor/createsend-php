<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns('Campaign ID to get lists for', $auth);
$result = $wrap->get_lists_and_segments();

echo "Result of GET /api/v3.1/campaigns/{id}/listsandsegments\n<br />";
if($result->was_successful()) {
    echo "Got lists and segments\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';