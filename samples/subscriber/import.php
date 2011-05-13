<?php

require_once '../../csrest_subscribers.php';

$wrap = new CS_REST_Subscribers('Your list ID', 'Your API Key');

$result = $wrap->import(array(
    array(
	    'EmailAddress' => 'Subscriber email',
	    'Name' => 'Subscriber name',
	    'CustomFields' => array(
            array(
                'Key' => 'Field Key',
                'Value' => 'Field Value'
            )
        )
	),
	array(
	    'EmailAddress' => '2nd Subscriber email',
	    'Name' => '2nd Subscriber name',
	    'CustomFields' => array(
	        array(
	            'Key' => 'Field Key',
	            'Value' => 'Field Value'
	        )
	    )
	)
), true);

echo "Result of POST /api/v3/subscribers/{list id}/import.{format}\n<br />";
if($result->was_successful()) {
    echo "Subscribed with results <pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
    
    if($result->response->TotalExistingSubscribers > 0) {
        echo 'Updated '.$result->response->TotalExistingSubscribers.' existing subscribers in the list';        
    } else if($result->response->TotalNewSubscribers > 0) {
        echo 'Added '.$result->response->TotalNewSubscribers.' to the list';
    } else if(count($result->response->DuplicateEmailsInSubmission) > 0) { 
        echo $result->response->DuplicateEmailsInSubmission.' were duplicated in the provided array.';
    }
    
    echo 'The following emails failed to import correctly.<pre>';
    var_dump($result->response->FailureDetails);
}
echo '</pre>';