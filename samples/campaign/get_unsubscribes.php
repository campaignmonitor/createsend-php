<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get unsubscribes for', 'Your API Key');
$result = $wrap->get_unsubscribes('Get unsubscribes since');
//$result = $wrap->get_unsubscribes(date('Y-m-d', strtotime('-30 days')));

echo "Result of GET /api/v3/campaigns/{id}/unsubscribes\n<br />";
if($wrap->was_successful($result)) {
	echo "Got unsubscribes\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';