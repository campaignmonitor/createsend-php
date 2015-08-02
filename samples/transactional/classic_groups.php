<?php
require_once "../../csrest_transactional_classicemail.php";

$auth = array("api_key" => "9a01e798f90285898bfa9122b38420e51ecee5e260ff03f4");
// $wrap = new CS_REST_Transactional_ClassicEmail($auth);
$wrap = new CS_REST_Transactional_ClassicEmail($auth, null, 'https', null, 'api.devcreatesend.com');

echo "Get the list of classic groups...\n";
$result = $wrap->groups();
var_dump($result->response);

