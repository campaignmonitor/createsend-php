<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients(NULL, 'Your API Key');

$result = $wrap->create(array(
    'CompanyName' => 'Clients company name',
    'ContactName' => 'Clients contact name',
    'EmailAddress' => 'Clients email',
    'Country' => 'Clients country',
    'Timezone' => 'Clients timezone'
));

echo "Result of POST /api/v3/clients\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}