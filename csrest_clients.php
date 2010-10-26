<?php
require_once 'csrest.php';

define('CS_REST_CLIENT_ACCESS_NONE', 0x0);
define('CS_REST_CLIENT_ACCESS_REPORTS', 0x1);
define('CS_REST_CLIENT_ACCESS_SUBSCRIBERS', 0x2);
define('CS_REST_CLIENT_ACCESS_CREATESEND', 0x4);
define('CS_REST_CLIENT_ACCESS_DESIGNSPAMTEST', 0x8);
define('CS_REST_CLIENT_ACCESS_IMPORTSUBSCRIBERS', 0x10);
define('CS_REST_CLIENT_ACCESS_IMPORTURL', 0x20);

/**
 * Class to access a clients resources from the create send API.
 * This class includes functions to create and edit clients,
 * along with accessing lists of client specific resources e.g campaigns
 * @author tobyb
 *
 */
class CS_REST_Clients extends CS_REST_Wrapper_Base {

    /**
     * The base route of the clients resource.
     * @var string
     * @access private
     */
    var $_clients_base_route;

    /**
     * Constructor.
     * @param $client_id string The client id to access (Ignored for create requests)
     * @param $api_key string Your api key (Ignored for get_apikey requests)
     * @param $protocol string The protocol to use for requests (http|https)
     * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
     * @param $host string The host to send API requests to. There is no need to change this
     * @param $log CS_REST_Log The logger to use. Used for dependency injection
     * @param $serialiser The serialiser to use. Used for dependency injection
     * @param $transport The transport to use. Used for dependency injection
     * @access public
     */
    function CS_REST_Clients(
    $client_id,
    $api_key,
    $protocol = 'https',
    $debug_level = CS_REST_LOG_NONE,
    $host = 'api.createsend.com',
    $log = NULL,
    $serialiser = NULL,
    $transport = NULL) {
        	
        $this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
        $this->set_client_id($client_id);
    }

    /**
     * Change the client id used for calls after construction
     * @param $client_id
     * @access public
     */
    function set_client_id($client_id) {
        $this->_clients_base_route = $this->_base_route.'clients/'.$client_id.'/';
    }

