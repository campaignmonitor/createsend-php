<?php
require_once "../../csrest_transactional_smartemail.php";

$auth = array("api_key" => "Your API Key");
$wrap = new CS_REST_Transactional_SmartEmail(NULL, $auth);

echo "\nGetting the list of smart emails...\n";

$result = $wrap->get_list();
echo "Found " . count($result->response) . " smart emails, here's the first in the list:\n";
var_dump($result->response);


echo "\nGetting the list smart emails, filtered by status...\n";

$total  = $wrap->get_list(array("status" => 'all'))->response;
$active = $wrap->get_list(array("status" => 'active'))->response;
$draft  = $wrap->get_list(array("status" => 'draft'))->response;
echo "Found " . count($active) .  " active and " . count($draft) . " draft smart emails, for a total of " . count($total) . " smart emails.\n";

