<?php

require_once '../csrest.php';

$wrap = new CS_REST_Wrapper_Base('Your API Key');

$result = $wrap->get_apikey('Your username', 'Your password', 'account.test.createsend.com');

echo "Result of /api/v3/apikey\n<br />";
if($wrap->was_successful($result)) {
    echo "Got API Key\n<br />".$result['response'];
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
    echo '</pre>';
}