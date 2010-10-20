<?php

require_once 'csrest_campaigns';

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
    'Segments' => array(
        array(
            'ListID' => 'Segment ListID',
            'Name' => 'Segment Name'
        ),
        array(
            'ListID' => 'Segment ListID',
            'Name' => 'Segment Name'
        )
    )
));

echo "Result of POST /api/v3/campaigns/{clientID}\n<br />";
if($wrap->was_successful($result)) {
	echo "Created with ID\n<br />".$result['response'];
} else {
	echo 'Failed with code '.$result['code']."\n<br /><pre>";
	print_r($result['response']);
    echo '</pre>';
}