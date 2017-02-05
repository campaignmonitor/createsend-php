<?php

use CreateSend\Wrapper\Segments;

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new Segments('Segment ID', $auth);

$result = $wrap->add_rulegroup(array(
    'Rules' => array(
        array(
            'RuleType' => 'EmailAddress',
            'Clause' => 'CONTAINS example.com'
        )
    )
));

echo "Result of PUT /api/v3.1/segments/{segmentID}/rules\n<br />";
if($result->was_successful()) {
    echo "Updated with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}