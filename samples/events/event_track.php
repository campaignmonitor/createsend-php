<?php
require_once "../../csrest_events.php";

$auth = array("api_key" => "Your API Key");
$client_id = "Your Client ID";
$wrap = new CS_REST_Events($auth, $client_id);

echo "\nSending a simple event...\n";

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

$result = $wrap->track($contact, $event_type, $event_data);
echo "\nEvent Sent! Here's the response:\n";
var_dump($result);


