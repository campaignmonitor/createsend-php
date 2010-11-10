<?php

require_once 'class/serialisation.php';
require_once 'class/transport.php';
require_once 'class/log.php';

define('CS_REST_WRAPPER_VERSION', '1.0.0');

class CS_REST_Wrapper_Result {
    /**
     * The deserialised result of the API call
     * @var mixed
     */
    var $response;
    
    /**
     * The http status code of the API call
     * @var int
     */
    var $http_status_code;
    
    function CS_REST_Wrapper_Result($response, $code) {
        $this->response = $response;
        $this->http_status_code = $code;
    }

    /**
     * Can be used to check if a call to the api resulted in a successful response.
     * @return boolean False if the call failed. Check the response property for the failure reason.
     * @access public
     */
    function was_successful() {
        return $this->http_status_code >= 200 && $this->http_status_code < 300;
    }
}

/**
 * Base class for the create send PHP wrapper.
 * This class includes functions to access the general data,
 * i.e timezones, clients and getting your API Key from username and password
 * @author tobyb
 *
 */
class CS_REST_Wrapper_Base {
    /**
     * The protocol to use while accessing the api
     * @var string http or https
     * @access private
     */
    var $_protocol;

    /**
     * The base route of the create send api.
     * @var string
     * @access private
     */
    var $_base_route;

    /**
     * The serialiser to use for serialisation and deserialisation
     * of API request and response data
     * @var CS_REST_JsonSerialiser or CS_REST_XmlSerialiser
     * @access private
     */
    var $_serialiser;

    /**
     * The transport to use to send API requests
     * @var CS_REST_CurlTransport or CS_REST_SocketTransport or your own custom transport.
     * @access private
     */
    var $_transport;

    /**
     * The logger to use for debugging of all API requests
     * @var CS_REST_Log
     * @access private
     */
    var $_log;

    /**
     * The default options to use for each API request.
     * These can be overridded by passing in an array as the call_options argument
     * to a single api request.
     * Valid options are:
     *
     * deserialise boolean:
     *     Set this to false if you want to get the raw response.
     *     This can be useful if your passing json directly to javascript.
     *
     * While there are clearly other options there is no need to change them.
     * @var array
     * @access private
     */
    var $_default_call_options;

    var $_call_error_handler;

    /**
     * Constructor.
     * @param $api_key string Your api key (Ignored for get_apikey requests)
     * @param $protocol string The protocol to use for requests (http|https)
     * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
     * @param $host string The host to send API requests to. There is no need to change this
     * @param $log CS_REST_Log The logger to use. Used for dependency injection
     * @param $serialiser The serialiser to use. Used for dependency injection
     * @param $transport The transport to use. Used for dependency injection
     * @access public
     */
    function CS_REST_Wrapper_Base(
    $api_key,
    $protocol = 'https',
    $debug_level = CS_REST_LOG_NONE,
    $host = 'api.createsend.com',
    $log = NULL,
    $serialiser = NULL,
    $transport = NULL) {
        	
        $this->_log = is_null($log) ? new CS_REST_Log($debug_level) : $log;
        	
        $this->_protocol = $protocol;
        $this->_base_route = $protocol.'://'.$host.'/api/v3/';

        $this->_log->log_message('Creating wrapper for '.$this->_base_route, get_class($this), CS_REST_LOG_VERBOSE);

        $this->_transport = is_null($transport) ?
        @CS_REST_TransportFactory::get_available_transport($this->is_secure(), $this->_log) :
        $transport;

        $transport_type = method_exists($this->_transport, 'get_type') ? $this->_transport->get_type() : 'Unknown';
        $this->_log->log_message('Using '.$transport_type.' for transport', get_class($this), CS_REST_LOG_WARNING);

        $this->_serialiser = is_null($serialiser) ?
            @CS_REST_SerialiserFactory::get_available_serialiser($this->_log) : $serialiser;
        	
        $this->_log->log_message('Using '.$this->_serialiser->get_type().' data format', get_class($this), CS_REST_LOG_WARNING);

        $this->_default_call_options = array (
		    'credentials' => $api_key.':nopass',
		    'userAgent' => 'CS_REST_Wrapper v'.CS_REST_WRAPPER_VERSION.
		        ' PHPv'.phpversion().' over '.$transport_type.' with '.$this->_serialiser->get_type(),
		    'contentType' => 'application/json; charset=utf-8', 
			'deserialise' => true,
			'host' => $host
        );
    }
    
