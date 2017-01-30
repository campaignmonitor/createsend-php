<?php

namespace CreateSend\Wrapper;

use CreateSend\Log\LogInterface;
use CreateSend\Serializer\SerializerInterface;
use CreateSend\Transport\TransportInterface;

defined('CS_REST_CUSTOM_FIELD_TYPE_TEXT') or define('CS_REST_CUSTOM_FIELD_TYPE_TEXT', 'Text');
defined('CS_REST_CUSTOM_FIELD_TYPE_NUMBER') or define('CS_REST_CUSTOM_FIELD_TYPE_NUMBER', 'Number');
defined('CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE') or define('CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE', 'MultiSelectOne');
defined('CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTMANY') or define('CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTMANY', 'MultiSelectMany');
defined('CS_REST_CUSTOM_FIELD_TYPE_DATE') or define('CS_REST_CUSTOM_FIELD_TYPE_DATE', 'Date');
defined('CS_REST_CUSTOM_FIELD_TYPE_COUNTRY') or define('CS_REST_CUSTOM_FIELD_TYPE_COUNTRY', 'Country');
defined('CS_REST_CUSTOM_FIELD_TYPE_USSTATE') or define('CS_REST_CUSTOM_FIELD_TYPE_USSTATE', 'USState');

defined('CS_REST_LIST_WEBHOOK_SUBSCRIBE') or define('CS_REST_LIST_WEBHOOK_SUBSCRIBE', 'Subscribe');
defined('CS_REST_LIST_WEBHOOK_DEACTIVATE') or define('CS_REST_LIST_WEBHOOK_DEACTIVATE', 'Deactivate');
defined('CS_REST_LIST_WEBHOOK_UPDATE') or define('CS_REST_LIST_WEBHOOK_UPDATE', 'Update');
defined('CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS') or define('CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS', 'AllClientLists');
defined('CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST') or define('CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST', 'OnlyThisList');

/**
 * Class to access a lists resources from the create send API.
 * This class includes functions to create lists and custom fields,
 * along with accessing the subscribers of a specific list
 * @author tobyb
 *
 */
class Lists extends Base
{
    /**
     * The base route of the lists resource.
     * @var string
     */
    private $_lists_base_route;

    /**
     * Constructor.
     * @param string $list_id The list id to access (Ignored for create requests)
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
        $list_id,
        $auth_details,
        $protocol = 'https',
        $host = CS_HOST,
        LogInterface $log = null,
        SerializerInterface $serialiser = null,
        TransportInterface $transport = null)
    {

        parent::__construct($auth_details, $protocol, $host, $log, $serialiser, $transport);
        $this->set_list_id($list_id);
    }

    /**
     * Change the list id used for calls after construction
     * @param string $list_id
     */
    public function set_list_id($list_id)
    {
        $this->_lists_base_route = $this->_base_route . 'lists/' . $list_id . '/';
    }

    /**
     * Creates a new list based on the provided details.
     * Both the UnsubscribePage and the ConfirmationSuccessPage parameters are optional
     * @param string $client_id The client to create the campaign for
     * @param array $list_details The list details to use during creation.
     *     This array should be of the form
     *     array(
     *         'Title' => string The list title
     *         'UnsubscribePage' => string The page to redirect subscribers to when they unsubscribe
     *         'ConfirmedOptIn' => boolean Whether this list requires confirmation of subscription
     *         'ConfirmationSuccessPage' => string The page to redirect subscribers to when
     *             they confirm their subscription
     *         'UnsubscribeSetting' => string Unsubscribe setting must be
     *             CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS or
     *             CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST.
     *             See the documentation for details: http://www.campaignmonitor.com/api/lists/#creating_a_list
     *     )
     * @return Result A successful response will be the ID of the newly created list
     */
    public function create($client_id, $list_details)
    {
        return $this->post_request($this->_base_route . 'lists/' . $client_id . '.json', $list_details);
    }

