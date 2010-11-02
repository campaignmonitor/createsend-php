<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists('List ID', 'Your API Key');

$result = $wrap->get_segment_subscribers('Segment ID', 'Added since');
//$result = $wrap->get_segment_subscribers('abc123', date('Y-m-d', strtotime('-30 days')));

echo "Result of GET /api/v3/lists/{ID}/segments/{segment id}/active\n<br />";
if($wrap->was_successful($result)) {
    echo "Got subscribers\n<br /><pre>";
    print_r($result['response']);
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
}
echo '</pre>';