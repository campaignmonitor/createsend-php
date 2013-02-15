<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/lastcraft/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestLists extends CS_REST_TestLists {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestLists extends CS_REST_TestLists {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestLists extends CS_REST_TestBase {
    var $list_id = 'not a real list id';
    var $list_base_route;

    function set_up_inner() {
        $this->list_base_route = $this->base_route.'lists/'.$this->list_id.'/';
        $this->wrapper = new CS_REST_Lists($this->list_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testcreate_without_unsubscribe_setting() {
        $raw_result = 'the new list id';
        $client_id = 'not a real client id';
        $response_code = 200;

        $call_options = $this->get_call_options($this->base_route.'lists/'.$client_id.'.json', 'POST');

        $list_info = array (
            'Title' => 'ABC Widgets',
            'UnsubscribeURL' => 'Widget Man!'
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'list info was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'list info was serialised to this', $list_info, $response_code);

        $result = $this->wrapper->create($client_id, $list_info);

        $this->assertIdentical($expected_result, $result);
    }

    function testcreate_with_unsubscribe_setting() {
        $raw_result = 'the new list id';
        $client_id = 'not a real client id';
        $response_code = 200;

        $call_options = $this->get_call_options($this->base_route.'lists/'.$client_id.'.json', 'POST');

        $list_info = array (
            'Title' => 'ABC Widgets',
            'UnsubscribeURL' => 'Widget Man!',
            'UnsubscribeSetting' => CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS
        );

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'list info was serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'list info was serialised to this', $list_info, $response_code);

        $result = $this->wrapper->create($client_id, $list_info);

        $this->assertIdentical($expected_result, $result);
    }

    function testupdate_without_unsubscribe_setting() {
        $raw_result = '';

        $call_options = $this->get_call_options(trim($this->list_base_route, '/').'.json', 'PUT');

        $list_info = array (
            'Title' => 'ABC Widgets',
            'UnsubscribeURL' => 'Widget Man!'
        );

        $this->general_test_with_argument('update', $list_info, $call_options,
            $raw_result, $raw_result, 'list info was serialised to this');
    }

    function testupdate_with_unsubscribe_setting() {
        $raw_result = '';

        $call_options = $this->get_call_options(trim($this->list_base_route, '/').'.json', 'PUT');

        $list_info = array (
            'Title' => 'ABC Widgets',
            'UnsubscribeURL' => 'Widget Man!',
            'UnsubscribeSetting' => CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS
        );

        $this->general_test_with_argument('update', $list_info, $call_options,
            $raw_result, $raw_result, 'list info was serialised to this');
    }

    function testupdate_with_unsubscribe_setting_and_supp_list_options() {
        $raw_result = '';

        $call_options = $this->get_call_options(trim($this->list_base_route, '/').'.json', 'PUT');

        $list_info = array (
            'Title' => 'ABC Widgets',
            'UnsubscribeURL' => 'Widget Man!',
            'UnsubscribeSetting' => CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS,
            'AddUnsubscribesToSuppList' => true,
            'ScrubActiveWithSuppList' => true
        );

        $this->general_test_with_argument('update', $list_info, $call_options,
            $raw_result, $raw_result, 'list info was serialised to this');
    }

    function testcreate_custom_field() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->list_base_route.'customfields.json', 'POST');

        $custom_field = array (
            'Key' => 'ABC Widgets',
            'Options' => array(1,2,3,4)
        );

        $this->general_test_with_argument('create_custom_field', $custom_field, $call_options,
        $raw_result, $raw_result, 'custom field was serialised to this');
    }

    function testupdate_custom_field() {
        $raw_result = '';
        $field_key = 'not a real custom field';
        $response_code = 200;

        $call_options = $this->get_call_options(
            $this->list_base_route.'customfields/'.rawurlencode($field_key).'.json', 'PUT');
          
        $keep_existing = true;

        $serialise_input = array(
            'FieldName' => 'new field name',
            'VisibleInPreferenceCenter' => true
        );

        $transport_result = array (
            'code' => $response_code,
            'response' => $raw_result
        );

        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'options were serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'options were serialised to this', $serialise_input, $response_code);

        $result = $this->wrapper->update_custom_field($field_key, $serialise_input);

        $this->assertIdentical($expected_result, $result);
    }

    function testupdate_field_options() {
        $raw_result = '';
        $field_key = 'not a real custom field';
        $response_code = 200;

        $call_options = $this->get_call_options(
            $this->list_base_route.'customfields/'.rawurlencode($field_key).'/options.json', 'PUT');
            
        $new_options = array ('Option 1', 'Option 2');
        $keep_existing = true;

        $serialise_input = array(
            'KeepExistingOptions' => $keep_existing,
            'Options' => $new_options
        );
        
        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $call_options['data'] = 'options were serialised to this';
        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, 'options were serialised to this', $serialise_input, $response_code);

        $result = $this->wrapper->update_field_options($field_key, $new_options, $keep_existing);

        $this->assertIdentical($expected_result, $result);
    }

    function testdelete() {
        $raw_result = '';

        $call_options = $this->get_call_options(trim($this->list_base_route, '/').'.json', 'DELETE');

        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }

    function testdelete_custom_field() {
        $raw_result = '';
        $response_code = 200;
        $key = 'custom field key';

        $call_options = $this->get_call_options(
            $this->list_base_route.'customfields/'.rawurlencode($key).'.json', 'DELETE');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->delete_custom_field($key);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_custom_fields() {
        $raw_result = 'some custom fields';
        $deserialised = array('Custom Field 1', 'Custom Field 2');
        $call_options = $this->get_call_options($this->list_base_route.'customfields.json');

        $this->general_test('get_custom_fields', $call_options, $raw_result, $deserialised);
    }

    function testget_segments() {
        $raw_result = 'some segments';
        $deserialised = array('Segment 1', 'Segment 2');
        $call_options = $this->get_call_options($this->list_base_route.'segments.json');

        $this->general_test('get_segments', $call_options, $raw_result, $deserialised);
    }

    function testget_active() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->list_base_route.'active.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_active_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_unconfirmed_subscribers() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->list_base_route.'unconfirmed.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_unconfirmed_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_bounced() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->list_base_route.'bounced.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_bounced_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_unsubscribed() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->list_base_route.'unsubscribed.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_unsubscribed_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }
	
	function testget_deleted() {
        $raw_result = 'some subscribers';
        $since = '2020';
        $response_code = 200;
        $deserialised = array('Subscriber 1', 'Subscriber 2');
        $call_options = $this->get_call_options($this->list_base_route.'deleted.json?date='.$since);

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_deleted_subscribers($since);

        $this->assertIdentical($expected_result, $result);
    }

    function testget() {
        $raw_result = 'list details';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options(trim($this->list_base_route, '/').'.json');

        $this->general_test('get', $call_options, $raw_result, $deserialised);
    }

    function testget_stats() {
        $raw_result = 'list stats';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options($this->list_base_route.'stats.json');

        $this->general_test('get_stats', $call_options, $raw_result, $deserialised);
    }

    function testget_webhooks() {
        $raw_result = 'list webhooks';
        $deserialised = array(1,23,4,5,6,7);
        $call_options = $this->get_call_options($this->list_base_route.'webhooks.json');

        $this->general_test('get_webhooks', $call_options, $raw_result, $deserialised);
    }

    function testcreate_webhook() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->list_base_route.'webhooks.json', 'POST');

        $webhook = array (
            'Url' => 'http://webhooks.abcwidgets.com/receive',
            'Events' => array('Subscribe', 'Deactivate')
        );

        $this->general_test_with_argument('create_webhook', $webhook, $call_options,
        $raw_result, $raw_result, 'webhook was serialised to this');
    }

    function testtest_webhook() {
        $raw_result = '';

        $webhook_id = 'not a real id';
        $call_options = $this->get_call_options($this->list_base_route.'webhooks/'.$webhook_id.'/test.json');

        $this->general_test_with_argument('test_webhook', $webhook_id, $call_options,
            $raw_result, $raw_result, NULL);
    }

    function testdelete_webhook() {
        $raw_result = '';
        $response_code = 200;
        $webhook_id = 'not a webhook id';

        $call_options = $this->get_call_options(
            $this->list_base_route.'webhooks/'.$webhook_id.'.json', 'DELETE');

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->delete_webhook($webhook_id);

        $this->assertIdentical($expected_result, $result);
    }

    function testactivate_webhook() {
        $raw_result = '';
        $response_code = 200;
        $webhook_id = 'not a webhook id';

        $call_options = $this->get_call_options(
            $this->list_base_route.'webhooks/'.$webhook_id.'/activate.json', 'PUT');
        $call_options['data'] = '';

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, '', '', $response_code);

        $result = $this->wrapper->activate_webhook($webhook_id);

        $this->assertIdentical($expected_result, $result);
    }

    function testdeeeeactivate_webhook() {
        $raw_result = '';
        $response_code = 200;
        $webhook_id = 'not a webhook id';

        $call_options = $this->get_call_options(
            $this->list_base_route.'webhooks/'.$webhook_id.'/deactivate.json', 'PUT');
        $call_options['data'] = '';

        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        
        $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

        $this->setup_transport_and_serialisation($transport_result, $call_options,
            $raw_result, $raw_result, '', '', $response_code);

        $result = $this->wrapper->deactivate_webhook($webhook_id);

        $this->assertIdentical($expected_result, $result);
    }
}
