<?php

require_once '../../csrest_lists.php';

$wrap = new CS_REST_Lists('List ID', 'Your API Key');

$result = $wrap->update(array(
    'Title' => 'List Title',
    'UnsubscribePage' => 'List unsubscribe page',
    'ConfirmedOptIn' => true,
    'ConfirmationSuccessPage' => 'List confirmation success page'
));

echo "Result of PUT /api/v3/lists/{ID}\n<br />";
if($wrap->was_successful($result)) {
    echo "Updated with code\n<br />".$result['code'];
} else {
    echo 'Failed with code '.$result['code']."\n<br /><pre>";
    print_r($result['response']);
    echo '</pre>';
}