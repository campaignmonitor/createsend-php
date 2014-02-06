<?php

require_once '../../csrest_general.php';

$auth = array('api_key' => 'your api key');
$wrap = new CS_REST_General($auth);

$result = $wrap->get_clients();


echo "Result of /api/v3.1/clients\n<br />";
if($result->was_successful()) {
    echo "Got clients\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';