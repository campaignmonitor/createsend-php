<?php

use CreateSend\Wrapper\General;

$wrap = new General(null);

$result = $wrap->get_apikey('Your username', 'Your password', 'account.test.createsend.com');

echo "Result of /api/v3.1/apikey\n<br />";
if($result->was_successful()) {
    echo "Got API Key\n<br />".$result->response->ApiKey;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}

