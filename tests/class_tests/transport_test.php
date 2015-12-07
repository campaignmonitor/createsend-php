<?php

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/lastcraft/simpletest/autorun.php';
require_once '../class/transport.php';
require_once '../class/log.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_SocketWrapper');
@Mock::generatePartial(
    'CS_REST_SocketTransport', 
    'PartialSocketTransport',
    array('_build_request', '_get_status_code')
);

class CS_REST_TestSocketTransport extends UnitTestCase {
    var $mock_wrapper;
    var $mock_log;

    var $transport;
    var $partial;

    function setUp() {
        $this->mock_log = new MockCS_REST_Log($this);
        $this->mock_wrapper = new MockCS_REST_SocketWrapper($this);

        $this->transport = new CS_REST_SocketTransport($this->mock_log, $this->mock_wrapper);
        $this->partial = new PartialSocketTransport($this);
        $this->partial->__construct($this->mock_log, $this->mock_wrapper);
    }

    function test_make_call_http() {
        $this->make_call_base('http', 80, '');
    }

    function test_make_call_https() {
        $this->make_call_base('https', 443, 'ssl://');
    }

    function make_call_base($protocol, $port, $domain_prefix) {
        $host = 'api.test.createsend.com';
        $path = '/path/to/stuff';
        $call_options = array(
            'method' => 'CONJURE',
            'route' => $protocol.'://'.$host.$path,
            'host' => $host
        );

        $request = 'Get me some data!';
        $body = 'Some data';
        $headers = '
HTTP/1.1 200 OK
Cache-Control: private, s-maxage=0
Content-Type: application/json; charset=utf-8
Server: Microsoft-IIS/7.0';
        $response = $headers."\r\n\r\n".$body;
        $status = '200';


        $this->partial->setReturnValue('_build_request', $request);
        $this->partial->expectOnce('_build_request',
            array(
                new IdenticalExpectation($call_options),
                new IdenticalExpectation($host),
                new IdenticalExpectation($path),
                new IdenticalExpectation(true)
            )
        );

        $this->mock_wrapper->setReturnValue('open', true);
        $this->mock_wrapper->expectOnce('open',
            array(
                new IdenticalExpectation($domain_prefix.$host),
                new IdenticalExpectation($port)
            )
        );

        $this->mock_wrapper->expectOnce('write', array(new IdenticalExpectation($request)));

        $this->mock_wrapper->setReturnValue('read', $response);

        $this->partial->setReturnValue('_get_status_code', $status);
        $this->partial->expectOnce('_get_status_code', array(new IdenticalExpectation($headers)));

        $this->assertIdentical(array (
            'code' => $status,
            'response' => $body
        ), $this->partial->make_call($call_options));
    }

    function test_get_type() {
        $this->assertIdentical($this->transport->get_type(), 'Socket');
    }

    function test_get_status_code_200() {
        $headers =
'
HTTP/1.1 200 OK
Cache-Control: private, s-maxage=0
Content-Type: application/json; charset=utf-8
Server: Microsoft-IIS/7.0';

        $this->assertIdentical($this->transport->_get_status_code($headers), '200');
    }

    function test_get_status_code_404() {
        $headers =
'HTTP/1.1 404 Not Found
Cache-Control: private, s-maxage=0
Content-Type: application/json; charset=utf-8
Server: Microsoft-IIS/7.0';

        $this->assertIdentical($this->transport->_get_status_code($headers), '404');
    }

    function test_build_request_no_data_or_gzip() {
        $call_options = array(
            'method' => 'CONJURE',
            'authdetails' => array('api_key' => 'chucknorris'),
            'userAgent' => 'Nozilla/ Firechuck',
            'contentType' => 'application/visa'
        );

        $host = 'api.test.createsend.com';
        $path = '/path/to/resource';

        $expected =
        $call_options['method'].' '.$path." HTTP/1.1\n".
'Host: '.$host."\n".
'Authorization: Basic '.base64_encode($call_options['authdetails']['api_key'].":nopass")."\n".
'User-Agent: '.$call_options['userAgent']."\n".
"Connection: Close\n".
'Content-Type: '.$call_options['contentType']."\n\n\n";
    	     
    	    $this->assertIdentical($this->transport->_build_request($call_options, $host, $path, false), $expected);
    }
    
    function test_build_request_no_data_with_gzip() {
        $call_options = array(
            'method' => 'CONJURE',
            'authdetails' => array('api_key' => 'chucknorris'),
            'userAgent' => 'Nozilla/ Firechuck',
            'contentType' => 'application/visa'
        );
             
        $host = 'api.test.createsend.com';
        $path = '/path/to/resource';
             
        $expected =
        $call_options['method'].' '.$path." HTTP/1.1\n".
'Host: '.$host."\n".
'Authorization: Basic '.base64_encode($call_options['authdetails']['api_key'].":nopass")."\n".
'User-Agent: '.$call_options['userAgent']."\n".
"Connection: Close\n".
'Content-Type: '.$call_options['contentType']."\n".
"Accept-Encoding: gzip\n\n\n";

        $this->assertIdentical($this->transport->_build_request($call_options, $host, $path, true), $expected);
    }

    function test_build_request_with_data_no_gzip() {
        $call_options = array(
            'method' => 'CONJURE',
            'authdetails' => array('api_key' => 'chucknorris'),
            'userAgent' => 'Nozilla/ Firechuck',
            'contentType' => 'application/visa',
            'data' => 'Send this to your bank for a new Credit Card!'
        );

        $host = 'api.test.createsend.com';
        $path = '/path/to/resource';

        $expected =
        $call_options['method'].' '.$path." HTTP/1.1\n".
'Host: '.$host."\n".
'Authorization: Basic '.base64_encode($call_options['authdetails']['api_key'].":nopass")."\n".
'User-Agent: '.$call_options['userAgent']."\n".
"Connection: Close\n".
'Content-Type: '.$call_options['contentType']."\n".
'Content-Length: '.strlen($call_options['data'])."\n\n".
        $call_options['data']."\n\n";

        $this->assertIdentical($this->transport->_build_request($call_options, $host, $path, false), $expected);
    }

    function test_build_request_with_data_and_gzip() {
        $call_options = array(
            'method' => 'CONJURE',
            'authdetails' => array('api_key' => 'chucknorris'),
            'userAgent' => 'Nozilla/ Firechuck',
            'contentType' => 'application/visa',
            'data' => 'Send this to your bank for a new Credit Card!'
        );

        $host = 'api.test.createsend.com';
        $path = '/path/to/resource';

        $expected =
        $call_options['method'].' '.$path." HTTP/1.1\n".
'Host: '.$host."\n".
'Authorization: Basic '.base64_encode($call_options['authdetails']['api_key'].":nopass")."\n".
'User-Agent: '.$call_options['userAgent']."\n".
"Connection: Close\n".
'Content-Type: '.$call_options['contentType']."\n".
"Accept-Encoding: gzip\n".
'Content-Length: '.strlen($call_options['data'])."\n\n".
        $call_options['data']."\n\n";

        $this->assertIdentical($this->transport->_build_request($call_options, $host, $path, true), $expected);
    }
}