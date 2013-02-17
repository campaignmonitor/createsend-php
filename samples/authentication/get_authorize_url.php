<?php

require_once '../../csrest_general.php';

$client_id = 8998879;
$client_secret = 'iou0q9wud0q9wd0q9wid0q9iwd0q9wid0q9wdqwd';
$redirect_uri = 'http://example.com/auth';
$scope = 'ViewReports,CreateCampaigns,SendCampaigns';
$state = 'some state data';

$authorize_url = CS_REST_General::authorize_url($client_id, $client_secret, $redirect_uri, $scope, $state);

echo "Redirect your users to: ".$authorize_url."\n<br />";
