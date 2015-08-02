<?php
require_once "../../csrest_transactional_classicemail.php";

$auth = array("api_key" => "Your API Key");
$wrap = new CS_REST_Transactional_ClassicEmail($auth);

echo "Get the list of classic groups...\n";
$result = $wrap->groups();
var_dump($result->response);

