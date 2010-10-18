<?php

require_once '../csrest_clients.php';

$wrap = new CS_REST_Clients(
	'ClientID to get Campaigns for', 
    'Your API Key');

$result = $wrap->get_campaigns();

echo "Result of /api/v3/clients/{id}/campaigns\n<pre>";
if(is_array($result)) {
    print_r($result);
} else {
	echo $result;
}
echo '</pre>';