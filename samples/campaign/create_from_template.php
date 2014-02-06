<?php

require_once '../../csrest_campaigns.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Campaigns(NULL, $auth);

$template_content = array(
  'Singlelines' => array(
    array(
      'Content' => 'This is a heading',
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

# $template_content as defined above would be used to fill the content of
# a template with markup similar to the following:
#
# <html>
#   <head><title>My Template</title></head>
#   <body>
#     <p><singleline>Enter heading...</singleline></p>
#     <div><multiline>Enter description...</multiline></div>
#     <img id="header-image" editable="true" width="500" />
#     <repeater>
#       <layout label="My layout">
#         <div class="repeater-item">
#           <p><singleline></singleline></p>
#           <div><multiline></multiline></div>
#           <img editable="true" width="500" />
#         </div>
#       </layout>
#     </repeater>
#     <p><unsubscribe>Unsubscribe</unsubscribe></p>
#   </body>
# </html>

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

echo "Result of POST /api/v3.1/campaigns/{clientID}/fromtemplate\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}