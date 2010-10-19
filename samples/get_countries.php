<?php

require_once '../csrest.php';

$wrap = new CS_REST_Wrapper_Base('Your API Key');

$result = $wrap->get_countries();

echo "Result of /api/v3/countries\n<br />";
if($wrap->was_successful($result)) {
	echo "Got countries\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';