<?php

require_once '../../csrest_subscribers.php';

$auth = array(
    'access_token' => 'your access token',
    'refresh_token' => 'your refresh token');
$wrap = new CS_REST_Subscribers('Your list ID', $auth);

$result = $wrap->import(array(
    array(
	    'EmailAddress' => 'Subscriber email',
	    'Name' => 'Subscriber name',
	    'CustomFields' => array(
	        array(
	            'Key' => 'Field 1 Key',
	            'Value' => 'Field Value'
	        ),
	        array(
	            'Key' => 'Field 2 Key',
	            'Value' => 'Field Value'
	        ),
	        array(
	            'Key' => 'Multi Option Field 1',
	            'Value' => 'Option 1'
	        ),
	        array(
	            'Key' => 'Multi Option Field 1',
	            'Value' => 'Option 2'
	        )
            )
	),
	array(
	    'EmailAddress' => '2nd Subscriber email',
	    'Name' => '2nd Subscriber name',
	    'CustomFields' => array(
	        array(
	            'Key' => 'Field 1 Key',
	            'Value' => 'Field Value'
	        ),
	        array(
	            'Key' => 'Field 2 Key',
	            'Value' => 'Field Value'
	        ),
	        array(
	            'Key' => 'Multi Option Field 1',
	            'Value' => 'Option 1'
	        ),
	        array(
	            'Key' => 'Multi Option Field 1',
	            'Value' => 'Option 2'
	        )
	    )
	)
), false);

echo "Result of POST /api/v3.1/subscribers/{list id}/import.{format}\n<br />";
if($result->was_successful()) {
    echo "Subscribed with results <pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
    
    if($result->response->ResultData->TotalExistingSubscribers > 0) {
        echo 'Updated '.$result->response->ResultData->TotalExistingSubscribers.' existing subscribers in the list';        
    } else if($result->response->ResultData->TotalNewSubscribers > 0) {
        echo 'Added '.$result->response->ResultData->TotalNewSubscribers.' to the list';
    } else if(count($result->response->ResultData->DuplicateEmailsInSubmission) > 0) { 
        echo $result->response->ResultData->DuplicateEmailsInSubmission.' were duplicated in the provided array.';
    }
    
    echo 'The following emails failed to import correctly.<pre>';
    var_dump($result->response->ResultData->FailureDetails);
}
echo '</pre>';
