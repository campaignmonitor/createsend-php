<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Your clients ID', 'Your API Key');
$result = $wrap->get();

echo "Result of GET /api/v3/clients/{id}\n<br />";
if($wrap->was_successful($result)) {
    echo "Got client <pre>";
    print_r($result['response']);

    $access_level = $result['response']['AccessAndBilling']['AccessLevel'];
    if($access_level & CS_REST_CLIENT_ACCESS_CREATESEND === CS_REST_CLIENT_ACCESS_CREATESEND) {
        echo 'Client has Create Send access';
    }
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
}
echo '</pre>';