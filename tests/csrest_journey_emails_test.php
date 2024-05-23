<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestJourneyEmails extends CS_REST_TestJourneyEmails {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestJourneyEmails extends CS_REST_TestJourneyEmails {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestJourneyEmails extends CS_REST_TestBase {
    var $journey_email_id = 'not a real email id';
    var $journey_emails_base_route;

    function set_up_inner() {
        $this->journey_emails_base_route = $this->base_route.'journeys/email/'.$this->journey_email_id.'/';
        $this->wrapper = new CS_REST_JourneyEmails($this->journey_email_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);

    }

    function testget_journey_recipients() {
        $raw_result = 'some recipients';
        $since = '2021';
        $response_code = 200;
        $deserialised = array('Recipient 1', 'Recipient 2');
        $call_options = $this->get_call_options(
            $this->journey_emails_base_route.'opens.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_journey_opens($since);

        $this->assertIdentical($expected_result, $result);


    }



    function testget_journey_opens() {
        $raw_result = 'some journey opens';
        $since = '2021';
        $response_code = 200;
        $deserialised = array('Journey Open 1', 'Journey Open 2');
        $call_options = $this->get_call_options(
            $this->journey_emails_base_route.'opens.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_journey_opens($since);

        $this->assertIdentical($expected_result, $result);
    }


    function testget_journey_clicks() {
        $raw_result = 'some journey clicks';
        $since = '2021';
        $response_code = 200;
        $deserialised = array('Journey Click 1', 'Journey Click 2');
        $call_options = $this->get_call_options(
            $this->journey_emails_base_route.'clicks.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_journey_clicks($since);

        $this->assertIdentical($expected_result, $result);
    }


    function testget_journey_unsubscribes() {
        $raw_result = 'some journey clicks';
        $since = '2021';
        $response_code = 200;
        $deserialised = array('Journey Unsub 1','Journey Unsub 2');
        $call_options = $this->get_call_options(
            $this->journey_emails_base_route.'unsubscribes.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_journey_unsubscribes($since);

        $this->assertIdentical($expected_result, $result);
    }



    function testget_journey_bounces() {
        $raw_result = 'some journey bounces';
        $since = '2021';
        $response_code = 200;
        $deserialised = array('Journey Bounce 1','Journey Bounce 2');
        $call_options = $this->get_call_options(
            $this->journey_emails_base_route.'bounces.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_journey_bounces($since);

        $this->assertIdentical($expected_result, $result);
    }





   
}