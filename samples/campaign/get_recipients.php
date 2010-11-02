<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get recipients for', 'Your API Key');
$result = $wrap->get_recipients(1, 50, 'email', 'asc');
//$result = $wrap->get_recipients(page number, page size, order by, order direction);

echo "Result of GET /api/v3/campaigns/{id}/recipients\n<br />";
if($wrap->was_successful($result)) {
    echo "Got recipients\n<br /><pre>";
    print_r($result['response']);
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
}
echo '</pre>';