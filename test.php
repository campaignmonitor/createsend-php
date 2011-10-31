<?php
require_once('csrest_subscribers.php');

    $row->other_email = 'ben@kerve.co.uk';
    $row->billing_name_first = 'Ben';
    $row->billing_name_last = 'Smith';
   
   
    $wrap = new CS_REST_Subscribers('92aa94463366f16e74a3a718b67209fb', '207ff9948923cf26ad96b4f1e0a6fff7', 'http');
    $result = $wrap->add(array(
        'EmailAddress' => $row->other_email,
        'Name' => $row->billing_name_first.' '.$row->billing_name_last
    ));
   
    echo "Result of POST /api/v3/subscribers/{list id}.{format}\n<br />";
    if($result->was_successful()) {
        echo "Subscribed with code ".$result->http_status_code;
    } else {
        echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
        var_dump($result->response);
        echo '</pre>';
    }
