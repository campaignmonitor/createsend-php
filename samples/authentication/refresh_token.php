<?php

require_once '../../csrest_general.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token'
);
$wrap = new CS_REST_General($auth);
$result = $wrap->get_clients();
if (!$result->was_successful()) {
    # If you receive '121: Expired OAuth Token', refresh the access token
    if ($result->response->Code == 121) {
        list($new_access_token, $new_expires_in, $new_refresh_token) = 
            $wrap->refresh_token();
        # Save $new_access_token, $new_expires_in, and $new_refresh_token
    }
    # Make the call again
    $result = $wrap->get_clients();
}
var_dump($result->response);
