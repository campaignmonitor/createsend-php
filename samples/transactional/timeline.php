<?php
require_once "../../csrest_transactional_timeline.php";

$auth = array("api_key" => "Your API Key");
$wrap = new CS_REST_Transactional_Timeline($auth);


echo "\nGetting the statistics with the default parameters...\n";
$result = $wrap->statistics();
var_dump($result->response);


echo "\nGetting the statistics, filtered to a classic group...\n";
$result = $wrap->statistics(array(
  "from" => "2015-01-01",
  "to" => "2015-06-30",
  "timezone" => "utc",
  "group" => "PHP Test Group"
));
var_dump($result->response);


echo "\nGetting the statistics, filtered to a smart email...\n";
$smart_email_id = "94b2a1a5-6754-416b-a87f-1edb81c460a2"; #grab it from the URL
$result = $wrap->statistics(array(
  "from" => "2015-01-01",
  "to" => "2015-06-30",
  "timezone" => "client",
  "smartEmailID" => $smart_email_id
));
var_dump($result->response);


echo "\nGetting the most recent sent messages...\n";
$result = $wrap->messages();
$last_message_id = $result->response[0]->MessageID;
echo "\nHere's the first:\n";
var_dump($result->response[0]);


echo "\nGetting the most recent messages for a smart email, with all the options...\n";
$result = $wrap->messages(array(
  "status" => 'all',
  "count" => 200,
  "sentBeforeID" => NULL, # message ID
  "sentAfterID" => NULL, # message ID
  "smartEmailID" => '94b2a1a5-6754-416b-a87f-1edb81c460a2',
));
$last_message_id = $result->response[0]->MessageID;
echo "\nHere's the first:\n";
var_dump($result->response[0]);


echo "\nGetting the most recent messages for a classic email, with all the options...\n";
$result = $wrap->messages(array(
  "status" => 'all',
  "count" => 200,
  "sentBeforeID" => NULL, # message ID
  "sentAfterID" => NULL, # message ID
  "group" => 'PHP test group',
));
$last_message_id = $result->response[0]->MessageID;
echo "\nHere's the first:\n";
var_dump($result->response[0]);


echo "\nGetting the details of the most recent message...\n";
$result = $wrap->details($last_message_id);
var_dump($result->response);


echo "\nGetting the message details with details of opens and clicks...\n";
$result = $wrap->details($last_message_id, true);
var_dump($result->response);


echo "\nResending message...\n";
$result = $wrap->resend($last_message_id);
var_dump($result->response);

