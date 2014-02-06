<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns('Campaign ID to Test', $auth);
$result = $wrap->send_preview(array(
    'test1@test.com',
    'test2@test.com'
), 'Fallback');

echo "Result of POST /api/v3.1/campaigns/{id}/sendpreview\n<br />";
if($result->was_successful()) {
    echo "Preview sent with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}