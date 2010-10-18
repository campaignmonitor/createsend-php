<?php

require_once '../csrest.php';

$wrap = new CS_REST_Wrapper_Base('Your API Key');

$result = $wrap->get_clients();


echo "Result of /api/v3/clients\n<br />";
if($wrap->was_successful($result)) {
	echo "Got clients\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';