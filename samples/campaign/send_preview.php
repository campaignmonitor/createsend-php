<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to Test', 'Your API Key');
$result = $wrap->send_preview(array(
    'test1@test.com',
    'test2@test.com'
), 'Fallback');

echo "Result of POST /api/v3/campaigns/{id}/sendpreview\n<br />";
if($result->was_successful()) {
    echo "Preview sent with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}