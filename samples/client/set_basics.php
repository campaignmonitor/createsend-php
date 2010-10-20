<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Your clients ID', 'Your API Key');

$result = $wrap->set_basics(array(
    'CompanyName' => 'Clients company name',
    'ContactName' => 'Clients contact name',
    'EmailAddress' => 'Clients email',
    'Country' => 'Clients country',
    'Timezone' => 'Clients timezone'
));

echo "Result of PUT /api/v3/clients/{id}/setbasics\n<br />";
if($wrap->was_successful($result)) {
	echo "Updated with Code ".$result['code'];
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
    echo '</pre>';
}