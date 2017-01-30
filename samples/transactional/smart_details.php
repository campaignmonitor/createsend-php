<?php

use CreateSend\Wrapper\Transactional\SmartEmail;

$auth = array("api_key" => "Your API Key");
$smart_email_id = "Smart Email ID goes here"; #grab it from the URL
$wrap = new SmartEmail($smart_email_id, $auth);

echo "\nGetting the details of the smart email...\n";
$result = $wrap->get_details();
var_dump($result->response);

