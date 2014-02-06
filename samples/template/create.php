<?php

require_once '../../csrest_templates.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Templates(NULL, $auth);

$result = $wrap->create('Templates Client ID', array(
    'Name' => 'Template Name',
    'HtmlPageURL' => 'Template HTML Url',
    'ZipFileURL' => 'Template Images Zip URL'
));

echo "Result of POST /api/v3.1/templates/{clientID}\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}