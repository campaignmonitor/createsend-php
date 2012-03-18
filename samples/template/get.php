<?php

require_once '../../csrest_templates.php';

$wrap = new CS_REST_Templates('Template ID', 'Your API Key');

$result = $wrap->get();

echo "Result of GET /api/v3/templates/{ID}\n<br />";
if($result->was_successful()) {
    echo "Got template details\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';