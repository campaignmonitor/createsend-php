<?php

require_once '../../csrest_templates.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Templates('Template ID', $auth);

$result = $wrap->update(array(
    'Name' => 'Template Name',
    'HtmlPageURL' => 'Template HTML Url',
    'ZipFileURL' => 'Template Images Zip URL'
));

echo "Result of PUT /api/v3.1/templates/{ID}\n<br />";
if($result->was_successful()) {
    echo "Updated with code\n<br />".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}