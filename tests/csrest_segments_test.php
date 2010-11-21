<?php

require_once 'simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';
require_once '../csrest_segments.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_TestSegments extends CS_REST_TestBase {
    var $segment_id = 'not a real segment id';
    var $segment_base_route;

    function set_up_inner() {
        $this->segment_base_route = $this->base_route.'segments/'.$this->segment_id.'/';
        $this->wrapper = &new CS_REST_Segments($this->segment_id, $this->api_key, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testget() {
        $raw_result = 'segment details';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options(trim($this->segment_base_route, '/').'.json');

        $this->general_test('get', $call_options, $raw_result, $deserialised);
    }

    function testget_segment_subscribers() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $segment_id = 'abc123';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->segment_base_route.'active.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }
    
    function testclear_rules() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->segment_base_route.'rules.json', 'DELETE');

        $this->general_test('clear_rules', $call_options, $raw_result, $raw_result);        
    }
    
    function testdelete() {
        $raw_result = '';

        $call_options = $this->get_call_options(trim($this->segment_base_route, '/').'.json', 'DELETE');

        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }
}