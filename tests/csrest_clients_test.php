<?php

require_once 'simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';
require_once '../csrest_clients.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_JsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_TestClients extends CS_REST_TestBase {
	var $client_id = 'not a real client id';
	
	function set_up_inner() {
		$this->base_route .= 'clients/'.$this->client_id.'/';
		$this->wrapper = &new CS_REST_Clients($this->client_id, $this->api_key, $this->protocol, $this->log_level,
		    $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
	}
	
	function testget_campaigns() {		
		$raw_result = 'some campaigns';
		$deserialised = array('Campaign 1', 'Campaign 2');
		$call_options = $this->get_call_options(
		    $this->base_route.'campaigns.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_campaigns', $call_options, $raw_result, $deserialised);
	}
	
	function testget_drafts() {		
		$raw_result = 'some drafts';
		$deserialised = array('Campaign 1', 'Campaign 2');
		$call_options = $this->get_call_options(
		    $this->base_route.'drafts.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_drafts', $call_options, $raw_result, $deserialised);
	}
	
	function testget_lists() {		
		$raw_result = 'some lists';
		$deserialised = array('List 1', 'List 2');
		$call_options = $this->get_call_options(
		    $this->base_route.'lists.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_lists', $call_options, $raw_result, $deserialised);
	}
	
	function testget_segments() {		
		$raw_result = 'some segments';
		$deserialised = array('Segment 1', 'Segment 2');
		$call_options = $this->get_call_options(
		    $this->base_route.'segments.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_segments', $call_options, $raw_result, $deserialised);
	}
	
	function testget_suppressionlist() {		
		$raw_result = 'some emails';
		$deserialised = array('dont@email.me', 'go@away.com');
		$call_options = $this->get_call_options(
		    $this->base_route.'suppressionlist.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_suppressionlist', $call_options, $raw_result, $deserialised);
	}
	
	function testget_templates() {		
		$raw_result = 'some templates';
		$deserialised = array('Template 1', 'Template 2');
		$call_options = $this->get_call_options(
		    $this->base_route.'templates.'.$this->mock_serialiser->get_format());
		
		$this->general_get_test('get_templates', $call_options, $raw_result, $deserialised);
	}
	
	function testdelete() {
		$raw_result = '';
	}
}