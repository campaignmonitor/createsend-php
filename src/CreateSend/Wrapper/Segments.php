<?php

namespace CreateSend\Wrapper;

use CreateSend\Log\LogInterface;
use CreateSend\Serializer\SerializerInterface;
use CreateSend\Transport\TransportInterface;

/**
 * Class to access a segments resources from the create send API.
 * This class includes functions to create and edits segments
 * along with accessing the subscribers of a specific segment
 * @author tobyb
 *
 */
class Segments extends Base
{
    /**
     * The base route of the lists resource.
     * @var string
     */
    private $_segments_base_route;

    /**
     * Constructor.
     * @param string $segment_id The segment id to access (Ignored for create requests)
     * @param array $auth_details Authentication details to use for API calls.
     *        This array must take one of the following forms:
     *        If using OAuth to authenticate:
     *        array(
     *          'access_token' => 'your access token',
     *          'refresh_token' => 'your refresh token')
     *
     *        Or if using an API key:
     *        array('api_key' => 'your api key')
     * @param string $protocol The protocol to use for requests (http|https)
     * @param string $host The host to send API requests to. There is no need to change this
     * @param LogInterface $log The logger to use. Used for dependency injection
     * @param SerializerInterface $serialiser The serialiser to use. Used for dependency injection
     * @param TransportInterface $transport The transport to use. Used for dependency injection
     */
    public function __construct(
        $segment_id,
        $auth_details,
        $protocol = 'https',
        $host = CS_HOST,
        LogInterface $log = null,
        SerializerInterface $serialiser = null,
        TransportInterface $transport = null)
    {

        parent::__construct($auth_details, $protocol, $host, $log, $serialiser, $transport);
        $this->set_segment_id($segment_id);
    }

    /**
     * Change the segment id used for calls after construction
     * @param string $segment_id
     */
    public function set_segment_id($segment_id)
    {
        $this->_segments_base_route = $this->_base_route . 'segments/' . $segment_id;
    }

    /**
     * Creates a new segment on the given list with the provided details
     * @param int $list_id The list on which to create the segment
     * @param array $segment_details The details of the new segment
     *     This should be an array of the form
     *         array(
     *             'Title' => The title of the new segment
     *             'RuleGroups' => array(
     *                 array(
     *                     'Rules' => array(
     *                         array(
     *                             'RuleType' => The subject of this rule
     *                             'Clause' => The specific clauses for this rule
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     * @return Result A successful response will be the ID of the newly created segment
     */
    public function create($list_id, $segment_details)
    {
        return $this->post_request($this->_base_route . 'segments/' . $list_id . '.json', $segment_details);
    }

    /**
     * Updates the current segment with the provided details. Calls to this route will clear any existing rules
     * @param array $segment_details The new details for the segment
     *     This should be an array of the form
     *         array(
     *             'Title' => The title of the new segment
     *             'RuleGroups' => array(
     *                 array(
     *                     'Rules' => array(
     *                         array(
     *                             'RuleType' => The subject of this rule
     *                             'Clause' => The specific clauses for this rule
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     * @return Result A successful response will be empty
     */
    public function update($segment_details)
    {
        return $this->put_request($this->_segments_base_route . '.json', $segment_details);
    }

    /**
     * Adds the given rule to the current segment
     * @param array $rulegroup The rule to add to the segment
     *     This should be an array of the form
     *         array(
     *             'Rules' => array(
     *                 array(
     *                     'RuleType' => The subject of this rule
     *                     'Clause' => The specific clauses for this rule
     *                 )
     *             )
     *         )
     * @return Result A successful response will be empty
     */
    function add_rulegroup($rulegroup)
    {
        return $this->post_request($this->_segments_base_route . '/rules.json', $rulegroup);
    }

    /**
     * Gets the details of the current segment
     * @return Result A successful response will be an object of the form
     * {
     *     'ActiveSubscribers' => The number of active subscribers in this segment
     *     'Rules' => array(
     *         {
     *             'Subject' => The subject of the rule
     *             'Clauses' => array<string> The clauses making up this segment rule
     *         }
     *     ),
     *     'ListID' => The ID of the list on which this segment is applied
     *     'SegmentID' => The ID of this segment
     *     'Title' => The title of this segment
     * }
     */
    public function get()
    {
        return $this->get_request($this->_segments_base_route . '.json');
    }

    /**
     * Deletes an existing segment from the system
     * @return Result A successful response will be empty
     */
    public function delete()
    {
        return $this->delete_request($this->_segments_base_route . '.json');
    }

    /**
     * Deletes all rules for the current segment
     * @return Result A successful response will be empty
     */
    public function clear_rules()
    {
        return $this->delete_request($this->_segments_base_route . '/rules.json');
    }

    /**
     * Gets a paged collection of subscribers which fall into the given segment
     * @param string $subscribed_since The date to start getting subscribers from
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'NAME', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @return Result A successful response will be an object of the form
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
     *             'EmailAddress' => The email address of the subscriber
     *             'Name' => The name of the subscriber
     *             'Date' => The date that the subscriber was added to the list
     *             'State' => The current state of the subscriber, will be 'Active'
     *             'CustomFields' => array (
     *                 {
     *                     'Key' => The personalisation tag of the custom field
     *                     'Value' => The value of the custom field for this subscriber
     *                 }
     *             )
     *         }
     *     )
     * }
     */
    public function get_subscribers($subscribed_since = '', $page_number = null,
                             $page_size = null, $order_field = null, $order_direction = null)
    {

        return $this->get_request_paged($this->_segments_base_route . '/active.json?date=' . urlencode($subscribed_since),
            $page_number, $page_size, $order_field, $order_direction);
    }
}
