<?php

require_once '../../csrest_clients.php';

$wrap = new CS_REST_Clients('Your clients ID', 'Your API Key');

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

echo "Result of PUT /api/v3/clients/{id}/setpaygbilling\n<br />";
if($result->was_successful()) {
    echo "Updated with Code ".$result->http_status_code;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}