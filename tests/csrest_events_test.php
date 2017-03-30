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
    var $client_id = 'fake';
    var $events_base_route;

    function set_up_inner() {
        $this->events_base_route =  $this->base_route.'events/'.$this->client_id.'/'; 
        $this->wrapper = new CS_REST_Events($this->auth, $this->client_id, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }
   
    function testtrack() {

        $raw_result = '{"EventID":"56a3b255-8bd5-491f-b241-fb6ce7e4ccbf"}';
        $email = 'test@email.com';
        $event_type = 'test';
        $data = array();


        $deserialised = array('EventID' => '56a3b255-8bd5-491f-b241-fb6ce7e4ccbf');
        $response_code = 202;
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );

        $input_payload = array('Data' => $data);
        

        $call_options = $this->get_call_options($this->events_base_route.'track', 'POST');
        $call_options['data'] = $input_payload;
        
        $this->setup_transport_and_serialisation($transport_result, $call_options,  $deserialised, $input_payload);


    	$result = $this->wrapper->track($email, $event_type, $data);    	

    	$this->assertIdentical($expected_result, $result);      
    }
}