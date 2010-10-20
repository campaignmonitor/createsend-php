<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get the summary of', 'Your API Key');
$result = $wrap->get_summary();

echo "Result of GET /api/v3/campaigns/{id}/summary\n<br />";
if($wrap->was_successful($result)) {
	echo "Got summary\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';