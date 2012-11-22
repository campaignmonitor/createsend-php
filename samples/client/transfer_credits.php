<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Client ID', 'Your API Key');

$transfer_details = array(
  'Credits' => 200,
  'CanUseMyCreditsWhenTheyRunOut' => false
);

$result = $wrap->transfer_credits($transfer_details);

echo "Result of POST /api/v3/clients/{id}/credits\n<br />";
if($result->was_successful()) {
    echo "Transferred with response\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}