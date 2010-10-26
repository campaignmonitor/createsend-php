<?php

require_once '../../csrest_templates.php';

$wrap = new CS_REST_Templates(NULL, 'Your API Key');

$result = $wrap->create('Templates Client ID', array(
    'Name' => 'Template Name',
    'HtmlPageURL' => 'Template HTML Url',
    'ZipFileURL' => 'Template Images Zip URL',
    'ScreenshotURL' => 'Template Screenshot URL'
));

echo "Result of POST /api/v3/templates/{clientID}\n<br />";
if($wrap->was_successful($result)) {
    echo "Created with ID\n<br />".$result['response'];
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
    echo '</pre>';
}