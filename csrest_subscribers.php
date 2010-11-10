<?php
require_once 'csrest.php';

/**
 * Class to access a subscribers resources from the create send API.
 * This class includes functions to add and remove subscribers ,
 * along with accessing statistics for a single subscriber
 * @author tobyb
 *
 */
class CS_REST_Subscribers extends CS_REST_Wrapper_Base {

    /**
     * The base route of the subscriber resource.
     * @var string
     * @access private
     */
    var $_subscribers_base_route;

    /**
     * Constructor.
     * @param $list_id string The list id to access (Ignored for create requests)
     * @param $api_key string Your api key (Ignored for get_apikey requests)
     * @param $protocol string The protocol to use for requests (http|https)
     * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
     * @param $host string The host to send API requests to. There is no need to change this
     * @param $log CS_REST_Log The logger to use. Used for dependency injection
     * @param $serialiser The serialiser to use. Used for dependency injection
     * @param $transport The transport to use. Used for dependency injection
     * @access public
     */
    function CS_REST_Subscribers (
    $list_id,
    $api_key,
    $protocol = 'https',
    $debug_level = CS_REST_LOG_NONE,
    $host = 'api.createsend.com',
    $log = NULL,
    $serialiser = NULL,
    $transport = NULL) {
        	
        $this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
        $this->set_list_id($list_id);

    }

    /**
     * Change the list id used for calls after construction
     * @param $list_id
     * @access public
     */
    function set_list_id($list_id) {
        $this->_subscribers_base_route = $this->_base_route.'subscribers/'.$list_id;
    }

    /**
     * Adds a new subscriber to the specified list
     * @param array $subscriber The subscriber details to use during creation.
     *     This array should be of the form
     *     array (
     *         'EmailAddress' => The new subscribers email address
     *         'Name' => The name of the new subscriber
     *         'CustomFields' => array(
     *             array(
     *                 'Key' => The custom fields personalisation tag
     *                 'Value' => The value for this subscriber
     *             )
     *         )
     *         'Resubscribe' => Whether we should resubscribe this subscriber if they already exist in the list
     *     )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (201)
     *     'response' => string The HTTP response (It will be empty)
     * )
     */
    function add($subscriber, $call_options = array()) {
        $call_options['route'] = $this->_subscribers_base_route.'.json';
        $call_options['method'] = CS_REST_POST;
        $call_options['data'] = $this->_serialiser->serialise($subscriber);

        return $this->_call($call_options);
    }

    /**
     * Imports an array of subscribers into the current list
     * @param array $subscribers An array of subscribers to import.
     *     This array should be of the form
     *     array (
     *         array (
     *             'EmailAddress' => The new subscribers email address
     *             'Name' => The name of the new subscriber
     *             'CustomFields' => array(
     *                 array(
     *                     'Key' => The custom fields personalisation tag
     *                     'Value' => The value for this subscriber
     *                 )
     *             )
     *         )
     *     )
     * @param $resubscribe Whether we should resubscribe any existing subscribers
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (201)
     *     'response' => array(
     *         'TotalUniqueEmailsSubmitted' => The number of unique emails submitted in the call
     *         'TotalExistingSubscribers' => The number of subscribers who already existed in the list
     *         'TotalNewSubscribers' => The number of new subscriptions to the list
     *         'DuplicateEmailsInSubmission' => array<string> The emails which appeared more than once in the batch
     *         'FailureDetails' => array (
     *             array(
     *                 'EmailAddress' => The email address which failed
     *                 'Code' => The Create Send API Error code
     *                 'Message' => The reason for the failure
     *         )
     *     )
     * )
     *
     * For successful calls the FailureDetails element will be an empty array. Imports 'fail' when
     * any one subscriber submitted is not correctly subscribed, this does not mean that all
     * subscribers in the batch failed. Correct parsing of the response is required to
     * determine which subscribers were subscribed and which subscribers failed.
     */
    function import($subscribers, $resubscribe, $call_options = array()) {
        $subscribers = array(
		    'Resubscribe' => $resubscribe,
		    'Subscribers' => $subscribers
        );

        $call_options['route'] = $this->_subscribers_base_route.'/import.json';
        $call_options['method'] = CS_REST_POST;
        $call_options['data'] = $this->_serialiser->serialise($subscribers);

        return $this->_call($call_options);
    }

    /**
     * Gets a subscriber details, including custom fields
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'EmailAddress' => The subscriber email address
     *         'Name' => The subscribers name
     *         'Date' => The date the subscriber was added to the list
     *         'State' => The current state of the subscriber
     *         'CustomFields' => array(
     *             array(
     *                 'Key' => The custom fields personalisation tag
     *                 'Value' => The custom field value for this subscriber
     *             )
     *         )
     *     )
     * )
     */
    function get($email, $call_options = array()) {
        $call_options['route'] = $this->_subscribers_base_route.'.json?email='.urlencode($email);
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets the sending history to a specific subscriber
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             ID => The id of the email which was sent
     *             Type => 'Campaign'
     *             Name => The name of the email
     *             Actions => array(
     *                 array(
     *                     Event => The type of action (Click, Open, Unsubscribe etc)
     *                     Date => The date the event occurred
     *                     IPAddress => The IP that the event originated from
     *                     Detail => Any available details about the event i.e the URL for clicks
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    function get_history($email, $call_options = array()) {
        $call_options['route'] = $this->_subscribers_base_route.'/history.json?email='.urlencode($email);
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Unsubscribes the given subscriber from the current list
     * @param string $email The email address to unsubscribe
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => The HTTP response (It will be empty)
     * )
     */
    function unsubscribe($email, $call_options = array()) {
        // We need to build the subscriber data structure.
        $email = array(
		    'EmailAddress' => $email 
        );

        $call_options['route'] = $this->_subscribers_base_route.'/unsubscribe.json';
        $call_options['method'] = CS_REST_POST;
        $call_options['data'] = $this->_serialiser->serialise($email);

        return $this->_call($call_options);
    }


}