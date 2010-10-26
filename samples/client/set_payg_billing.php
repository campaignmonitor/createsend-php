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
if($wrap->was_successful($result)) {
    echo "Updated with Code ".$result['code'];
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
    echo '</pre>';
}