    /**
     * Updates the details of an existing list
     * Both the UnsubscribePage and the ConfirmationSuccessPage parameters are optional
     * @param array $list_details The list details to use during creation.
     *     This array should be of the form
     *     array(
     *         'Title' => string The list title
     *         'UnsubscribePage' => string The page to redirect subscribers to when they unsubscribe
     *         'ConfirmedOptIn' => boolean Whether this list requires confirmation of subscription
     *         'ConfirmationSuccessPage' => string The page to redirect subscribers to when
     *             they confirm their subscription
     *         'UnsubscribeSetting' => string Unsubscribe setting must be
     *             CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS or
     *             CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST.
     *             See the documentation for details: http://www.campaignmonitor.com/api/lists/#updating_a_list
     *         'AddUnsubscribesToSuppList' => boolean When UnsubscribeSetting
     *             is CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS,
     *             whether unsubscribes from this list should be added to the
     *             suppression list.
     *         'ScrubActiveWithSuppList' => boolean When UnsubscribeSetting
     *             is CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS,
     *             whether active subscribers should be scrubbed against the
     *             suppression list.
     *     )
     * @return Result A successful response will be empty
     */
    public function update($list_details)
    {
        return $this->put_request(trim($this->_lists_base_route, '/') . '.json', $list_details);
    }

    /**
     * Creates a new custom field for the current list
     * @param array $custom_field_details The details of the new custom field.
     *     This array should be of the form
     *     array(
     *         'FieldName' => string The name of the new custom field
     *         'DataType' => string The data type of the new custom field
     *             This should be one of
     *             CS_REST_CUSTOM_FIELD_TYPE_TEXT
     *             CS_REST_CUSTOM_FIELD_TYPE_NUMBER
     *             CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE
     *             CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTMANY
     *             CS_REST_CUSTOM_FIELD_TYPE_DATE
     *             CS_REST_CUSTOM_FIELD_TYPE_COUNTRY
     *             CS_REST_CUSTOM_FIELD_TYPE_USSTATE
     *         'Options' => array<string> Valid options for either
     *           Multi-Optioned field data type.
     *         'VisibleInPreferenceCenter' => boolean representing whether or
     *           not the field should be visible in the subscriber preference
     *           center.
     *     )
     * @return Result A successful response will be the
     * personalisation tag of the newly created custom field
     */
    public function create_custom_field($custom_field_details)
    {
        return $this->post_request($this->_lists_base_route . 'customfields.json', $custom_field_details);
    }

    /**
     * Updates a custom field for the current list
     * @param string $key The personalisation tag of the field to update
     * @param array $custom_field_details The details of the new custom field.
     *     This array should be of the form
     *     array(
     *         'FieldName' => string The new name for the field
     *         'VisibleInPreferenceCenter' => boolean representing whether or
     *           not the field should be visible in the subscriber preference
     *           center.
     *     )
     * @return Result A successful response will be the
     * personalisation tag of the updated custom field
     */
    public function update_custom_field($key, $custom_field_details)
    {
        return $this->put_request($this->_lists_base_route . 'customfields/' . rawurlencode($key) . '.json',
            $custom_field_details);
    }

    /**
     * Updates the optios for the given multi-optioned custom field
     * @param string $key The personalisation tag of the field to update
     * @param array $new_options The set of options to add to the custom field
     * @param boolean $keep_existing Whether to remove any existing options not contained in $new_options
     * @return Result A successful response will be empty
     */
    public function update_field_options($key, $new_options, $keep_existing)
    {
        $options = array(
            'KeepExistingOptions' => $keep_existing,
            'Options' => $new_options
        );

        return $this->put_request($this->_lists_base_route . 'customfields/' . rawurlencode($key) . '/options.json',
            $options);
    }

    /**
     * Deletes an existing list from the system
     * @return Result A successful response will be empty
     */
    public function delete()
    {
        return $this->delete_request(trim($this->_lists_base_route, '/') . '.json');
    }

    /**
     * Deletes an existing custom field from the system
     * @param string $key
     * @return Result A successful response will be empty
     */
    public function delete_custom_field($key)
    {
        return $this->delete_request($this->_lists_base_route . 'customfields/' . rawurlencode($key) . '.json');
    }

