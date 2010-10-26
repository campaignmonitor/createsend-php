<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists('List ID', 'Your API Key');

$result = $wrap->get_stats();

echo "Result of GET /api/v3/lists/{ID}/stats\n<br />";
if($wrap->was_successful($result)) {
    echo "Got list stats\n<br /><pre>";
    print_r($result['response']);
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
}
echo '</pre>';