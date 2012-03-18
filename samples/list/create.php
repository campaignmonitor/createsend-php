<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists(NULL, 'Your API Key');

$result = $wrap->create('Lists Client ID', array(
    'Title' => 'List Title',
    'UnsubscribePage' => 'List unsubscribe page',
    'ConfirmedOptIn' => true,
    'ConfirmationSuccessPage' => 'List confirmation success page'
));

echo "Result of POST /api/v3/lists/{clientID}\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}