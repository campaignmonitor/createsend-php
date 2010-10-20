<?php

require_once '../../csrest_subscribers.php';

$wrap = new CS_REST_Subscribers('Your list ID', 'Your API Key');
$result = $wrap->add(array(
    'EmailAddress' => 'Subscriber email',
    'Name' => 'Subscriber name',
    'CustomFields' => array(
        array(
            'Key' => 'Field name',
            'Value' => 'Field Value'
        )
    ),
    'Resubscribe' => true
));

echo "Result of POST /api/v3/subscribers/{list id}.{format}\n<br />";
if($wrap->was_successful($result)) {
	echo "Subscribed with code ".$response['code'];
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
    echo '</pre>';
}