<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestEvents extends CS_REST_TestEvents {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestEvents extends CS_REST_TestEvents {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestEvents extends CS_REST_TestBase {
     var $client_id = 'not a real client id';

    function set_up_inner() {
        $this->wrapper = new CS_REST_Events($this->auth, $this->client_id, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }
   
    function testtrack() {
        $raw_result = 'send event';
        $deserialised = array('EventID'=> '56a3b255-8bd5-491f-b241-fb6ce7e4ccbf');
        $response_code = 202;

        $contact = array('ContactID' => array('Email' => 'test@email.com'));
        $event_type = 'test';

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );

        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);
    	
    	$this->setup_transport_and_serialisation($transport_result, $call_options,
    			$raw_result, $raw_result, '', '', $response_code);
    	
    	$result = $this->wrapper->track($contact, $event_type, $data);
    	
    	$this->assertIdentical($expected_result, $result);      
    }
}