<?php

require_once '../../csrest_clients.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Clients(
    'ClientID to get Lists for',
    $auth);

$email_address = 'your_email_address@example.com';

$result = $wrap->get_lists_for_email($email_address);

echo "Result of /api/v3.1/clients/{id}/listsforemail\n<br />";
if($result->was_successful()) {
    echo "Got lists to which email address ".$email_address." is subscribed\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';