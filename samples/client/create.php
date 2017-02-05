<?php

use CreateSend\Wrapper\Clients;

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new Clients(null, $auth);

$result = $wrap->create(array(
    'CompanyName' => 'Clients company name',
    'Country' => 'Clients country',
    'Timezone' => 'Clients timezone'
));

echo "Result of POST /api/v3.1/clients\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}