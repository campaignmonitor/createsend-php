<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestJourneys extends CS_REST_TestJourneys {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestJourneys extends CS_REST_TestJourneys {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestJourneys extends CS_REST_TestBase {
    var $journey_id = 'not a real journey id';
    var $journey_base_route;

    function set_up_inner() {
        $this->journey_base_route = $this->base_route.'journeys/'.$this->journey_id.'/';
        $this->wrapper = new CS_REST_Journeys($this->journey_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);

    }

    function testget_journey_summary() {

        $raw_result = 'journey details';
        $deserialised = array(1,23,4,5);
        $call_options = $this->get_call_options(trim($this->journey_base_route, '/').'.json');
        $this->general_test('get_journey_summary', $call_options, $raw_result, $deserialised);
    }
}