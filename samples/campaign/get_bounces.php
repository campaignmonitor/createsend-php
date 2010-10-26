<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get bounces for', 'Your API Key');
$result = $wrap->get_bounces();

echo "Result of GET /api/v3/campaigns/{id}/bounces\n<br />";
if($wrap->was_successful($result)) {
    echo "Got bounces\n<br /><pre>";
    print_r($result['response']);
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
}
echo '</pre>';