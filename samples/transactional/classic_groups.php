<?php

use CreateSend\Wrapper\Transactional\ClassicEmail;

$auth = array("api_key" => "Your API Key");
$wrap = new ClassicEmail($auth);

echo "Get the list of classic groups...\n";
$result = $wrap->groups();
var_dump($result->response);