    /**
     * Gets a list of sent campaigns for the current client
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'WebVersionURL' => The webversion url of the campaign
     *             'CampaignID' => The id of the campaign
     *             'Subject' => The campaign subject
     *             'Name' => The name of the campaign
     *             'SentDate' => The sent data of the campaign
     *             'TotalRecipient' => The number of recipients of the campaign
     *         )
     *     )
     * )
     */
    function get_campaigns($call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'campaigns.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets a list of sent campaigns for the current client
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'CampaignID' => The id of the campaign
     *             'Name' => The name of the campaign
     *             'Subject' => The subject of the campaign
     *             'DateCreated' => The date the campaign was created
     *         )
     *     )
     * )
     */
    function get_drafts($call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'drafts.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets all subscriber lists the current client has created
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'ListID' => The id of the list
     *             'Name' => The name of the list
     *         )
     *     )
     * )
     */
    function get_lists($call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'lists.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets all list segments the current client has created
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'ListID' => The id of the list on which the segment was created
     *             'Name' => The name of the segment
     *         )
     *     )
     * )
     */
    function get_segments($call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'segments.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets all email addresses on the current clients suppression list
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'EmailAddress' => The suppressed email address
     *             'Date' => The date the email was suppressed
     *             'State' => The state of the suppressed email
     *         )
     *     )
     * )
     */
    function get_suppressionlist($call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'suppressionlist.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets all templates the current client has access to
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'TemplateID' => The id of the template
     *             'Name' => The name of the template
     *             'PreviewURL' => The url to preview the template from
     *             'ScreenshotURL' => The url of the template screenshot
     *         )
     *     )
     * )
     */
    function get_templates($call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'templates.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets all templates the current client has access to
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'BasicDetails' => array(
     *             'ClientID' => The id of the client
     *             'CompanyName' => The company name of the client
     *             'ContactName' => The contact name of the client
     *             'EmailAddress' => The clients contact email address
     *             'Country' => The clients country
     *             'TimeZone' => The clients timezone
     *         )
     *         'AccessDetails' => array(
     *             'AccessLevel' => The current access level of the client.
     *                 This will be some bitwise combination of
     *
     *                 CS_REST_CLIENT_ACCESS_REPORTS
     *                 CS_REST_CLIENT_ACCESS_SUBSCRIBERS
     *                 CS_REST_CLIENT_ACCESS_CREATESEND
     *                 CS_REST_CLIENT_ACCESS_DESIGNSPAMTEST
     *                 CS_REST_CLIENT_ACCESS_IMPORTSUBSCRIBERS
     *                 CS_REST_CLIENT_ACCESS_IMPORTURL
     *
     *                 or
     *                 CS_REST_CLIENT_ACCESS_NONE
     *             'Username' => The clients current username
     *         )
     *         'BillingDetails' =>
     *         If on monthly billing
     *         array(
     *             'CurrentTier' => The current monthly tier the client sits in
     *             'CurrentMonthlyRate' => The current pricing rate the client pays per month
     *             'MarkupPercentage' => The percentage markup applied to the base rates
     *             'Currency' => The currency paid in
     *             'ClientPays' => Whether the client pays for themselves
     *         )
     *         If paying per campaign
     *         array(
     *             'CanPurchaseCredits' => Whether the client can purchase credits
     *             'BaseDeliveryFee' => The base fee payable per campaign
     *             'BaseRatePerRecipient' => The base fee payable per campaign recipient
     *             'BaseDesignSpamTestRate' => The base fee payable per design and spam test
     *             'MarkupOnDelivery' => The markup applied per campaign
     *             'MarkupPerRecipient' => The markup applied per campaign recipient
     *             'MarkupOnDesignSpamTest' => The markup applied per design and spam test
     *             'Currency' => The currency fees are paid in
     *             'ClientPays' => Whether client client pays for themselves
     *         )
     *     )
     * )
     */
    function get($call_options = array()) {
        $call_options['route'] = trim($this->_clients_base_route, '/').'.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Deletes an existing client from the system
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
    function delete($call_options = array()) {
        $call_options['route'] = trim($this->_clients_base_route, '/').'.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_DELETE;

        return $this->_call($call_options);
    }

    /**
     * Creates a new client based on the provided information
     * @param array $client Basic information of the new client.
     *     This should be an array of the form
     *         array(
     *             'CompanyName' => The company name of the client
     *             'ContactName' => The contact name of the client
     *             'EmailAddress' => The clients contact email address
     *             'Country' => The clients country
     *             'TimeZone' => The clients timezone
     *         )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (201)
     *     'response' => string The ID of the newly created client
     * )
     */
    function create($client, $call_options = array()) {
        $client = $this->_serialiser->format_item('Client', $client);

        $call_options['route'] = $this->_base_route.'clients.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_POST;
        $call_options['data'] = $this->_serialiser->serialise($client);

        return $this->_call($call_options);
    }

    /**
     * Updates the basic information for a client
     * @param array $client_basics Basic information of the client.
     *     This should be an array of the form
     *         array(
     *             'CompanyName' => The company name of the client
     *             'ContactName' => The contact name of the client
     *             'EmailAddress' => The clients contact email address
     *             'Country' => The clients country
     *             'TimeZone' => The clients timezone
     *         )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
    function set_basics($client_basics, $call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'setbasics.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_PUT;
        $call_options['data'] = $this->_serialiser->serialise($client_basics);

        return $this->_call($call_options);
    }

    /**
     * Updates the access details of the current client
     * @param array $client_access Access details of the client.
     *     This should be an array of the form
     *         array(
     *             'AccessLevel' => The current access level of the client.
     *                 This will be some bitwise combination of
     *
     *                 CS_REST_CLIENT_ACCESS_REPORTS
     *                 CS_REST_CLIENT_ACCESS_SUBSCRIBERS
     *                 CS_REST_CLIENT_ACCESS_CREATESEND
     *                 CS_REST_CLIENT_ACCESS_DESIGNSPAMTEST
     *                 CS_REST_CLIENT_ACCESS_IMPORTSUBSCRIBERS
     *                 CS_REST_CLIENT_ACCESS_IMPORTURL
     *
     *                 or
     *                 CS_REST_CLIENT_ACCESS_NONE
     *             'Username' => The clients current username
     *             'Password' => The new password for the given client
     *         )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
    function set_access($client_access, $call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'setaccess.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_PUT;
        $call_options['data'] = $this->_serialiser->serialise($client_access);

        return $this->_call($call_options);
    }

    /**
     * Updates the billing details of the current client, setting the client to the payg billing model
     * For clients not set to pay themselves then all fields below ClientPays are ignored
     * All Markup fields are optional
     * @param array $client_billing Payg billing details of the client.
     *     This should be an array of the form
     *         array(
     *             'Currency' => The currency fees are paid in
     *             'ClientPays' => Whether client client pays for themselves
     *             'MarkupPercentage' => Can be used to set the percentage markup for all unset fees
     *             'CanPurchaseCredits' => Whether the client can purchase credits
     *             'MarkupOnDelivery' => The markup applied per campaign
     *             'MarkupPerRecipient' => The markup applied per campaign recipient
     *             'MarkupOnDesignSpamTest' => The markup applied per design and spam test
     *         )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
    function set_payg_billing($client_billing, $call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'setpaygbilling.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_PUT;
        $call_options['data'] = $this->_serialiser->serialise($client_billing);

        return $this->_call($call_options);
    }

    /**
     * Updates the billing details of the current client, setting the client to the monthly billing model
     * For clients not set to pay themselves then the markup percentage field is ignored
     * @param array $client_billing Payg billing details of the client.
     *     This should be an array of the form
     *         array(
     *             'Currency' => The currency fees are paid in
     *             'ClientPays' => Whether client client pays for themselves
     *             'MarkupPercentage' => Sets the percentage markup used for all monthly tiers
     *         )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
    function set_monthly_billing($client_billing, $call_options = array()) {
        $call_options['route'] = $this->_clients_base_route.'setmonthlybilling.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_PUT;
        $call_options['data'] = $this->_serialiser->serialise($client_billing);

        return $this->_call($call_options);
    }
}