<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Your clients ID', 'Your API Key');

/*
 * The AccessLevel parameter should be some bitwise combination of
 *
 * CS_REST_CLIENT_ACCESS_REPORTS
 * CS_REST_CLIENT_ACCESS_SUBSCRIBERS
 * CS_REST_CLIENT_ACCESS_CREATESEND
 * CS_REST_CLIENT_ACCESS_DESIGNSPAMTEST
 * CS_REST_CLIENT_ACCESS_IMPORTSUBSCRIBERS
 * CS_REST_CLIENT_ACCESS_IMPORTURL
 *
 * or
 * CS_REST_CLIENT_ACCESS_NONE
 */
$result = $wrap->set_access(array(
    'AccessLevel' => CS_REST_CLIENT_ACCESS_REPORTS | CS_REST_CLIENT_ACCESS_CREATESEND,
    'Username' => 'username',
    'Password' => 'password'
));

echo "Result of PUT /api/v3/clients/{id}/setaccess\n<br />";
if($wrap->was_successful($result)) {
    echo "Updated with Code ".$result['code'];
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
    echo '</pre>';
}