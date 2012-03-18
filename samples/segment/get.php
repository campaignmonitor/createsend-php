<?php

require_once '../../csrest_segments.php';

$wrap = new CS_REST_Segments('Segment ID', 'Your API Key');

$result = $wrap->get();

echo "Result of GET /api/v3/segments/{ID}\n<br />";
if($result->was_successful()) {
    echo "Got segment details\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';