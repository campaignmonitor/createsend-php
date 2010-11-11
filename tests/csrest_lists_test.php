<?php

require_once 'simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';
require_once '../csrest_lists.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_TestLists extends CS_REST_TestBase {
    var $list_id = 'not a real list id';
    var $list_base_route;

    function set_up_inner() {
        $this->list_base_route = $this->base_route.'lists/'.$this->list_id.'/';
        $this->wrapper = &new CS_REST_Lists($this->list_id, $this->api_key, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testcreate() {
        $raw_result = 'the new list id';
        $client_id = 'not a real client id';
        $response_code = 200;

        $call_options = $this->get_call_options($this->base_route.'lists/'.$client_id.'.json', 'POST');

        $list_info = array (
            'Title' => 'ABC Widgets',
            'UnsubscribeURL' => 'Widget Man!'
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'list info was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'list info was serialised to this', $list_info, $response_code);

        $result = $this->wrapper->create($client_id, $list_info);

        $this->assertIdentical($expected_result, $result);
    }

    function testupdate() {
        $raw_result = '';

        $call_options = $this->get_call_options(trim($this->list_base_route, '/').'.json', 'PUT');

        $list_info = array (
            'Title' => 'ABC Widgets',
            'UnsubscribeURL' => 'Widget Man!'
        );

        $this->general_test_with_argument('update', $list_info, $call_options,
            $raw_result, $raw_result, 'list info was serialised to this');
    }

    function testcreate_custom_field() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->list_base_route.'customfields.json', 'POST');

        $custom_field = array (
            'Key' => 'ABC Widgets',
            'Options' => array(1,2,3,4)
        );

        $this->general_test_with_argument('create_custom_field', $custom_field, $call_options,
        $raw_result, $raw_result, 'custom field was serialised to this');
    }

    function testdelete() {
        $raw_result = '';

        $call_options = $this->get_call_options(trim($this->list_base_route, '/').'.json', 'DELETE');

        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }

    function testdelete_custom_field() {
        $raw_result = '';
        $response_code = 200;
        $key = 'custom field key';

        $call_options = $this->get_call_options(
            $this->list_base_route.'customfields/'.rawurlencode($key).'.json', 'DELETE');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->delete_custom_field($key);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_custom_fields() {
        $raw_result = 'some custom fields';
        $deserialised = array('Custom Field 1', 'Custom Field 2');
        $call_options = $this->get_call_options($this->list_base_route.'customfields.json');

        $this->general_test('get_custom_fields', $call_options, $raw_result, $deserialised);
    }

    function testget_segments() {
        $raw_result = 'some segments';
        $deserialised = array('Segment 1', 'Segment 2');
        $call_options = $this->get_call_options($this->list_base_route.'segments.json');

        $this->general_test('get_segments', $call_options, $raw_result, $deserialised);
    }

    function testget_active() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->list_base_route.'active.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_active_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_bounced() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->list_base_route.'bounced.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_bounced_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_unsubscribed() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->list_base_route.'unsubscribed.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_unsubscribed_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget() {
        $raw_result = 'list details';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options(trim($this->list_base_route, '/').'.json');

        $this->general_test('get', $call_options, $raw_result, $deserialised);
    }

    function testget_stats() {
        $raw_result = 'list stats';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options($this->list_base_route.'stats.json');

        $this->general_test('get_stats', $call_options, $raw_result, $deserialised);
    }
}
