<?php

require_once '../../csrest_subscribers.php';

$wrap = new CS_REST_Subscribers('Your list ID', 'Your API Key');
$result = $wrap->unsubscribe('Email Address');

echo "Result of GET /api/v3/subscribers/{list id}/unsubscribe.{format}\n<br />";
if($result->was_successful()) {
    echo "Unsubscribed with code ".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}