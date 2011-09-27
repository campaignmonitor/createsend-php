<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to unschedule', 'Your API Key');

$result = $wrap->unschedule();

echo "Result of POST /api/v3/campaigns/{id}/unschedule\n<br />";
if($result->was_successful()) {
    echo "Scheduled with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}