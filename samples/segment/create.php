<?php

require_once '../../csrest_segments.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Segments(NULL, $auth);

$result = $wrap->create('Segments List ID', array(
    'Title' => 'Segment Title',
    'RuleGroups' => array(
        array(
            'Rules' => array(
                array(
                    'RuleType' => 'EmailAddress',
                    'Clause' => 'CONTAINS example.com'
                )
            )
        ),
        array(
            'Rules' => array(
                array(
                    'RuleType' => '[customfield]',
                    'Clause' => 'PROVIDED'
                ),
                array(
                    'RuleType' => '[customfield]',
                    'Clause' => 'EQUALS 1'
                )
            )
        )
    )
));

echo "Result of POST /api/v3.1/segments/{listID}\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}