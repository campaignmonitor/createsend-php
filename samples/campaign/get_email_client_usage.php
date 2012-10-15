<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to get the email client usage for', 'Your API Key');
$result = $wrap->get_email_client_usage();

echo "Result of GET /api/v3/campaigns/{id}/emailclientusage\n<br />";
if($result->was_successful()) {
    echo "Got email client usage\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';