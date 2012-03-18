<?php

require_once 'simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';
require_once '../csrest_general.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_TestBase extends UnitTestCase {
    var $mock_log;
    var $mock_serialiser;
    var $mock_transport;

    var $wrapper;

    var $serialisation_type = 'mockjson';
    var $transport_type = 'mock_cURL';
    var $api_key = 'not a real api key';
    var $protocol = 'hotpotatoes';
    var $api_host = 'api.test.createsend.com';
    var $log_level = CS_REST_LOG_NONE;

    var $base_route;

    function setUp() {
        $this->mock_log = &new MockCS_REST_Log();
        $this->mock_serialiser = &new MockCS_REST_NativeJsonSerialiser();
        $this->mock_transport = &new MockCS_REST_CurlTransport();

        $this->mock_transport->setReturnValue('get_type', $this->transport_type);
        $this->mock_serialiser->setReturnValue('get_type', $this->serialisation_type);

        $this->base_route = $this->protocol.'://'.$this->api_host.'/api/v3/';

        $this->set_up_inner();
    }

    function set_up_inner() {
        $this->wrapper = &new CS_REST_General($this->api_key, $this->protocol, $this->log_level,
            $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function get_call_options($route, $method = 'GET') {
        return array (
		    'credentials' => $this->api_key.':nopass',
		    'userAgent' => 'CS_REST_Wrapper v'.CS_REST_WRAPPER_VERSION.
		        ' PHPv'.phpversion().' over '.$this->transport_type.' with '.$this->serialisation_type,
		    'contentType' => 'application/json; charset=utf-8',
			'deserialise' => true,
			'host' => $this->api_host,
            'protocol' => $this->protocol,
		    'route' => $route,
		    'method' => $method
        );
    }

    function setup_transport_and_serialisation($make_call_result, $call_options,
        $deserialise_result, $deserialise_input, $serialise_result = NULL, $serialise_input = NULL) {
        	
        $this->mock_transport->setReturnValue('make_call', $make_call_result);
        $this->mock_transport->expectOnce('make_call', array(new IdenticalExpectation($call_options)));

        $this->mock_serialiser->setReturnValue('deserialise', $deserialise_result);
        $this->mock_serialiser->expectOnce('deserialise', array(new IdenticalExpectation($deserialise_input)));

        if(!is_null($serialise_result)) {
            $this->mock_serialiser->setReturnValue('serialise', $serialise_result);
            $this->mock_serialiser->expectOnce('serialise', array(new IdenticalExpectation($serialise_input)));
        }
    }

    function general_test($wrapper_function, $call_options, $from_transport,
        $from_deserialisation, $response_code = 200) {

        $transport_result = array (
	        'code' => $response_code, 
	        'response' => $from_transport
        );
        
        $expected_result = new CS_REST_Wrapper_Result($from_deserialisation, $response_code);
         
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $from_deserialisation, $from_transport, NULL, NULL, $response_code);

        $result = $this->wrapper->$wrapper_function();
         
        $this->assertIdentical($expected_result, $result);
    }

    function general_test_with_argument($wrapper_function, $function_argument, $call_options,
        $from_transport, $from_deserialisation,
        $from_serialisation = 'serialised', $response_code = 200) {

        $transport_result = array (
            'code' => $response_code, 
            'response' => $from_transport
        );
        
        $expected_result = new CS_REST_Wrapper_Result($from_deserialisation, $response_code);
         
        if(!is_null($from_serialisation)) {
            $call_options['data'] = $from_serialisation;
        }
        
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $from_deserialisation, $from_transport, $from_serialisation, 
            $function_argument, $response_code);

        $result = $this->wrapper->$wrapper_function($function_argument);
         
        $this->assertIdentical($expected_result, $result);
    }
}

class CS_REST_TestGeneral extends CS_REST_TestBase {
    
    function testget_timezones() {
        $raw_result = 'some timezones';
        $deserialised = array('timezone1', 'timezone2');
        $call_options = $this->get_call_options($this->base_route.'timezones.json');

        $this->general_test('get_timezones', $call_options, $raw_result, $deserialised);
    }

    function testget_systemdate() {
        $raw_result = 'system date';
        $call_options = $this->get_call_options($this->base_route.'systemdate.json');

        $this->general_test('get_systemdate', $call_options, $raw_result, $raw_result);
    }

    function testget_countries() {
        $raw_result = 'some countries';
        $deserialised = array('Australia', 'Suid Afrika');
        $call_options = $this->get_call_options($this->base_route.'countries.json');

        $this->general_test('get_countries', $call_options, $raw_result, $deserialised);
    }

    function testget_apikey() {
        $raw_result = 'another fake api key';
        $username = 'username';
        $password = 'password';
        $site_url = 'unit.test.createsend.com';

        $call_options = $this->get_call_options($this->base_route.'apikey.json?siteurl='.$site_url);
        $call_options['credentials'] = $username.':'.$password;

        $transport_result = array (
            'code' => 200, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, 200);
                 
        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $raw_result, $raw_result);

        $result = $this->wrapper->get_apikey($username, $password, $site_url);
        $this->assertIdentical($expected_result, $result);
    }

    function testget_clients() {
        $raw_result = 'some clients';
        $deserialised = array('Curran & Hughes', 'Repsol');
        $call_options = $this->get_call_options($this->base_route.'clients.json');

        $this->general_test('get_clients', $call_options, $raw_result, $deserialised);
    }
}