<?php

require_once '../csrest.php';

$wrap = new CS_REST_Wrapper_Base('Your API Key');

$result = $wrap->get_clients();

echo "Result of /api/v3/clients\n<pre>";
if(is_array($result)) {
    print_r($result);
} else {
	echo $result;
}
echo '</pre>';