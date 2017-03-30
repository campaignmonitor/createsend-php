<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/simpletest/simpletest/autorun.php';

@Mock::generate('CS_REST_Log');
@Mock::generate('CS_REST_NativeJsonSerialiser');
@Mock::generate('CS_REST_CurlTransport');

class CS_REST_ApiKeyTestClients extends CS_REST_TestClients {
    var $auth = array('api_key' => 'not a real api key');
}

class CS_REST_OAuthTestClients extends CS_REST_TestClients {
    var $auth = array(
        'access_token' => '7y872y3872i3eh',
        'refresh_token' => 'kjw8qjd9ow8jo');
}

abstract class CS_REST_TestClients extends CS_REST_TestBase {
    var $client_id = 'not a real client id';
    var $client_base_route;

    function set_up_inner() {
        $this->client_base_route = $this->base_route.'clients/'.$this->client_id.'/';
        $this->wrapper = new CS_REST_Clients($this->client_id, $this->auth, $this->protocol, $this->log_level,
        $this->api_host, $this->mock_log, $this->mock_serialiser, $this->mock_transport);
    }

    function testget_campaigns() {
        $raw_result = 'some campaigns';
        $deserialised = array('Campaign 1', 'Campaign 2');
        $call_options = $this->get_call_options($this->client_base_route.'campaigns.json');

        $this->general_test('get_campaigns', $call_options, $raw_result, $deserialised);
    }

    function testget_scheduled() {
        $raw_result = 'some scheduled campaigns';
        $deserialised = array('Campaign 1', 'Campaign 2');
        $call_options = $this->get_call_options($this->client_base_route.'scheduled.json');

        $this->general_test('get_scheduled', $call_options, $raw_result, $deserialised);
    }

    function testget_drafts() {
        $raw_result = 'some drafts';
        $deserialised = array('Campaign 1', 'Campaign 2');
        $call_options = $this->get_call_options($this->client_base_route.'drafts.json');

        $this->general_test('get_drafts', $call_options, $raw_result, $deserialised);
    }

    function testget_lists() {
        $raw_result = 'some lists';
        $deserialised = array('List 1', 'List 2');
        $call_options = $this->get_call_options($this->client_base_route.'lists.json');

        $this->general_test('get_lists', $call_options, $raw_result, $deserialised);
    }

    function testget_lists_for_email() {
        $email = 'valid@example.com';
        $raw_result = 'lists to which email is subscribed';
        $deserialised = array('List 1', 'List 2');
        $response_code = 200;
        $call_options = $this->get_call_options($this->client_base_route .
          'listsforemail.json?email='.urlencode($email), 'GET');
        $transport_result = array (
            'code' => $response_code, 
            'response' => $raw_result
        );
        $expected_result = new CS_REST_Wrapper_Result($deserialised, $response_code);
        $this->setup_transport_and_serialisation($transport_result, $call_options,
          $deserialised, $raw_result, NULL, NULL, $response_code);

        $result = $this->wrapper->get_lists_for_email($email);

        $this->assertIdentical($expected_result, $result);
    }

    function testget_segments() {
        $raw_result = 'some segments';
        $deserialised = array('Segment 1', 'Segment 2');
        $call_options = $this->get_call_options($this->client_base_route.'segments.json');

        $this->general_test('get_segments', $call_options, $raw_result, $deserialised);
    }

    function testget_suppressionlist() {
        $raw_result = 'some emails';
        $deserialised = array('dont@email.me', 'go@away.com');
        $call_options = $this->get_call_options($this->client_base_route.'suppressionlist.json');

        $this->general_test('get_suppressionlist', $call_options, $raw_result, $deserialised);
    }

    function testsuppress() {
      $raw_result = '';
      $response_code = 200;
      $call_options = $this->get_call_options(
          $this->client_base_route.'suppress.json', 'POST');
      $emails = array (
          'test1@test.com',
          'test1@test.com'
      );
      $suppression_info = array(
          'EmailAddresses' => $emails
      );
      $transport_result = array (
          'code' => $response_code, 
          'response' => $raw_result
      );

      $expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);
      $call_options['data'] = 'suppression data was serialised to this';
      $this->setup_transport_and_serialisation($transport_result, $call_options,
          $raw_result, $raw_result, 'suppression data was serialised to this', 
          $suppression_info);

      $result = $this->wrapper->suppress($emails);

