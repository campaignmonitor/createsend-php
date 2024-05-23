<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestEvents extends CS_REST_TestEvents {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestEvents extends CS_REST_TestEvents {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestEvents extends CS_REST_TestBase {
    var $client_id = 'fakeclientid';
    var $events_base_route;
    var $event_type = "identify";

    function set_up_inner() {
        $this->events_base_route =  $this->base_route.'events/'.$this->client_id.'/'; 
        $this->wrapper = new CS_REST_Events($this->auth, $this->client_id, $this->event_type, $this->protocol,
                                            $this->log_level, $this->api_host, $this->mock_log,
                                            $this->mock_serialiser, $this->mock_transport);
    }
   
    function testtrack() {
        $client_id = 'fakeclientid';
        $raw_result = 'the new event id';
        $email = 'test@email.com';
        $event_name = 'Widget Man!';
        $data = array('ExampleField'=> 'Me');
        $anon_id = 'anonid-0';
        $user_id = 'userid-0';
        $response_code = 202;

        $call_options = $this->get_call_options($this->base_route.'events/'.$this->event_type.'/'.$this->client_id.'/track', 'POST');

        // `Non-identify` event (custom, shopify)
        $event_info = array (
            'ContactID' => array(
                'Email' => 'test@email.com',
            ),
            'EventName' => $event_name,
            'Data' => array(
                'ExampleField'=> 'Me'
            )
        );

        if (strcmp($this->event_type, "identify") === 0) {
            // `Identify` event
            $event_info['ContactID']['AnonymousID'] = $anon_id;
            $event_info['ContactID']['UserID'] = $user_id;
        }

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'event info was serialised to this';
        
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'event info was serialised to this', $event_info, $response_code);

        if (strcmp($this->event_type, "identify") == 0) {
            $result = $this->wrapper->track($email, $event_name, $anon_id, $user_id, $data);
        } else {
            $result = $this->wrapper->track($email, $event_name, NULL, NULL, $data);
        }

        $this->assertIdentical($expected_result, $result);

    }
}