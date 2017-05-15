<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestCampaigns extends CS_REST_TestCampaigns {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestCampaigns extends CS_REST_TestCampaigns {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestCampaigns extends CS_REST_TestBase {
    var $campaign_id = 'not a real campaign id';
    var $campaign_base_route;

    function set_up_inner() {
        $this->campaign_base_route = $this->base_route.'campaigns/'.$this->campaign_id.'/';
        $this->wrapper = new CS_REST_Campaigns($this->campaign_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testcreate() {
        $raw_result = 'the new campaign id';
        $client_id = 'not a real client id';
        $response_code = 200;

        $call_options = $this->get_call_options(
            $this->base_route.'campaigns/'.$client_id.'.json', 'POST');

        $campaign_data = array (
            'Name' => 'ABC Widgets',
            'Subject' => 'Widget Man!',
            'ListIDs' => array(1,2,3),
            'SegmentIDs' => array(4,5,6)
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);
        $call_options['data'] = 'campaign data was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'campaign data was serialised to this', 
            $campaign_data, $response_code);

        $result = $this->wrapper->create($client_id, $campaign_data);

        $this->assertIdentical($expected_result, $result);
    }

    function testcreate_from_template() {
        $raw_result = 'the new campaign id';
        $client_id = 'not a real client id';
        $response_code = 200;

        $call_options = $this->get_call_options(
            $this->base_route.'campaigns/'.$client_id.'/fromtemplate.json', 'POST');

        $template_content = array(
          'Singlelines' => array(
            array(
              'Content' => 'This is a heading',
              'Href' => 'http://example.com/'
            )
          ),
          'Multilines' => array(
            array(
              'Content' => '<p>This is example</p><p>multiline <a href=\"http://example.com\">content</a>...</p>'
            )
          ),
          'Images' => array(
            array(
              'Content' => 'http://example.com/image.png',
              'Alt' => 'This is alt text for an image',
              'Href' => 'http://example.com/'
            )
          ),
          'Repeaters' => array(
            array(
              'Items' => array(
                array(
                  'Layout' => 'My layout',
                  'Singlelines' => array(
                    array(
                      'Content' => 'This is a repeater heading',
                      'Href' => 'http://example.com/'
                    )
                  ),
                  'Multilines' => array(
                    array(
                      'Content' => '<p>This is example</p><p>multiline <a href=\"http://example.com\">content</a>...</p>'
                    )
                  ),
                  'Images' => array(
                    array(
                      'Content' => 'http://example.com/image.png',
                      'Alt' => 'This is alt text for a repeater image',
                      'Href' => 'http://example.com/'
                    )
                  )
                )
              )
            )
          )
        );

        $campaign_data = array (
            'Name' => 'ABC Widgets',
            'Subject' => 'Widget Man!',
            'ListIDs' => array(1,2,3),
            'SegmentIDs' => array(4,5,6),
            'TemplateID' => 'dj9qw8jdq98wjdqd2112e',
            'TemplateContent' => $template_content
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);
        $call_options['data'] = 'campaign data was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'campaign data was serialised to this', 
            $campaign_data, $response_code);

        $result = $this->wrapper->create_from_template($client_id, $campaign_data);

        $this->assertIdentical($expected_result, $result);
    }

    function testsend_preview() {
        $raw_result = '';
        $response_code = 200;

        $call_options = $this->get_call_options(
            $this->campaign_base_route.'sendpreview.json', 'POST');

        $recipients = array (
            'test1@test.com',
            'test1@test.com'
        );
        $personalise = 'Random';
        
        $preview_info = array(
            'PreviewRecipients' => $recipients,
            'Personalize' => $personalise
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);
        $call_options['data'] = 'campaign data was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'campaign data was serialised to this', 
            $preview_info);

        $result = $this->wrapper->send_preview($recipients, $personalise);

        $this->assertIdentical($expected_result, $result);
    }

    function testsend() {
        $raw_result = '';

        $call_options = $this->get_call_options(
            $this->campaign_base_route.'send.json', 'POST');

        $schedule = array (
            'CompanyName' => 'ABC Widgets',
            'ContactName' => 'Widget Man!',
            'EmailAddress' => 'widgets@abc.net.au'
        );

        $this->general_test_with_argument('send', $schedule, $call_options,
            $raw_result, $raw_result, 'scheduling was serialised to this');
    }

    function testdelete() {
        $raw_result = '';

        $call_options = $this->get_call_options(
            trim($this->campaign_base_route, '/').'.json', 'DELETE');

        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }

    function testget_recipients() {
        $raw_result = 'some recipients';
        $deserialised = array('Recipient 1', 'Recipient 2');
        $call_options = $this->get_call_options($this->campaign_base_route.'recipients.json');

        $this->general_test('get_recipients', $call_options, $raw_result, $deserialised);
    }

    function testget_bounces() {
        $raw_result = 'some bounces';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Bounce 1', 'Bounce 2');
        $call_options = $this->get_call_options(
          $this->campaign_base_route.'bounces.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_bounces($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_lists_and_segments() {
        $raw_result = 'some lists';
        $deserialised = array('List 1', 'List 2');
        $call_options = $this->get_call_options(
            $this->campaign_base_route.'listsandsegments.json');

        $this->general_test('get_lists_and_segments', $call_options, $raw_result, $deserialised);
    }

    function testget_summary() {
        $raw_result = 'campaign summary';
        $deserialised = array(1,2,3,4,5);
        $call_options = $this->get_call_options(
            $this->campaign_base_route.'summary.json');

        $this->general_test('get_summary', $call_options, $raw_result, $deserialised);
    }

    function testget_email_client_usage() {
        $raw_result = 'campaign email client usage';
        $deserialised = array(1,2,3,4,5);
        $call_options = $this->get_call_options(
            $this->campaign_base_route.'emailclientusage.json');

        $this->general_test('get_email_client_usage', $call_options, $raw_result, $deserialised);
    }

    function testget_opens() {
        $raw_result = 'some opens';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Open 1', 'Open 2');
        $call_options = $this->get_call_options(
            $this->campaign_base_route.'opens.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_opens($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_clicks() {
        $raw_result = 'some clicks';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Click 1', 'Click 2');
        $call_options = $this->get_call_options(
            $this->campaign_base_route.'clicks.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_clicks($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_unsubscribes() {
        $raw_result = 'some unsubscribed';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Unsubscribe 1', 'Unsubscribe 2');
        $call_options = $this->get_call_options(
            $this->campaign_base_route.'unsubscribes.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_unsubscribes($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_spam() {
        $raw_result = 'some spam';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Spam 1', 'Spam 2');
        $call_options = $this->get_call_options(
            $this->campaign_base_route.'spam.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_spam($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testunschedule() {
        $raw_result = '';
        $response_code = 200;

        $call_options = $this->get_call_options($this->campaign_base_route.'unschedule.json', 'POST');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
        $raw_result, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->unschedule();

        $this->assertIdentical($expected_result, $result);
    }
}