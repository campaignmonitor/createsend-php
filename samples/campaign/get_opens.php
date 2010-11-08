<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get opens for', 'Your API Key');
$result = $wrap->get_opens('Get opens since', 1, 50, 'email', 'asc');
//$result = $wrap->get_opens(date('Y-m-d', strtotime('-30 days')), page, page size, order field, order direction);

echo "Result of GET /api/v3/campaigns/{id}/opens\n<br />";
if($wrap->was_successful($result)) {
    echo "Got opens\n<br /><pre>";
    print_r($result['response']);
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
}
echo '</pre>';