<?php

require_once '../../csrest_campaigns.php';

$wrap = new CS_REST_Campaigns(NULL, 'Your API Key');

$template_content = array(
  'Singlelines' => array(
    array(
      'Content' => 'This is a heading',
      'Alt' => 'This is alt text',
      'Href' => 'http://example.com/'
    )
  ),
  'Multilines' => array(
    array(
      'Content' => '<p>This is example</p><p>multiline <a href=\"http://example.com\">content</a>...</p>'
    )
  ),
  'Images' => array(
    array(
      'Content' => 'http://example.com/image.png',
      'Alt' => 'This is alt text for an image',
      'Href' => 'http://example.com/'
    )
  ),
  'Repeaters' => array(
    array(
      'Items' => array(
        array(
          'Layout' => 'My layout',
          'Singlelines' => array(
            array(
              'Content' => 'This is a repeater heading',
              'Alt' => 'This is alt text',
              'Href' => 'http://example.com/'
            )
          ),
          'Multilines' => array(
            array(
              'Content' => '<p>This is example</p><p>multiline <a href=\"http://example.com\">content</a>...</p>'
            )
          ),
          'Images' => array(
            array(
              'Content' => 'http://example.com/image.png',
              'Alt' => 'This is alt text for a repeater image',
              'Href' => 'http://example.com/'
            )
          )
        )
      )
    )
  )
);

$result = $wrap->create_from_template('Campaigns Client ID', array(
    'Subject' => 'Campaign Subject',
    'Name' => 'Campaign Name',
    'FromName' => 'Campaign From Name',
    'FromEmail' => 'Campaign From Email Address',
    'ReplyTo' => 'Campaign Reply To Email Address',
    'ListIDs' => array('First List', 'Second List'),
    'SegmentIDs' => array('First Segment', 'Second Segment'),
    'TemplateID' => 'Template ID',
    'TemplateContent' => $template_content
));

echo "Result of POST /api/v3/campaigns/{clientID}/fromtemplate\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}