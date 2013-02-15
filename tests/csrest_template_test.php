<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/lastcraft/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestTemplates extends CS_REST_TestTemplates {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestTemplates extends CS_REST_TestTemplates {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestTemplates extends CS_REST_TestBase {
    var $template_id = 'not a real template id';
    var $template_base_route;

    function set_up_inner() {
        $this->template_base_route = $this->base_route.'templates/'.$this->template_id;
        $this->wrapper = new CS_REST_Templates($this->template_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testcreate() {
        $raw_result = 'the new template id';
        $client_id = 'not a real client id';
        $response_code = 200;

        $call_options = $this->get_call_options(
            $this->base_route.'templates/'.$client_id.'.json', 'POST');

        $template = array (
            'Name' => 'ABC Widgets',
            'HtmlURL' => 'http://test.abc.net.au'
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'template was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $raw_result, $raw_result,
        'template was serialised to this', $template, $response_code);

        $result = $this->wrapper->create($client_id, $template);

        $this->assertIdentical($expected_result, $result);
    }

    function testupdate() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->template_base_route.'.json', 'PUT');

        $template = array (
            'Name' => 'ABC Widgets',
            'HtmlURL' => 'http://test.abc.net.au'
        );

        $this->general_test_with_argument('update', $template, $call_options,
            $raw_result, $raw_result, 'template was serialised to this');
    }

    function testget() {
        $raw_result = 'template details';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options($this->template_base_route.'.json');

        $this->general_test('get', $call_options, $raw_result, $deserialised);
    }

    function testdelete() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->template_base_route.'.json', 'DELETE');

        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }
}