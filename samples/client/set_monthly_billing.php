<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Your clients ID', 'Your API Key');

$result = $wrap->set_monthly_billing(array(
    'Currency' => 'USD',
    'ClientPays' => true,
    'MarkupPercentage' => 100
));

echo "Result of PUT /api/v3/clients/{id}/setmonthlybilling\n<br />";
if($wrap->was_successful($result)) {
    echo "Updated with Code ".$result['code'];
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
    echo '</pre>';
}