<?php

require_once '../../csrest_subscribers.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Subscribers('Your list ID', $auth);
$result = $wrap->add(array(
    'EmailAddress' => 'Subscriber email',
    'Name' => 'Subscriber name',
    'CustomFields' => array(
        array(
            'Key' => 'Field 1 Key',
            'Value' => 'Field Value'
        ),
        array(
            'Key' => 'Field 2 Key',
            'Value' => 'Field Value'
        ),
        array(
            'Key' => 'Multi Option Field 1',
            'Value' => 'Option 1'
        ),
        array(
            'Key' => 'Multi Option Field 1',
            'Value' => 'Option 2'
        )
    ),
    'Resubscribe' => true
));

echo "Result of POST /api/v3.1/subscribers/{list id}.{format}\n<br />";
if($result->was_successful()) {
    echo "Subscribed with code ".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}