<?php

require_once '../../csrest_subscribers.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Subscribers('Your list ID', $auth);
$result = $wrap->update('Old Email Address', array(
    'EmailAddress' => 'New Email Address',
    'Name' => 'Subscriber name',
    'CustomFields' => array(
        array(
            'Key' => 'Field Key',
            'Value' => 'Field Value'
        )
    ),
    'Resubscribe' => true
));

echo "Result of PUT /api/v3.1/subscribers/{list id}.{format}?email={email}\n<br />";
if($result->was_successful()) {
    echo "Subscribed with code ".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}