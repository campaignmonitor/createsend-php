<?php

require_once '../../csrest_general.php';

$client_id = 8998879;
$client_secret = 'iou0q9wud0q9wd0q9wid0q9iwd0q9wid0q9wdqwd';
$redirect_uri = 'http://example.com/auth';
$code = 'd92id09iwdwqw';

$result = CS_REST_General::exchange_token($client_id, $client_secret, $redirect_uri, $code);

if($result->was_successful()) {
    $access_token = $result->response->access_token;
    $expires_in = $result->response->expires_in;
    $refresh_token = $result->response->refresh_token;
    # Save $access_token, $expires_in, and $refresh_token.
    echo "access token: ".$access_token."\n";
    echo "expires in (seconds): ".$expires_in."\n";
    echo "refresh token: ".$refresh_token."\n";
} else {
    echo 'An error occurred:\n';
    echo $result->response->error.': '.$result->response->error_description."\n";
    # Handle error...
}