      $this->assertIdentical($expected_result, $result);
    }

    function testunsuppress() {
    	$raw_result = '';
    	$response_code = 200;
    	$email = 'example@example.com';
    	$call_options = $this->get_call_options($this->client_base_route.'unsuppress.json?email=' . urlencode($email), 'PUT');
    	$call_options['data'] = '';

    	$transport_result = array (
    			'code' => $response_code,
    			'response' => $raw_result
    	);

    	$expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);

    	$this->setup_transport_and_serialisation($transport_result, $call_options,
    			$raw_result, $raw_result, '', '', $response_code);

    	$result = $this->wrapper->unsuppress($email);

    	$this->assertIdentical($expected_result, $result);
    }

    function testget_templates() {
        $raw_result = 'some templates';
        $deserialised = array('Template 1', 'Template 2');
        $call_options = $this->get_call_options($this->client_base_route.'templates.json');

        $this->general_test('get_templates', $call_options, $raw_result, $deserialised);
    }

    function testget() {
        $raw_result = 'client data';
        $deserialised = array('CompanyName' => 'Widget Land');
        $call_options = $this->get_call_options(trim($this->client_base_route, '/').'.json');

        $this->general_test('get', $call_options, $raw_result, $deserialised);
    }

    function testdelete() {
        $raw_result = '';

        $call_options = $this->get_call_options(
            trim($this->client_base_route, '/').'.json', 'DELETE');
        	
        $this->general_test('delete', $call_options, $raw_result, $raw_result);
    }

    function testcreate() {
        $raw_result = 'the new client id';

        $call_options = $this->get_call_options($this->base_route.'clients.json', 'POST');

        $client_data = array (
	        'CompanyName' => 'ABC Widgets',
	      );

        $this->general_test_with_argument('create', $client_data, $call_options,
          $raw_result, $raw_result, 'client data was serialised to this');
    }

    function testset_basics() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->client_base_route.'setbasics.json', 'PUT');
         
        $client_data = array (
            'CompanyName' => 'ABC Widgets',
            'ContactName' => 'Widget Man!',
            'EmailAddress' => 'widgets@abc.net.au'
        );

        $this->general_test_with_argument('set_basics', $client_data, $call_options,
            $raw_result, $raw_result, 'client data was serialised to this');
    }

    function testset_payg() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->client_base_route.'setpaygbilling.json', 'PUT');
         
        $client_data = array (
            'Current' => 'PZD',
            'ClientPays' => true,
            'MarkupPercentage' => 1000
        );

        $this->general_test_with_argument('set_payg_billing', $client_data, $call_options,
            $raw_result, $raw_result, 'client data was serialised to this');
    }

    function testset_monthly() {
        $raw_result = '';

        $call_options = $this->get_call_options($this->client_base_route.'setmonthlybilling.json', 'PUT');
         
        $client_data = array (
            'Current' => 'PZD',
            'ClientPays' => true,
            'MarkupPercentage' => 1000
        );

        $this->general_test_with_argument('set_monthly_billing', $client_data, $call_options,
            $raw_result, $raw_result, 'client data was serialised to this');
    }

    function testtransfer_credits() {
      $raw_result = 'the result';

      $call_options = $this->get_call_options($this->client_base_route.'credits.json', 'POST');

      $transfer_data = array(
          'Credits' => 200,
          'CanUseMyCreditsWhenTheyRunOut' => false
      );

      $this->general_test_with_argument('transfer_credits', $transfer_data, $call_options,
        $raw_result, $raw_result, 'transfer data was serialised to this');
    }

    function testget_primary_contact() {
    	$raw_result = 'primary contact result';
    	$deserialized = array('EmailAddress' => 'test@foo.bar');
    	$call_options = $this->get_call_options($this->client_base_route.'primarycontact.json', 'GET');
    
    	$this->general_test('get_primary_contact', $call_options,
    			$raw_result, $deserialized);
    }
    
    function testset_primary_contact() {
    	$raw_result = '';
    	$response_code = 200;
    	$email = 'test@foo.bar';
    	$call_options = $this->get_call_options($this->client_base_route.'primarycontact.json?email=' . urlencode($email), 'PUT'); 	
    	$call_options['data'] = '';
    	
    	$transport_result = array (
    			'code' => $response_code,
    			'response' => $raw_result
    	);
    	
    	$expected_result = new CS_REST_Wrapper_Result($raw_result, $response_code);
    	
    	$this->setup_transport_and_serialisation($transport_result, $call_options,
    			$raw_result, $raw_result, '', '', $response_code);
    	
    	$result = $this->wrapper->set_primary_contact($email);
    	
    	$this->assertIdentical($expected_result, $result);       
    }
}