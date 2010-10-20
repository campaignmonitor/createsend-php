<?php

require_once '../../csrest_templates.php';

$wrap = new CS_REST_Templates('Template ID', 'Your API Key');

$result = $wrap->get();

echo "Result of GET /api/v3/templates/{ID}\n<br />";
if($wrap->was_successful($result)) {
	echo "Got template details\n<br /><pre>";
	print_r($result['response']);
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
}
echo '</pre>';