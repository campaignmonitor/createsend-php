<?php
require_once "../../csrest_transactional_classicemail.php";

$client_id = "Your Client ID";
$auth = array("api_key" => "Your API Key");

$wrap = new CS_REST_Transactional_ClassicEmail($auth, $client_id);

echo "Get the list of classic groups...\n";
$result = $wrap->groups();
var_dump($result->response);

