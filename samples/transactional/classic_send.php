<?php
require_once "../../csrest_transactional_classicemail.php";

$auth = array("api_key" => "Your API Key");
$wrap = new CS_REST_Transactional_ClassicEmail($auth, NULL);

echo "\nSending a simple message...\n";

$simple_message = array(
  "From" => "support@example.com",
  "Subject" => "Test from createsend-php",
  "To" => "Joe Bloggs <joe@example.org>",
  "HTML" => "This is the HTML message body with a <a href='http://google.com'>link</a>."
);
$group_name = 'PHP test group'; # optional but great for reporting, should not be unique per message
$result = $wrap->send($simple_message, $group_name);
echo "\nSent! Here's the response:\n";
var_dump($result->response);


echo "\nSending a message with all the options...\n";

$complex_message = array(
  "From" => "ed@example.com",
  "ReplyTo" => "support@example.com",
  "Subject" => "Test from createsend-php",
  "To" => array(
    "Sam Jones <sam@example.org>",
    "jane@example.org"
  ),
  "CC" => array(
    "Mike <mike@example.com>",
    "Sally Perlis <sally@example.com>"
  ),
  "BCC" => array(
    "tim@example.com",
    "allison@example.com",
  ),
  "HTML" => "This is the HTML message body with a <a href='http://google.com'>link</a>.",
  "Text" => "Instead of using the auto-generated text from the HTML, you can supply your own.",
  "Attachments" => array(
    array(
      "Name" => "filename.gif",
      "Type" => "image/gif",
      "Content" => "R0lGODlhIAAgAKIAAP8AAJmZADNmAMzMAP//AAAAAP///wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMwMTQgNzkuMTU2Nzk3LCAyMDE0LzA4LzIwLTA5OjUzOjAyICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNCAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDowNzZGOUNGOUVDRDIxMUU0ODM2RjhGMjNCMTcxN0I2RiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDowNzZGOUNGQUVDRDIxMUU0ODM2RjhGMjNCMTcxN0I2RiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjA3NkY5Q0Y3RUNEMjExRTQ4MzZGOEYyM0IxNzE3QjZGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjA3NkY5Q0Y4RUNEMjExRTQ4MzZGOEYyM0IxNzE3QjZGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAAAAAAAsAAAAACAAIAAAA5loutz+MKpSpIWU3r1KCBW3eYQmWgWhmiemEgPbNqk6xDOd1XGYV77UzTfbTWC4nAHYQRKLu1VSuXxlpsodAFDAZrfcIbXDFXqhNacoQ3vZpuxHSJZ2zufyTqcunugdd00vQ0F4chQCAgYCaTcxiYuMMhGJFG89kYpFl5MzkoRPnpJskFSaDqctRoBxHEQsdGs0f7Qjq3utDwkAOw=="
    )
  )
);
$group_name = "PHP test group"; # optional, great for reporting, should not be unique message
$add_recipients_to_subscriber_list_ID = "6d0366fcee146ab9bdaf3247446bbfdd"; # optional, make sure you have permission
$options = array(
  "TrackOpens" => true,
  "TrackClicks" => true,
  "InlineCSS" => true,
); # all are true by default, but you can override
$result = $wrap->send($complex_message, $group_name, $add_recipients_to_subscriber_list_ID, $options);
echo "\nSent! Here's the response:\n";
var_dump($result->response);

