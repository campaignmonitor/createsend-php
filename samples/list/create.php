<?php

require_once '../../csrest_lists.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Lists(NULL, $auth);

$result = $wrap->create('Lists Client ID', array(
    'Title' => 'List Title',
    'UnsubscribePage' => 'List unsubscribe page',
    'ConfirmedOptIn' => false,
    'ConfirmationSuccessPage' => 'List confirmation success page',
    'UnsubscribeSetting' => CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS
));

echo "Result of POST /api/v3.1/lists/{clientID}\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}
