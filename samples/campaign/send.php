<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns('Campaign ID to Send', $auth);

$result = $wrap->send(array(
    'ConfirmationEmail' => 'Confirmation Email Address',
    'SendDate' => 'Date to send (yyyy-mm-dd or immediately)'
));

echo "Result of POST /api/v3.1/campaigns/{id}/send\n<br />";
if($result->was_successful()) {
    echo "Scheduled with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}