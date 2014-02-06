<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns(NULL, $auth);

$result = $wrap->create('Campaigns Client ID', array(
    'Subject' => 'Campaign Subject',
    'Name' => 'Campaign Name',
    'FromName' => 'Campaign From Name',
    'FromEmail' => 'Campaign From Email Address',
    'ReplyTo' => 'Campaign Reply To Email Address',
    'HtmlUrl' => 'Campaign HTML Import URL',
    # 'TextUrl' => 'Optional campaign text import URL',
    'ListIDs' => array('First List', 'Second List'),
    'SegmentIDs' => array('First Segment', 'Second Segment')
));

echo "Result of POST /api/v3.1/campaigns/{clientID}\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}