<?php

require_once '../../csrest_general.php';

$client_id = 8998879;
$redirect_uri = 'http://example.com/auth';
$scope = 'ViewReports,CreateCampaigns,SendCampaigns';
$state = 'some state data';

$authorize_url = CS_REST_General::authorize_url($client_id, $redirect_uri, $scope, $state);

echo "Redirect your users to: ".$authorize_url."\n<br />";
