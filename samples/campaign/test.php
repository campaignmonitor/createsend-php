<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns('Campaign ID to Test', 'Your API Key');

$result = $wrap->test(array(
    'test1@test.com',
    'test2@test.com'
), 'Fallback');

echo "Result of POST /api/v3/campaigns/{id}/test\n<br />";
if($wrap->was_successful($result)) {
    echo "Preview sent with code\n<br />".$result['code'];
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
    echo '</pre>';
}