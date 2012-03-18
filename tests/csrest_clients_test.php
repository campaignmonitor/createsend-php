<?php

require_once 'simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';
require_once '../csrest_clients.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_TestClients extends CS_REST_TestBase {
    var $client_id = 'not a real client id';
    var $client_base_route;

    function set_up_inner() {
        $this->client_base_route = $this->base_route.'clients/'.$this->client_id.'/';
        $this->wrapper = &new CS_REST_Clients($this->client_id, $this->api_key, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testget_campaigns() {
        $raw_result = 'some campaigns';
        $deserialised = array('Campaign 1', 'Campaign 2');
        $call_options = $this->get_call_options($this->client_base_route.'campaigns.json');

        $this->general_test('get_campaigns', $call_options, $raw_result, $deserialised);
    }

    function testget_scheduled() {
        $raw_result = 'some scheduled campaigns';
        $deserialised = array('Campaign 1', 'Campaign 2');
        $call_options = $this->get_call_options($this->client_base_route.'scheduled.json');

        $this->general_test('get_scheduled', $call_options, $raw_result, $deserialised);
    }

    function testget_drafts() {
        $raw_result = 'some drafts';
        $deserialised = array('Campaign 1', 'Campaign 2');
        $call_options = $this->get_call_options($this->client_base_route.'drafts.json');

        $this->general_test('get_drafts', $call_options, $raw_result, $deserialised);
    }

    function testget_lists() {
        $raw_result = 'some lists';
        $deserialised = array('List 1', 'List 2');
        $call_options = $this->get_call_options($this->client_base_route.'lists.json');

        $this->general_test('get_lists', $call_options, $raw_result, $deserialised);
    }

    function testget_segments() {
        $raw_result = 'some segments';
        $deserialised = array('Segment 1', 'Segment 2');
        $call_options = $this->get_call_options($this->client_base_route.'segments.json');

        $this->general_test('get_segments', $call_options, $raw_result, $deserialised);
    }

    function testget_suppressionlist() {
        $raw_result = 'some emails';
        $deserialised = array('dont@email.me', 'go@away.com');
        $call_options = $this->get_call_options($this->client_base_route.'suppressionlist.json');

        $this->general_test('get_suppressionlist', $call_options, $raw_result, $deserialised);
    }

    function testget_templates() {
        $raw_result = 'some templates';
        $deserialised = array('Template 1', 'Template 2');
        $call_options = $this->get_call_options($this->client_base_route.'templates.json');

        $this->general_test('get_templates', $call_options, $raw_result, $deserialised);
    }

    function testget() {
        $raw_result = 'client data';
        $deserialised = array('CompanyName' => 'Widget Land');
        $call_options = $this->get_call_options(trim($this->client_base_route, '/').'.json');

        $this->general_test('get', $call_options, $raw_result, $deserialised);
    }

    function testdelete() {
        $raw_result = '';

        $call_options = $this->get_call_options(
            trim($this->client_base_route, '/').'.json', 'DELETE');
        	
        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }

    function testcreate() {
        $raw_result = 'the new client id';

        $call_options = $this->get_call_options($this->base_route.'clients.json', 'POST');
         
        $client_data = array (
	        'CompanyName' => 'ABC Widgets',
		    'ContactName' => 'Widget Man!',
		    'EmailAddress' => 'widgets@abc.net.au'
		);

	    $this->general_test_with_argument('create', $client_data, $call_options,
	        $raw_result, $raw_result, 'client data was serialised to this');
    }

    function testset_basics() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->client_base_route.'setbasics.json', 'PUT');
         
        $client_data = array (
            'CompanyName' => 'ABC Widgets',
            'ContactName' => 'Widget Man!',
            'EmailAddress' => 'widgets@abc.net.au'
        );

        $this->general_test_with_argument('set_basics', $client_data, $call_options,
            $raw_result, $raw_result, 'client data was serialised to this');
    }

    function testset_access() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->client_base_route.'setaccess.json', 'PUT');
         
        $client_data = array (
            'Username' => 'ABCWidgets',
            'Password' => 'Widget Man!',
            'AccessLevel' => 4
        );

        $this->general_test_with_argument('set_access', $client_data, $call_options,
            $raw_result, $raw_result, 'client data was serialised to this');
    }

    function testset_payg() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->client_base_route.'setpaygbilling.json', 'PUT');
         
        $client_data = array (
            'Current' => 'PZD',
            'ClientPays' => true,
            'MarkupPercentage' => 1000
        );

        $this->general_test_with_argument('set_payg_billing', $client_data, $call_options,
            $raw_result, $raw_result, 'client data was serialised to this');
    }

    function testset_monthly() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->client_base_route.'setmonthlybilling.json', 'PUT');
         
        $client_data = array (
            'Current' => 'PZD',
            'ClientPays' => true,
            'MarkupPercentage' => 1000
        );

        $this->general_test_with_argument('set_monthly_billing', $client_data, $call_options,
            $raw_result, $raw_result, 'client data was serialised to this');
    }
}