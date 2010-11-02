<?php

require_once '../../csrest_segments.php';

$wrap = new CS_REST_Lists('Segment ID', 'Your API Key');

$result = $wrap->get_subscribers('Added since');
//$result = $wrap->get_subscribers('abc123', date('Y-m-d', strtotime('-30 days')));

echo "Result of GET /api/v3/segments/{segment id}/active\n<br />";
if($wrap->was_successful($result)) {
    echo "Got subscribers\n<br /><pre>";
    print_r($result['response']);
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
}
echo '</pre>';