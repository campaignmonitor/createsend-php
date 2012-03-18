<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns(NULL, 'Your API Key');

$result = $wrap->create('Campaigns Client ID', array(
    'Subject' => 'Campaign Subject',
    'Name' => 'Campaign Name',
    'FromName' => 'Campaign From Name',
    'FromEmail' => 'Campaign From Email Address',
    'ReplyTo' => 'Campaign Reply To Email Address',
    'HtmlUrl' => 'Campaign HTML Import URL',
    'TextUrl' => 'Campaign Text Import URL',
    'ListIDs' => array('First List', 'Second List'),
    'SegmentIDs' => array('First Segment', 'Second Segment')
));

echo "Result of POST /api/v3/campaigns/{clientID}\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}