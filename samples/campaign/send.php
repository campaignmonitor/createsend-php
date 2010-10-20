<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to Send', 'Your API Key');

$result = $wrap->send(array(
    'ConfirmationEmail' => 'Confirmation Email Address',
    'SendDate' => 'Date to send (yyyy-mm-dd or immediately)'
));

echo "Result of POST /api/v3/campaigns/{id}/send\n<br />";
if($wrap->was_successful($result)) {
	echo "Scheduled with code\n<br />".$result['code'];
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
    echo '</pre>';
}