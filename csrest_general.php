<?php

require_once 'class/base_classes.php';
class CS_REST_General extends CS_REST_Wrapper_Base {

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
        $this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);        
    }

    /**
     * Gets an array of valid timezones
     * @param $call_options
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array<string>(timezones)
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
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'SystemDate' => string The current system date in your accounts timezone
     * }
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
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array<string>(countries)
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
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ApiKey' => string Your api key
     * }
     */
    function get_apikey($username, $password, $site_url, $call_options = array()) {
        $call_options['route'] = $this->_base_route.'apikey.json?siteurl='.$site_url;
        $call_options['method'] = CS_REST_GET;

        $call_options['credentials'] = $username.':'.$password;

        return $this->_call($call_options);
    }

    /**
     * Gets an array of clients
     * @param $call_options
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'ClientID' => The clients API ID,
     *         'Name' => The clients name
     *     }
     * )
     */
    function get_clients($call_options = array()) {
        $call_options['route'] = $this->_base_route.'clients.json';
        $call_options['method'] = CS_REST_GET;
        	
        return $this->_call($call_options);
    }
}