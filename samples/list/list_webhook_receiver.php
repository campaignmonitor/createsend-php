<?php

require_once '../../class/serialisation.php';
require_once '../../class/log.php';

// Get a serialiser for the webhook data - We assume here that we're dealing with json
$serialiser = CS_REST_SERIALISATION_get_available(new CS_REST_Log(CS_REST_LOG_NONE));

// Read all the posted data from the input stream
$raw_post = file_get_contents("php://input");

// We can log the raw data straight to disk
$raw_log = fopen('raw_log.txt', 'a') or die('Can\'t open raw log');
fwrite($raw_log, date('H:i:s').$raw_post."\n\n\n");
fclose($raw_log);

// And deserialise the data
$deserialised_data = $serialiser->deserialise($raw_post);

$parsed_log = fopen('parsed_log.txt', 'a') or die('Can\'t open parsed log');

fwrite($parsed_log, date('H:i:s').' Got hook data for list: '.$deserialised_data->ListID."\n");

// And now just do something with the data
foreach ($deserialised_data->Events as $event) {
    fwrite($parsed_log, 'Got '.$event->Type.' event for: '.$event->EmailAddress."\n");
    fwrite($parsed_log, var_export($event, true));
}

fclose($parsed_log);
?>
