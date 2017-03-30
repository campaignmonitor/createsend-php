<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestPeople extends CS_REST_TestPeople {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestPeople extends CS_REST_TestPeople {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestPeople extends CS_REST_TestBase {
    var $client_id = 'not a real client id';
    var $people_base_route;

    function set_up_inner() {
        $this->people_base_route = $this->base_route.'clients/'.$this->client_id . '/people';
        $this->wrapper = new CS_REST_People($this->client_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testadd() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->people_base_route.'.json', 'POST');

        $person = array (
            'EmailAddress' => 'test@test.com',
            'Name' => 'Widget Man!',
        	'AccessLevel' => 0
        );

        $this->general_test_with_argument('add', $person, $call_options,
			$raw_result, $raw_result, 'person was serialised to this');
    }

    function testupdate() {
        $raw_result = '';
        $email = 'test@test.com';
		$serialised_person = 'subscriber data';
		
        $call_options = $this->get_call_options(
            $this->people_base_route.'.json?email='.urlencode($email), 'PUT');

        $person = array (
            'EmailAddress' => 'test2@test.com',
            'Name' => 'Widget Man!',
        	'AccessLevel' => 0
        );

        $transport_result = array (
            'code' => 200, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, 200);
        $call_options['data'] = $serialised_person;
        
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, $serialised_person, 
            $person, 200);

        $result = $this->wrapper->update($email, $person);
         
        $this->assertIdentical($expected_result, $result);
    }

    function testget() {
        $raw_result = 'person details';
        $deserialised = array(1,2,34,5);
        $response_code = 200;
        $email = 'test@test.com';

        $call_options = $this->get_call_options(
            $this->people_base_route.'.json?email='.urlencode($email), 'GET');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get($email);

        $this->assertIdentical($expected_result, $result);
    }


    function testdelete() {
        $raw_result = '';
        $response_code = 200;
        $email = 'test@test.com';

        $call_options = $this->get_call_options($this->people_base_route.'.json?email='.urlencode($email), 'DELETE');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $raw_result, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->delete($email);

        $this->assertIdentical($expected_result, $result);
    }
}