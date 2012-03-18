<?php

require_once '../csrest_general.php';

$wrap = new CS_REST_General('Your API Key');

$result = $wrap->get_clients();


echo "Result of /api/v3/clients\n<br />";
if($result->was_successful()) {
    echo "Got clients\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';