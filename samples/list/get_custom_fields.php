<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists('List ID', 'Your API Key');

$result = $wrap->get_custom_fields();

echo "Result of GET /api/v3/lists/{ID}/customfields\n<br />";
if($wrap->was_successful($result)) {
	echo "Got custom fields\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';