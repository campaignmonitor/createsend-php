<?php

require_once '../csrest_clients.php';

$wrap = new CS_REST_Clients(
	'Client ID to Delete', 
    'Your API Key');

$result = $wrap->delete();

echo "Result of DELETE /api/v3/clients/{id}\n<pre>";
if(is_array($result)) {
    print_r($result);
} else {
	echo $result;
}
echo '</pre>';