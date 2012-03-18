<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get the summary of', 'Your API Key');
$result = $wrap->get_summary();

echo "Result of GET /api/v3/campaigns/{id}/summary\n<br />";
if($result->was_successful()) {
    echo "Got summary\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';