<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestAdministrator extends CS_REST_TestAdministrator {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestAdministrator extends CS_REST_TestAdministrator {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestAdministrator extends CS_REST_TestBase {
    var $admins_base_route;

    function set_up_inner() {
        $this->admins_base_route = $this->base_route.'admins';
        $this->wrapper = new CS_REST_Administrators($this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testadd() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->admins_base_route.'.json', 'POST');

        $admin = array (
            'EmailAddress' => 'test@test.com',
            'Name' => 'Widget Man!'
        );

        $this->general_test_with_argument('add', $admin, $call_options,
			$raw_result, $raw_result, 'administrator was serialised to this');
    }

    function testupdate() {
        $raw_result = '';
        $email = 'test@test.com';
		$serialised_admin = 'subscriber data';
		
        $call_options = $this->get_call_options(
            $this->admins_base_route.'.json?email='.urlencode($email), 'PUT');

        $admin = array (
            'EmailAddress' => 'test2@test.com',
            'Name' => 'Widget Man!',
        );

        $transport_result = array (
            'code' => 200, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, 200);
        $call_options['data'] = $serialised_admin;
        
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, $serialised_admin, 
            $admin, 200);

        $result = $this->wrapper->update($email, $admin);
         
        $this->assertIdentical($expected_result, $result);
    }

    function testget() {
        $raw_result = 'administrator details';
        $deserialised = array(1,2,34,5);
        $response_code = 200;
        $email = 'test@test.com';

        $call_options = $this->get_call_options(
            $this->admins_base_route.'.json?email='.urlencode($email), 'GET');

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

        $call_options = $this->get_call_options($this->admins_base_route.'.json?email='.urlencode($email), 'DELETE');

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