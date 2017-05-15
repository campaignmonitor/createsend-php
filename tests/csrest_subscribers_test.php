<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestSubscribers extends CS_REST_TestSubscribers {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestSubscribers extends CS_REST_TestSubscribers {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestSubscribers extends CS_REST_TestBase {
    var $list_id = 'not a real list id';
    var $list_base_route;

    function set_up_inner() {
        $this->list_base_route = $this->base_route.'subscribers/'.$this->list_id;
        $this->wrapper = new CS_REST_Subscribers($this->list_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testadd() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->list_base_route.'.json', 'POST');

        $subscriber = array (
            'Email' => 'test@test.com',
            'Name' => 'Widget Man!',
            'CustomFields' => array(array(1,2), array(3,4))
        );

        $this->general_test_with_argument('add', $subscriber, $call_options,
			$raw_result, $raw_result, 'subscriber was serialised to this');
    }

    function testupdate() {
        $raw_result = '';
        $email = 'test@test.com';
		$serialised_subscriber = 'subscriber data';
		
        $call_options = $this->get_call_options(
            $this->list_base_route.'.json?email='.urlencode($email), 'PUT');

        $subscriber = array (
            'Email' => 'test2@test.com',
            'Name' => 'Widget Man!',
            'CustomFields' => array(array(1,2), array(3,4))
        );

        $transport_result = array (
            'code' => 200, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, 200);
        $call_options['data'] = $serialised_subscriber;
        
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, $serialised_subscriber, 
            $subscriber, 200);

        $result = $this->wrapper->update($email, $subscriber);
         
        $this->assertIdentical($expected_result, $result);
    }

    function testimport() {
        $raw_result = 'the import result';
        $response_code = 200;
        $resubscribe = true;
		$queueSubscriptionBasedAutoResponders = true;
        $restartSubscriptionBasedAutoResponders = false;

        $call_options = $this->get_call_options($this->list_base_route.'/import.json', 'POST');

        $subscribers = array(
            array (
    	            'Email' => 'test@test.com',
    	            'Name' => 'Widget Man!',
    	            'CustomFields' => array(array(1,2), array(3,4))
            ),
            array (
                    'Email' => 'test@test.com',
                    'Name' => 'Widget Man!',
                    'CustomFields' => array(array(1,2), array(3,4))
            )
        );

        $data = array(
                'Resubscribe' => $resubscribe,
                'QueueSubscriptionBasedAutoResponders' => $queueSubscriptionBasedAutoResponders,
                'Subscribers' => $subscribers,
                'RestartSubscriptionBasedAutoresponders' => $restartSubscriptionBasedAutoResponders
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'subscribers were serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'subscribers were serialised to this', 
            $data, $response_code);

        $result = $this->wrapper->import($subscribers, $resubscribe, $queueSubscriptionBasedAutoResponders);

        $this->assertIdentical($expected_result, $result);
    }

    function testget() {
        $raw_result = 'subscriber details';
        $deserialised = array(1,2,34,5);
        $response_code = 200;
        $email = 'test@test.com';

        $call_options = $this->get_call_options(
            $this->list_base_route.'.json?email='.urlencode($email), 'GET');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get($email);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_history() {
        $raw_result = 'subscriber history';
        $deserialised = array(1,2,34,5);
        $response_code = 200;
        $email = 'test@test.com';

        $call_options = $this->get_call_options(
            $this->list_base_route.'/history.json?email='.urlencode($email), 'GET');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_history($email);

        $this->assertIdentical($expected_result, $result);
    }

    function testunsubscribe() {
        $raw_result = '';
        $response_code = 200;
        $email = 'test@test.com';

        $call_options = $this->get_call_options($this->list_base_route.'/unsubscribe.json', 'POST');
         
        $subscriber = array('EmailAddress' => $email);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'subscriber was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $raw_result, $raw_result,
            'subscriber was serialised to this', $subscriber, $response_code);

        $result = $this->wrapper->unsubscribe($email);

        $this->assertIdentical($expected_result, $result);
    }

    function testdelete() {
        $raw_result = '';
        $response_code = 200;
        $email = 'test@test.com';

        $call_options = $this->get_call_options($this->list_base_route.'.json?email='.urlencode($email), 'DELETE');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $raw_result, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->delete($email);

        $this->assertIdentical($expected_result, $result);
    }
}