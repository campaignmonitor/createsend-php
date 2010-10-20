<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get lists for', 'Your API Key');
$result = $wrap->get_lists();

echo "Result of GET /api/v3/campaigns/{id}/lists\n<br />";
if($wrap->was_successful($result)) {
	echo "Got lists\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';