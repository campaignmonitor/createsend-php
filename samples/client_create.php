<?php

require_once '../csrest_clients.php';

$wrap = new CS_REST_Clients(NULL, 'Your API Key');

$result = $wrap->create(array(
    'CompanyName' => 'PHP Test Client',
    'ContactName' => 'Toby Brain PHP',
    'EmailAddress' => 'tobyb+phpwrap@freshview.com',
    'Country' => 'Australia',
    'Timezone' => '(GMT+03:00) Nairobi'
));

echo "Result of POST /api/v3/clients\n<br />";
if($wrap->was_successful($result)) {
	echo "Created with ID\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';