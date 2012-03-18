<?php
require_once dirname(__FILE__).'/class/base_classes.php';

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
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'WebVersionURL' => The webversion url of the campaign
     *         'CampaignID' => The id of the campaign
     *         'Subject' => The campaign subject
     *         'Name' => The name of the campaign
     *         'SentDate' => The sent data of the campaign
     *         'TotalRecipients' => The number of recipients of the campaign
     *     }
     * )
     */
    function get_campaigns() {
        return $this->get_request($this->_clients_base_route.'campaigns.json');
    }

    /**
     * Gets a list of scheduled campaigns for the current client
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'CampaignID' => The id of the campaign
     *         'Name' => The name of the campaign
     *         'Subject' => The subject of the campaign
     *         'DateCreated' => The date the campaign was created
     *         'PreviewURL' => The preview url of the draft campaign
     *         'DateScheduled' => The date the campaign is scheduled to be sent
     *         'ScheduledTimeZone' => The time zone in which the campaign is scheduled to be sent at 'DateScheduled'
     *     }
     * )
     */
    function get_scheduled() {
        return $this->get_request($this->_clients_base_route.'scheduled.json');
    }

    /**
     * Gets a list of draft campaigns for the current client
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'CampaignID' => The id of the campaign
     *         'Name' => The name of the campaign
     *         'Subject' => The subject of the campaign
     *         'DateCreated' => The date the campaign was created
     *         'PreviewURL' => The preview url of the draft campaign
     *     }
     * )
     */
    function get_drafts() {
        return $this->get_request($this->_clients_base_route.'drafts.json');
    }

    /**
     * Gets all subscriber lists the current client has created
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'ListID' => The id of the list
     *         'Name' => The name of the list
     *     }
     * )
     */
    function get_lists() {
        return $this->get_request($this->_clients_base_route.'lists.json');
    }

    /**
     * Gets all list segments the current client has created
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'ListID' => The id of the list owning this segment
     *         'SegmentID' => The id of this segment
     *         'Title' => The title of this segment
     *     }
     * )
     */
    function get_segments() {
        return $this->get_request($this->_clients_base_route.'segments.json');
    }

    /**
     * Gets all email addresses on the current clients suppression list
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ResultsOrderedBy' => The field the results are ordered by
     *     'OrderDirection' => The order direction
     *     'PageNumber' => The page number for the result set
     *     'PageSize' => The page size used
     *     'RecordsOnThisPage' => The number of records returned
     *     'TotalNumberOfRecords' => The total number of records available
     *     'NumberOfPages' => The total number of pages for this collection
     *     'Results' => array(
     *         {
     *             'EmailAddress' => The suppressed email address
     *             'Date' => The date the email was suppressed
     *             'State' => The state of the suppressed email
     *         }
     *     )
     * }
     */
    function get_suppressionlist($page_number = NULL, $page_size = NULL, $order_field = NULL, 
        $order_direction = NULL) {
            
        return $this->get_request_paged($this->_clients_base_route.'suppressionlist.json', 
            $page_number, $page_size, $order_field, $order_direction, '?');
    }

    /**
     * Gets all templates the current client has access to
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'TemplateID' => The id of the template
     *         'Name' => The name of the template
     *         'PreviewURL' => The url to preview the template from
     *         'ScreenshotURL' => The url of the template screenshot
     *     }
     * )
     */
    function get_templates() {
        return $this->get_request($this->_clients_base_route.'templates.json');
    }

    /**
     * Gets all templates the current client has access to
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ApiKey' => The clients API Key, THIS IS NOT THE CLIENT ID
     *     'BasicDetails' => 
     *     {
     *         'ClientID' => The id of the client
     *         'CompanyName' => The company name of the client
     *         'ContactName' => The contact name of the client
     *         'EmailAddress' => The clients contact email address
     *         'Country' => The clients country
     *         'TimeZone' => The clients timezone
     *     }
     *     'AccessDetails' => 
     *     {
     *         'AccessLevel' => The current access level of the client.
     *             This will be some bitwise combination of
     *
     *             CS_REST_CLIENT_ACCESS_REPORTS
     *             CS_REST_CLIENT_ACCESS_SUBSCRIBERS
     *             CS_REST_CLIENT_ACCESS_CREATESEND
     *             CS_REST_CLIENT_ACCESS_DESIGNSPAMTEST
     *             CS_REST_CLIENT_ACCESS_IMPORTSUBSCRIBERS
     *             CS_REST_CLIENT_ACCESS_IMPORTURL
     *
     *             or
     *             CS_REST_CLIENT_ACCESS_NONE
     *         'Username' => The clients current username
     *     }
     *     'BillingDetails' =>
     *     If on monthly billing
     *     {
     *         'CurrentTier' => The current monthly tier the client sits in
     *         'CurrentMonthlyRate' => The current pricing rate the client pays per month
     *         'MarkupPercentage' => The percentage markup applied to the base rates
     *         'Currency' => The currency paid in
     *         'ClientPays' => Whether the client pays for themselves
     *     }
     *     If paying per campaign
     *     {
     *         'CanPurchaseCredits' => Whether the client can purchase credits
     *         'BaseDeliveryFee' => The base fee payable per campaign
     *         'BaseRatePerRecipient' => The base fee payable per campaign recipient
     *         'BaseDesignSpamTestRate' => The base fee payable per design and spam test
     *         'MarkupOnDelivery' => The markup applied per campaign
     *         'MarkupPerRecipient' => The markup applied per campaign recipient
     *         'MarkupOnDesignSpamTest' => The markup applied per design and spam test
     *         'Currency' => The currency fees are paid in
     *         'ClientPays' => Whether client client pays for themselves
     *     }     
     * }
     */
    function get() {
        return $this->get_request(trim($this->_clients_base_route, '/').'.json');
    }

    /**
     * Deletes an existing client from the system
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function delete() {
        return $this->delete_request(trim($this->_clients_base_route, '/').'.json');
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
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be the ID of the newly created client
     */
    function create($client) {
        return $this->post_request($this->_base_route.'clients.json', $client);
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
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function set_basics($client_basics) {
        return $this->put_request($this->_clients_base_route.'setbasics.json', $client_basics);
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
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function set_access($client_access) {
        return $this->put_request($this->_clients_base_route.'setaccess.json', $client_access);
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
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function set_payg_billing($client_billing) {
        return $this->put_request($this->_clients_base_route.'setpaygbilling.json', $client_billing);
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
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function set_monthly_billing($client_billing) {
        return $this->put_request($this->_clients_base_route.'setmonthlybilling.json', $client_billing);
    }
}