<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients(
	'ClientID to get Lists for', 
    'Your API Key');

$result = $wrap->get_lists();

echo "Result of /api/v3/clients/{id}/lists\n<br />";
if($wrap->was_successful($result)) {
    echo "Got lists\n<br /><pre>";
    print_r($result['response']);
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
}
echo '</pre>';