    /**
     * Gets a list of all custom fields defined for the current list
     * @return Result A successful response will be an object of the form
     * array(
     *     {
     *         'FieldName' => The name of the custom field
     *         'Key' => The personalisation tag of the custom field
     *         'DataType' => The data type of the custom field
     *         'FieldOptions' => Valid options for a multi-optioned custom field
     *         'VisibleInPreferenceCenter' => Boolean representing whether or
     *           not the field is visible in the subscriber preference center
     *     }
     * )
     */
    public function get_custom_fields()
    {
        return $this->get_request($this->_lists_base_route . 'customfields.json');
    }

    /**
     * Gets a list of all segments defined for the current list
     * @return Result A successful response will be an object of the form
     * array(
     *     {
     *         'ListID' => The current list id
     *         'SegmentID' => The id of this segment
     *         'Title' => The title of this segment
     *     }
     * )
     */
    public function get_segments()
    {
        return $this->get_request($this->_lists_base_route . 'segments.json');
    }

    /**
     * Gets all active subscribers added since the given date
     * @param string $added_since The date to start getting subscribers from
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
    public function get_active_subscribers($added_since = '', $page_number = null,
                                    $page_size = null, $order_field = null, $order_direction = null)
    {

        return $this->get_request_paged($this->_lists_base_route . 'active.json?date=' . urlencode($added_since),
            $page_number, $page_size, $order_field, $order_direction);
    }

    /**
     * Gets all unconfirmed subscribers added since the given date
     * @param string $added_since The date to start getting subscribers from
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
     *             'State' => The current state of the subscriber, will be 'Unconfirmed'
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
    public function get_unconfirmed_subscribers($added_since = '', $page_number = null,
                                         $page_size = null, $order_field = null, $order_direction = null)
    {

        return $this->get_request_paged($this->_lists_base_route . 'unconfirmed.json?date=' . urlencode($added_since),
            $page_number, $page_size, $order_field, $order_direction);
    }

    /**
     * Gets all bounced subscribers who have bounced out since the given date
     * @param string $bounced_since The date to start getting subscribers from
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
     *             'Date' => The date that the subscriber bounced out of the list
     *             'State' => The current state of the subscriber, will be 'Bounced'
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
    public function get_bounced_subscribers($bounced_since = '', $page_number = null,
                                     $page_size = null, $order_field = null, $order_direction = null)
    {

        return $this->get_request_paged($this->_lists_base_route . 'bounced.json?date=' . urlencode($bounced_since),
            $page_number, $page_size, $order_field, $order_direction);
    }

    /**
     * Gets all unsubscribed subscribers who have unsubscribed since the given date
     * @param string $unsubscribed_since The date to start getting subscribers from
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
     *             'Date' => The date that the subscriber was unsubscribed from the list
     *             'State' => The current state of the subscriber, will be 'Unsubscribed'
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
    public function get_unsubscribed_subscribers($unsubscribed_since = '', $page_number = null,
                                          $page_size = null, $order_field = null, $order_direction = null)
    {

        return $this->get_request_paged($this->_lists_base_route . 'unsubscribed.json?date=' . urlencode($unsubscribed_since),
            $page_number, $page_size, $order_field, $order_direction);
    }

    /**
     * Gets all subscribers who have been deleted since the given date
     * @param string $deleted_since The date to start getting subscribers from
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
     *             'Date' => The date that the subscriber was deleted from the list
     *             'State' => The current state of the subscriber, will be 'Deleted'
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
    public function get_deleted_subscribers($deleted_since = '', $page_number = null,
                                     $page_size = null, $order_field = null, $order_direction = null)
    {

        return $this->get_request_paged($this->_lists_base_route . 'deleted.json?date=' . urlencode($deleted_since),
            $page_number, $page_size, $order_field, $order_direction);
    }

    /**
     * Gets the basic details of the current list
     * @return Result A successful response will be an object of the form
     * {
     *     'ListID' => The id of the list
     *     'Title' => The title of the list
     *     'UnsubscribePage' => The page which subscribers are redirected to upon unsubscribing
     *     'ConfirmedOptIn' => Whether the list is Double-Opt In
     *     'ConfirmationSuccessPage' => The page which subscribers are
     *         redirected to upon confirming their subscription
     *     'UnsubscribeSetting' => The unsubscribe setting for the list. Will
     *         be either CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS or
     *         CS_REST_LIST_UNSUBSCRIBE_SETTING_ONLY_THIS_LIST.
     * }
     */
    public function get()
    {
        return $this->get_request(trim($this->_lists_base_route, '/') . '.json');
    }

