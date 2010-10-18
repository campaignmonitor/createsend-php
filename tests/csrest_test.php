<?php

require_once 'simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';
require_once '../csrest.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_JsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_TestBase extends UnitTestCase {
	var $mock_log;
	var $mock_serialiser;
	var $mock_transport;
	
	var $wrapper;
	
	var $format = 'mockjson';
	var $content_type = 'mock/json';
	var $transport_type = 'mock_cURL';
	var $api_key = 'not a real api key';
	var $protocol = 'hotpotatoes';
	var $api_host = 'api.test.createsend.com';
	var $log_level = CS_REST_LOG_NONE;
	
	var $base_route;
	
	function setUp() {
		$this->mock_log = &new MockCS_REST_Log();
		$this->mock_serialiser = &new MockCS_REST_JsonSerialiser();
		$this->mock_transport = &new MockCS_REST_CurlTransport();
		
		$this->mock_transport->setReturnValue('get_type', $this->transport_type);
		
		$this->mock_serialiser->setReturnValue('get_format', $this->format);
		$this->mock_serialiser->setReturnValue('get_content_type', $this->content_type);
		
		$this->base_route = $this->protocol.'://'.$this->api_host.'/api/v3/';
		
		$this->set_up_inner();
	}
	
	function set_up_inner() {		
		$this->wrapper = &new CS_REST_Wrapper_Base($this->api_key, $this->protocol, $this->log_level,
		    $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
	}
	
	function get_call_options($route, $method = 'GET') {
		return array (
		    'credentials' => $this->api_key.':nopass',
		    'userAgent' => 'CS_REST_Wrapper v'.CS_REST_WRAPPER_VERSION,
		    'contentType' => $this->mock_serialiser->get_content_type().'; charset=utf-8',
			'deserialise' => true,
			'host' => $this->api_host,
		    'route' => $route,
		    'method' => $method
		);
	}
	
	function general_get_test($wrapper_function, $call_options, $from_transport, $from_deserialisation) {
		$this->mock_transport->setReturnValue('make_call', $from_transport);
		$this->mock_transport->expectOnce('make_call', array(new IdenticalExpectation($call_options)));
		
		$this->mock_serialiser->setReturnValue('deserialise', $from_deserialisation);
		$this->mock_serialiser->expectOnce('deserialise', array(new IdenticalExpectation($from_transport)));
	    	
	    $result = $this->wrapper->$wrapper_function();
	    $this->assertIdentical($from_deserialisation, $result);		
	}
}

class CS_REST_TestWrapperBase extends CS_REST_TestBase {	
	function testget_timezones() {		
		$raw_result = 'some timezones';
		$deserialised = array('timezone1', 'timezone2');
		$call_options = $this->get_call_options(
		    $this->base_route.'timezones.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_timezones', $call_options, $raw_result, $deserialised);
	}
	
	function testget_systemdate() {
		$raw_result = 'system date';
		$call_options = $this->get_call_options(
		    $this->base_route.'systemdate.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_systemdate', $call_options, $raw_result, $raw_result);
	}
	
	function testget_countries() {
		$raw_result = 'some countries';
		$deserialised = array('Australia', 'Suid Afrika');
		$call_options = $this->get_call_options(
		    $this->base_route.'countries.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_countries', $call_options, $raw_result, $deserialised);
	}
	
	function testget_apikey() {
		$raw_result = 'another fake api key';
		$username = 'username';
		$password = 'password';
		$site_url = 'unit.test.createsend.com';
		
		$call_options = $this->get_call_options(
		    $this->base_route.'apikey.'.$this->mock_serialiser->get_format().'?siteurl='.$site_url);
		$call_options['credentials'] = $username.':'.$password;
		    		
		$this->mock_transport->setReturnValue('make_call', $raw_result);
		$this->mock_transport->expectOnce('make_call', array(new IdenticalExpectation($call_options)));
		
		$this->mock_serialiser->setReturnValue('deserialise', $raw_result);
		$this->mock_serialiser->expectOnce('deserialise', array(new IdenticalExpectation($raw_result)));
		
		$result = $this->wrapper->get_apikey($username, $password, $site_url);
		$this->assertIdentical($raw_result, $result);		
	}
	
	function testget_clients() {
		$raw_result = 'some clients';
		$deserialised = array('Curran & Hughes', 'Repsol');
		$call_options = $this->get_call_options(
		    $this->base_route.'clients.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_clients', $call_options, $raw_result, $deserialised);	
	}
}