    /**
     * @param $route
     * @param $page_number
     * @param $page_size
     * @param $order_field
     * @param $order_direction
     * @access private
     */
    function _add_paging_to_route($route, $page_number, $page_size, $order_field, $order_direction,
        $join_char = '&') {         
        if(!is_null($page_number)) {
            $route .= $join_char.'page='.$page_number;
            $join_char = '&';
        }
        
        if(!is_null($page_size)) {
            $route .= $join_char.'pageSize='.$page_size;
            $join_char = '&';
        }
        
        if(!is_null($order_field)) {
            $route .= $join_char.'orderField='.$order_field;
            $join_char = '&';
        }
        
        if(!is_null($order_direction)) {
            $route .= $join_char.'orderDirection='.$order_direction;
            $join_char = '&';
        }
        
        return $route;        
    }

    /**
     * @return boolean True if the wrapper is using SSL.
     * @access public
     */
    function is_secure() {
        return $this->_protocol === 'https';
    }

    /**
     * Internal method to make a general API request based on the provided options
     * @param $call_options
     * @access private
     */
    function _call($call_options) {
        $call_options = array_merge($this->_default_call_options, $call_options);
        $this->_log->log_message('Making '.$call_options['method'].' call to: '.$call_options['route'], get_class($this), CS_REST_LOG_WARNING);
        	
        $call_result = $this->_transport->make_call($call_options);

        $this->_log->log_message('Call result: <pre>'.var_export($call_result, true).'</pre>',
        get_class($this), CS_REST_LOG_VERBOSE);

        if($call_options['deserialise']) {
            $call_result['response'] = $this->_serialiser->deserialise($call_result['response']);
        }
         
        return new CS_REST_Wrapper_Result($call_result['response'], $call_result['code']);
    }

    /**
     * Gets an array of valid timezones
     * @param $call_options
     * @access public
     * @return array A successful call will be of the form array(
     *     'code' => int HTTP Response Code (200)
     *     'response' => array<string> The timezones
     * )
     */
    function get_timezones($call_options = array()) {
        $call_options['route'] = $this->_base_route.'timezones.json';
        $call_options['method'] = CS_REST_GET;
        	
        return $this->_call($call_options);
    }

    /**
     * Gets the current date in your accounts timezone
     * @param $call_options
     * @access public
     * @return array A successful call will be of the form array(
     *     'code' => int HTTP Response Code (200)
     *     'response' => string The current system date in your accounts timezone
     * )
     */
    function get_systemdate($call_options = array()) {
        $call_options['route'] = $this->_base_route.'systemdate.json';
        $call_options['method'] = CS_REST_GET;
        	
        return $this->_call($call_options);
    }

    /**
     * Gets an array of valid countries
     * @param $call_options
     * @access public
     * @return array A successful call will be of the form array(
     *     'code' => int HTTP Response Code (200)
     *     'response' => array<string> The countries
     * )
     */
    function get_countries($call_options = array()) {
        $call_options['route'] = $this->_base_route.'countries.json';
        $call_options['method'] = CS_REST_GET;
        	
        return $this->_call($call_options);
    }

    /**
     * Gets your API key
     * @param string $username Your username
     * @param string $password Your password
     * @param string $site_url The url you use to login from
     * @param $call_options
     * @access public
     * @return array A successful call will be of the form array(
     *     'code' => int HTTP Response Code (200)
     *     'response' => string Your api key
     * )
     */
    function get_apikey($username, $password, $site_url, $call_options = array()) {
        $call_options['route'] = $this->_base_route.'apikey.json?siteurl='.$site_url;
        $call_options['method'] = CS_REST_GET;

        $call_options['credentials'] = $username.':'.$password;

        return $this->_call($call_options);
    }

    /**
     * Gets an array of valid countries
     * @param $call_options
     * @access public
     * @return array A successful call will be of the form array(
     *     'code' => int HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'ClientID' => The clients API ID,
     *             'Name' => The clients name
     *         )
     *     )
     * )
     */
    function get_clients($call_options = array()) {
        $call_options['route'] = $this->_base_route.'clients.json';
        $call_options['method'] = CS_REST_GET;
        	
        return $this->_call($call_options);
    }
}