<?php

require_once '../../csrest_segments.php';

$wrap = new CS_REST_Segments('Segment ID', 'Your API Key');

$result = $wrap->add_rule(array(
    'Subject' => 'EmailAddress',
    'Clauses' => array('CONTAINS example.com')
));

echo "Result of PUT /api/v3/segments/{segmentID}/rules\n<br />";
if($result->was_successful()) {
    echo "Updated with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}