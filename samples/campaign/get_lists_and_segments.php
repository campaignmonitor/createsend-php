<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get lists for', 'Your API Key');
$result = $wrap->get_lists_and_segments();

echo "Result of GET /api/v3/campaigns/{id}/listsandsegments\n<br />";
if($result->was_successful()) {
    echo "Got lists and segments\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';