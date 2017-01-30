<?php

use CreateSend\Wrapper\General;
use CreateSend\Log\LogInterface;
use CreateSend\Wrapper\Result;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/lastcraft/simpletest/autorun.php';

@Mock::generate('CreateSend\CS_REST_Log');
@Mock::generate('CreateSend\Serializer\CS_REST_NativeJsonSerialiser');
@Mock::generate('CreateSend\Transport\CS_REST_CurlTransport');

class CS_REST_TestBase extends UnitTestCase {
    public $mock_log;
    public $mock_serialiser;
    public $mock_transport;

    public $wrapper;

    public $serialisation_type = 'mockjson';
    public $transport_type = 'mock_cURL';
    public $auth = null;
    public $protocol = 'hotpotatoes';
    public $api_host = 'api.test.createsend.com';
    public $log_level = LogInterface::LEVEL_NONE;

    public $base_route;

    public function setUp() {
        $this->mock_log = new MockCS_REST_Log();
        $this->mock_serialiser = new MockCS_REST_NativeJsonSerialiser();
        $this->mock_transport = new MockCS_REST_CurlTransport();

        $this->mock_transport->setReturnValue('get_type', $this->transport_type);
        $this->mock_serialiser->setReturnValue('get_type', $this->serialisation_type);

        $this->base_route = $this->protocol.'://'.$this->api_host.'/api/v3.1/';

        $this->set_up_inner();
    }

