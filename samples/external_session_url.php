<?php

require_once '../csrest_general.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_General($auth);

$result = $wrap->external_session_url(array(
    'Email' => 'The email address of the Campaign Monitor user for whom the login session should be created',
    'Chrome' => 'Which chrome to display - Must be either "all", "tabs", or "none"',
    'Url' => 'The URL to display once logged in. e.g. "/subscribers/"',
    'IntegratorID' => 'The Integrator ID. You need to contact Campaign Monitor support to get an Integrator ID.',
    'ClientID' => 'The Client ID of the client which should be active once logged in to the Campaign Monitor account.'
));

echo "Result of PUT /api/v3.1/externalsession\n<br />";
if($result->was_successful()) {
    echo "Succeeded with Code ".$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}