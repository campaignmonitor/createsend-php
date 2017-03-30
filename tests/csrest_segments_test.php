<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestSegments extends CS_REST_TestSegments {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestSegments extends CS_REST_TestSegments {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestSegments extends CS_REST_TestBase {
    var $segment_id = 'not a real segment id';
    var $segment_base_route;

    function set_up_inner() {
        $this->segment_base_route = $this->base_route.'segments/'.$this->segment_id;
        $this->wrapper = new CS_REST_Segments($this->segment_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testcreate() {
        $raw_result = 'the new segment id';
        $client_id = 'not a real list id';
        $response_code = 201;

        $call_options = $this->get_call_options(
            $this->base_route.'segments/'.$client_id.'.json', 'POST');

        $segment = array (
            'Title' => 'ABC Widgets Subscribers',
            'RuleGroups' => array(
                array(
                    'Rules' => array(
                        array(
                            'RuleType' => 'EmailAddress',
                            'Clause' => 'CONTAINS abcwidgets.com'
                        )
                    )
                )
            )
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'segment was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result,
        'segment was serialised to this', $segment, $response_code);

        $result = $this->wrapper->create($client_id, $segment);

        $this->assertIdentical($expected_result, $result);
    }

    function testupdate() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->segment_base_route.'.json', 'PUT');

        $segment = array (
            'Title' => 'ABC Widgets Subscribers',
            'RuleGroups' => array(
                array(
                    'Rules' => array(
                        array(
                            'RuleType' => 'EmailAddress',
                            'Clause' => 'CONTAINS abcwidgets.com'
                        )
                    )
                )
            )
        );

        $this->general_test_with_argument('update', $segment, $call_options,
            $raw_result, $raw_result, 'segment was serialised to this');
    }

    function testadd_rulegroup() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->segment_base_route.'/rules.json', 'POST');

        $rulegroup = array(
            'Rules' => array(
                array(
                    'RuleType' => 'EmailAddress',
                    'Clause' => 'CONTAINS abcwidgets.com'
                )
            )
        );

        $this->general_test_with_argument('add_rulegroup', $rulegroup, $call_options,
            $raw_result, $raw_result, 'rulegroup was serialised to this');
    }

    function testget() {
        $raw_result = 'segment details';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options($this->segment_base_route.'.json');

        $this->general_test('get', $call_options, $raw_result, $deserialised);
    }

    function testget_segment_subscribers() {
        $raw_result = 'some subscribers';
        $segment_id = 'abc123';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->segment_base_route.'/active.json?date=');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_subscribers();

        $this->assertIdentical($expected_result, $result);
    }
    
    function testclear_rules() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->segment_base_route.'/rules.json', 'DELETE');

        $this->general_test('clear_rules', $call_options, $raw_result, $raw_result);        
    }
    
    function testdelete() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->segment_base_route.'.json', 'DELETE');

        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }
}