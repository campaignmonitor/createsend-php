<?php

require_once 'simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/serialisation.php';
require_once '../class/log.php';
require_once '../csrest_templates.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_JsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_TestTemplates extends CS_REST_TestBase {
    var $template_id = 'not a real template id';
    var $template_base_route;
    
    function set_up_inner() {
        $this->template_base_route = $this->base_route.'templates/'.$this->template_id;
        $this->wrapper = &new CS_REST_Templates($this->template_id, $this->api_key, $this->protocol, $this->log_level,
            $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }
    
    function testcreate() {
        $raw_result = 'the new template id';
        $client_id = 'not a real client id';
        $response_code = 200;
        
        $call_options = $this->get_call_options(
            $this->base_route.'templates/'.$client_id.'.'.$this->format, 'POST');    
                    
        $template = array (
            'Name' => 'ABC Widgets',
            'HtmlURL' => 'http://test.abc.net.au'
        );
        
        $this->mock_serialiser->setReturnValue('format_item', $template);        
        $this->mock_serialiser->expectOnce('format_item', array( 
            new IdenticalExpectation('Template'),
            new IdenticalExpectation($template)
        ));
                        
        $expected_result = array (
            'code' => $response_code, 
            'response' => 'the new template id'
        );
                        
        $call_options['data'] = 'template was serialised to this';
        $this->setup_transport_and_serialisation($expected_result, $call_options, 
            $raw_result, $raw_result, 
            'template was serialised to this', $template, $response_code);
            
        $result = $this->wrapper->create($client_id, $template);    
        
        $expected_result['response'] = $raw_result;
        $this->assertIdentical($expected_result, $result);      
    }
    
    function testupdate() {
        $raw_result = '';
        
        $call_options = $this->get_call_options(
            $this->template_base_route.'.'.$this->format, 'PUT');      
                    
        $template = array (
            'Name' => 'ABC Widgets',
            'HtmlURL' => 'http://test.abc.net.au'
        );
        
        $this->mock_serialiser->setReturnValue('format_item', $template);        
        $this->mock_serialiser->expectOnce('format_item', array( 
            new IdenticalExpectation('Template'),
            new IdenticalExpectation($template)
        ));
        
        $this->general_test_with_argument('update', $template, $call_options, 
            $raw_result, $raw_result, 'template was serialised to this');
    }
    
    function testget() {      
        $raw_result = 'template details';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options(
            $this->template_base_route.'.'.$this->format);
        
        $this->general_test('get', $call_options, $raw_result, $deserialised);
    }
    
    function testdelete() {
        $raw_result = '';
        
        $call_options = $this->get_call_options(
             $this->template_base_route.'.'.$this->format, 'DELETE');
            
        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }
}