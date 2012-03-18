<?php

require_once '../../csrest_segments.php';

$wrap = new CS_REST_Segments('Segment ID', 'Your API Key');

$result = $wrap->get_subscribers('Added since', 1, 50, 'email', 'asc');
//$result = $wrap->get_subscribers(date('Y-m-d', strtotime('-30 days')), 
//  page number, page size, order by, order description);

echo "Result of GET /api/v3/segments/{segment id}/active\n<br />";
if($result->was_successful()) {
    echo "Got subscribers\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';