<?php

use CreateSend\Wrapper\General;

$client_id = 8998879;
$redirect_uri = 'http://example.com/auth';
$scope = 'ViewReports,CreateCampaigns,SendCampaigns';
$state = 'some state data';

$authorize_url = General::authorize_url($client_id, $redirect_uri, $scope, $state);

echo "Redirect your users to: ".$authorize_url."\n<br />";
