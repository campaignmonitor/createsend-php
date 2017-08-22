<?php
require_once "../../csrest_events.php";

$auth = array("api_key" => "sample api key");
$client_id = "sample client id";
$api_event_type = "identify";
$wrap = new CS_REST_Events($auth, $client_id, $api_event_type);

echo "\nSending a $api_event_type event...\n";

$contact = "joe@example.org";
$event_type = "checkout";
$event_data = array(
  "Page" => "/cart/checkout",
  "Items" => array(
      array(
      "Description" => "Rubber Widget",
      "Quantity" => 1,
      "Price" => 300,
      ),
      array(
      "Description" => "Paint 1L",
      "Quantity" => 10,
      "Price" => 1,
      ),
  ),
  "User" => "joe@example.org",
  "CardType" => "VISA",
);

if (strcmp($wrap->_event_type, "identify") === 0) {
    // `Identify` event
    $anon_id = "abcd";
    $result = $wrap->track($contact, $event_type, $anon_id, $event_data);
} else {
    // Non `identify` event
    $result = $wrap->track($contact, $event_type, NULL, $event_data);
}
echo "\nEvent Sent! Here's the response:\n";
var_dump($result);


