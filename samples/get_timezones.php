<?php

require_once '../csrest.php';

$wrap = new CS_REST_Wrapper_Base('Your API Key');

$result = $wrap->get_timezones();

echo "Result of /api/v3/timezones\n<br />";
if($result->was_successful()) {
    echo "Got timezones\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';