    /**
     * Gets statistics for list subscriptions, deletions, bounces and unsubscriptions
     * @return Result A successful response will be an object of the form
     * {
     *     'TotalActiveSubscribers'
     *     'NewActiveSubscribersToday'
     *     'NewActiveSubscribersYesterday'
     *     'NewActiveSubscribersThisWeek'
     *     'NewActiveSubscribersThisMonth'
     *     'NewActiveSubscribersThisYeay'
     *     'TotalUnsubscribes'
     *     'UnsubscribesToday'
     *     'UnsubscribesYesterday'
     *     'UnsubscribesThisWeek'
     *     'UnsubscribesThisMonth'
     *     'UnsubscribesThisYear'
     *     'TotalDeleted'
     *     'DeletedToday'
     *     'DeletedYesterday'
     *     'DeletedThisWeek'
     *     'DeletedThisMonth'
     *     'DeletedThisYear'
     *     'TotalBounces'
     *     'BouncesToday'
     *     'BouncesYesterday'
     *     'BouncesThisWeek'
     *     'BouncesThisMonth'
     *     'BouncesThisYear'
     * }
     */
    public function get_stats()
    {
        return $this->get_request($this->_lists_base_route . 'stats.json');
    }

    /**
     * Gets the webhooks which are currently subcribed to event on this list
     * @return Result A successful response will be an object of the form
     * array(
     *     {
     *         'WebhookID' => The if of
     *         'Events' => An array of the events this webhook is subscribed to ('Subscribe', 'Update', 'Deactivate')
     *         'Url' => The url the webhook data will be POSTed to
     *         'Status' => The current status of this webhook
     *         'PayloadFormat' => The format in which data will be POSTed
     *     }
     * )
     */
    public function get_webhooks()
    {
        return $this->get_request($this->_lists_base_route . 'webhooks.json');
    }

    /**
     * Creates a new webhook based on the provided details
     * @param array $webhook The details of the new webhook
     *     This array should be of the form
     *     array(
     *         'Events' => array<string> The events to subscribe to. Valid events are
     *             CS_REST_LIST_WEBHOOK_SUBSCRIBE,
     *             CS_REST_LIST_WEBHOOK_DEACTIVATE,
     *             CS_REST_LIST_WEBHOOK_UPDATE
     *         'Url' => string The url of the page to POST the webhook events to
     *         'PayloadFormat' => The format to use when POSTing webhook event data, either
     *             CS_REST_WEBHOOK_FORMAT_JSON or
     *             CS_REST_WEBHOOK_FORMAT_XML
     *         (xml or json)
     *     )
     * @return Result A successful response will be the ID of the newly created webhook
     */
    public function create_webhook($webhook)
    {
        return $this->post_request($this->_lists_base_route . 'webhooks.json', $webhook);
    }

    /**
     * Sends test events for the given webhook id
     * @param string $webhook_id The id of the webhook to test
     * @return Result A successful response will be empty.
     */
    public function test_webhook($webhook_id)
    {
        return $this->get_request($this->_lists_base_route . 'webhooks/' . $webhook_id . '/test.json');
    }

    /**
     * Deletes an existing webhook from the system
     * @param string $webhook_id The id of the webhook to delete
     * @return Result A successful response will be empty
     */
    public function delete_webhook($webhook_id)
    {
        return $this->delete_request($this->_lists_base_route . 'webhooks/' . $webhook_id . '.json');
    }

    /**
     * Activates an existing deactivated webhook
     * @param string $webhook_id The id of the webhook to activate
     * @return Result A successful response will be empty
     */
    public function activate_webhook($webhook_id)
    {
        return $this->put_request($this->_lists_base_route . 'webhooks/' . $webhook_id . '/activate.json', '');
    }

    /**
     * Deactivates an existing activated webhook
     * @param string $webhook_id The id of the webhook to deactivate
     * @return Result A successful response will be empty
     */
    public function deactivate_webhook($webhook_id)
    {
        return $this->put_request($this->_lists_base_route . 'webhooks/' . $webhook_id . '/deactivate.json', '');
    }
}
