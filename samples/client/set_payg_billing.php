<?php

require_once '../../csrest_clients.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Clients('Your client ID', $auth);

/*
 * Specific markup values can be set via the
 *
 * MarkupOnDelivery
 * MarkupPerRecipient
 * MarkupOnDesignSpamTest
 *
 * fields
 */
$result = $wrap->set_payg_billing(array(
    'Currency' => 'USD',
    'ClientPays' => true,
    'MarkupPercentage' => 100,
    'CanPurchaseCredits' => false/*,
'MarkupOnDelivery' => 4,
'MarkupPerRecipient' => 3,
'MarkupOnDesignSpamTest' => 7 */
));

echo "Result of PUT /api/v3.1/clients/{id}/setpaygbilling\n<br />";
if($result->was_successful()) {
    echo "Updated with Code ".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}