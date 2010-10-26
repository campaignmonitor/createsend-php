<?php

require_once '../../csrest_templates.php';

$wrap = new CS_REST_Templates('Template ID', 'Your API Key');

$result = $wrap->delete();

echo "Result of DELETE /api/v3/templates/{ID}\n<br />";
if($wrap->was_successful($result)) {
    echo "Deleted with code\n<br />".$result['code'];
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
    echo '</pre>';
}