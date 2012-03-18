<?php

require_once '../../csrest_subscribers.php';

$wrap = new CS_REST_Subscribers('Your list ID', 'Your API Key');
$result = $wrap->add(array(
    'EmailAddress' => 'Subscriber email',
    'Name' => 'Subscriber name',
    'CustomFields' => array(
        array(
            'Key' => 'Field Key',
            'Value' => 'Field Value'
        )
    ),
    'Resubscribe' => true
));

echo "Result of POST /api/v3/subscribers/{list id}.{format}\n<br />";
if($result->was_successful()) {
    echo "Subscribed with code ".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}