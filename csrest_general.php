<?php

require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access general resources from the create send API.
 * @author tobyb
 *
 */
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
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array<string>(timezones)
     */
    function get_timezones() {
        return $this->get_request($this->_base_route.'timezones.json');
    }

    /**
     * Gets the current date in your accounts timezone
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'SystemDate' => string The current system date in your accounts timezone
     * }
     */
    function get_systemdate() {
        return $this->get_request($this->_base_route.'systemdate.json');
    }

    /**
     * Gets an array of valid countries
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array<string>(countries)
     */
    function get_countries() {
        return $this->get_request($this->_base_route.'countries.json');
    }

    /**
     * Gets your API key
     * @param string $username Your username
     * @param string $password Your password
     * @param string $site_url The url you use to login from
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ApiKey' => string Your api key
     * }
     */
    function get_apikey($username, $password, $site_url) {
        return $this->get_request($this->_base_route.'apikey.json?siteurl='.$site_url,
            array('credentials' => $username.':'.$password));
    }

    /**
     * Gets an array of clients
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'ClientID' => The clients API ID,
     *         'Name' => The clients name
     *     }
     * )
     */
    function get_clients() {
        return $this->get_request($this->_base_route.'clients.json');
    }

    /**
     * Gets your billing details.
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'Credits' => The number of credits belonging to the account
     * }
     */
    function get_billing_details() {
        return $this->get_request($this->_base_route.'billingdetails.json');
    }

    /**
     * Gets an array of administrators
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'EmailAddress' => The administrators email address
     *         'Name' => The administrators name
     *         'Status' => The administrators status
     *     }
     * )
     */
    function get_administrators() {
    	return $this->get_request($this->_base_route.'admins.json');
    }

    /**
     * retrieves the email address of the primary contact for this account
     * @return CS_REST_Wrapper_Result a successful response will be an array in the form:
     * 		array('EmailAddress'=> email address of primary contact)
     */
    function get_primary_contact() {
    	return $this->get_request($this->_base_route.'primarycontact.json');
    }

    /**
     * assigns the primary contact for this account to the administrator with the specified email address
     * @param string $emailAddress the email address of the administrator designated to be the primary contact
     * @return CS_REST_Wrapper_Result a successful response will be an array in the form:
     * 		array('EmailAddress'=> email address of primary contact)
     */
    function set_primary_contact($emailAddress) {
    	return $this->put_request($this->_base_route.'primarycontact.json?email=' . urlencode($emailAddress), '');
    }
}