<?php
require_once "../../csrest_transactional_smartemail.php";

$auth = array("api_key" => "Your API Key");

$smart_email_id = "Smart Email ID goes here"; #grab it from the URL
$wrap = new CS_REST_Transactional_SmartEmail($smart_email_id, $auth);

echo "\nGetting the details of the smart email...\n";
$result = $wrap->get_details($smart_email_id);
var_dump($result->response);