    public function set_up_inner() {
        $this->wrapper = new General($this->auth, $this->protocol, $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    public function get_call_options($route, $method = 'GET') {
        return array (
            'authdetails' => $this->auth,
            'userAgent' => 'CS_REST_Wrapper v'.CS_REST_WRAPPER_VERSION.
            ' PHPv'.phpversion().' over '.$this->transport_type.' with '.$this->serialisation_type,
            'contentType' => 'application/json; charset=utf-8',
            'deserialise' => true,
            'host' => $this->api_host,
            'protocol' => $this->protocol,
            'route' => $route,
            'method' => $method
        );
    }

    public function setup_transport_and_serialisation($make_call_result, $call_options,
        $deserialise_result, $deserialise_input, $serialise_result = null, $serialise_input = null) {

        $this->mock_transport->setReturnValue('make_call', $make_call_result);
        $this->mock_transport->expectOnce('make_call', array(new IdenticalExpectation($call_options)));

        $this->mock_serialiser->setReturnValue('deserialise', $deserialise_result);
        $this->mock_serialiser->expectOnce('deserialise', array(new IdenticalExpectation($deserialise_input)));

        if(!is_null($serialise_result)) {
            $this->mock_serialiser->setReturnValue('serialise', $serialise_result);
            $this->mock_serialiser->expectOnce('serialise', array(new IdenticalExpectation($serialise_input)));
        }
    }

    public function general_test($wrapper_function, $call_options, $from_transport,
        $from_deserialisation, $response_code = 200) {

        $transport_result = array (
            'code' => $response_code, 
            'response' => $from_transport
        );

        $expected_result = new Result($from_deserialisation, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $from_deserialisation, $from_transport, null, null, $response_code);

        $result = $this->wrapper->$wrapper_function();

        $this->assertIdentical($expected_result, $result);
    }

    public function general_test_with_argument($wrapper_function, $function_argument, $call_options,
        $from_transport, $from_deserialisation,
        $from_serialisation = 'serialised', $response_code = 200) {

        $transport_result = array (
            'code' => $response_code, 
            'response' => $from_transport
        );
        
        $expected_result = new Result($from_deserialisation, $response_code);
         
        if(!is_null($from_serialisation)) {
            $call_options['data'] = $from_serialisation;
        }
        
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $from_deserialisation, $from_transport, $from_serialisation, 
            $function_argument, $response_code);

        $result = $this->wrapper->$wrapper_function($function_argument);
         
        $this->assertIdentical($expected_result, $result);
    }
}

class CS_REST_ApiKeyTestGeneral extends CS_REST_TestGeneral {
    public $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestGeneral extends CS_REST_TestGeneral {
    public $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');

    public function test_static_authorize_url_without_state() {
        $client_id = 8998879;
        $redirect_uri = 'http://example.com/auth';
        $scope = 'ViewReports,CreateCampaigns,SendCampaigns';
        $expected_result = "https://api.createsend.com/oauth?client_id=8998879&redirect_uri=http%3A%2F%2Fexample.com%2Fauth&scope=ViewReports%2CCreateCampaigns%2CSendCampaigns";

        $result = General::authorize_url($client_id, $redirect_uri, $scope);

        $this->assertIdentical($expected_result, $result);
    }

    public function test_static_authorize_url_with_state() {
        $client_id = 8998879;
        $redirect_uri = 'http://example.com/auth';
        $scope = 'ViewReports,CreateCampaigns,SendCampaigns';
        $state = 89879287;
        $expected_result = "https://api.createsend.com/oauth?client_id=8998879&redirect_uri=http%3A%2F%2Fexample.com%2Fauth&scope=ViewReports%2CCreateCampaigns%2CSendCampaigns&state=89879287";

        $result = General::authorize_url($client_id, $redirect_uri, $scope, $state);

        $this->assertIdentical($expected_result, $result);
    }

    public function test_refresh_token_error_when_refresh_token_null() {
        $auth = array('access_token' => 'validaccesstoken', 'refresh_token' => null);
        $this->wrapper = new General($auth, $this->protocol, $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
        $this->expectError('Error refreshing token. There is no refresh token set on this object.');
        list($new_access_token, $new_expires_in, $new_refresh_token) =
            $this->wrapper->refresh_token();
    }

    public function test_refresh_token_error_when_refresh_token_not_set() {
        $auth = array('access_token' => 'validaccesstoken');
        $this->wrapper = new General($auth, $this->protocol, $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
        $this->expectError('Error refreshing token. There is no refresh token set on this object.');
        list($new_access_token, $new_expires_in, $new_refresh_token) =
            $this->wrapper->refresh_token();
    }

    public function test_refresh_token_error_when_no_auth() {
        $this->wrapper = new General(null, $this->protocol, $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
        $this->expectError('Error refreshing token. There is no refresh token set on this object.');
        list($new_access_token, $new_expires_in, $new_refresh_token) =
            $this->wrapper->refresh_token();
    }
}

abstract class CS_REST_TestGeneral extends CS_REST_TestBase {

    public function testget_timezones() {
        $raw_result = 'some timezones';
        $deserialised = array('timezone1', 'timezone2');
        $call_options = $this->get_call_options($this->base_route.'timezones.json');

        $this->general_test('get_timezones', $call_options, $raw_result, $deserialised);
    }

    public function testget_systemdate() {
        $raw_result = 'system date';
        $call_options = $this->get_call_options($this->base_route.'systemdate.json');

        $this->general_test('get_systemdate', $call_options, $raw_result, $raw_result);
    }

    public function testget_countries() {
        $raw_result = 'some countries';
        $deserialised = array('Australia', 'Suid Afrika');
        $call_options = $this->get_call_options($this->base_route.'countries.json');

        $this->general_test('get_countries', $call_options, $raw_result, $deserialised);
    }

    public function testget_clients() {
        $raw_result = 'some clients';
        $deserialised = array('Curran & Hughes', 'Repsol');
        $call_options = $this->get_call_options($this->base_route.'clients.json');

        $this->general_test('get_clients', $call_options, $raw_result, $deserialised);
    }

    public function testget_billing_details() {
        $raw_result = 'billing details';
        $call_options = $this->get_call_options($this->base_route.'billingdetails.json');
        $this->general_test('get_billing_details', $call_options, $raw_result, $raw_result);
    }

    public function testget_primary_contact() {
        $raw_result = 'primary contact result';
        $deserialized = array('EmailAddress' => 'test@foo.bar');
        $call_options = $this->get_call_options($this->base_route.'primarycontact.json', 'GET');

        $this->general_test('get_primary_contact', $call_options,
            $raw_result, $deserialized);
    }

    public function testset_primary_contact() {
        $raw_result = '';
        $response_code = 200;
        $email = 'test@foo.bar';
        $call_options = $this->get_call_options($this->base_route.'primarycontact.json?email=' . urlencode($email), 'PUT');
        $call_options['data'] = '';

        $transport_result = array (
            'code' => $response_code,
            'response' => $raw_result
        );

        $expected_result = new Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, '', '', $response_code);

        $result = $this->wrapper->set_primary_contact($email);

        $this->assertIdentical($expected_result, $result);
    }

    public function testset_external_session_url() {
        $session_options = array(
            'Email' => "exammple@example.com",
            'Chrome' => "None",
            'Url' => "/subscribers",
            'IntegratorID' => "qw989q8wud98qwyd",
            'ClientID' => "9q8uw9d8u9wud" );
        $raw_result = '';
        $response_code = 200;
        $call_options = $this->get_call_options($this->base_route.'externalsession.json', 'PUT');
        $call_options['data'] = 'session options were serialised to this';

        $transport_result = array (
            'code' => $response_code,
            'response' => $raw_result
        );

        $expected_result = new Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'session options were serialised to this',
            $session_options, $response_code);

        $result = $this->wrapper->external_session_url($session_options);

        $this->assertIdentical($expected_result, $result);
    }
}
