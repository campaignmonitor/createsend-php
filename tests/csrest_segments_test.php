<?php

require_once 'simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';
require_once '../csrest_segments.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_JsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_TestSegments extends CS_REST_TestBase {
    var $segment_id = 'not a real segment id';
    var $segment_base_route;

    function set_up_inner() {
        $this->segment_base_route = $this->base_route.'segments/'.$this->segment_id.'/';
        $this->wrapper = &new CS_REST_Segments($this->segment_id, $this->api_key, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testget_segment_subscribers() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $segment_id = 'abc123';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options(
        $this->segment_base_route.'active.'.$this->format.'?date='.$since);

        $expected_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );

        $this->setup_transport_and_serialisation($expected_result, $call_options,
        $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_subscribers($since);

        $expected_result['response'] = $deserialised;
        $this->assertIdentical($expected_result, $result);
    }
}