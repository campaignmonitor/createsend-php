<?php

namespace CreateSend\Wrapper\Transactional;

use CreateSend\Log\LogInterface;
use CreateSend\Serializer\SerializerInterface;
use CreateSend\Transport\TransportInterface;
use CreateSend\Wrapper\Base;
use CreateSend\Wrapper\Result;

/**
 * Class to access transactional from the create send API.
 * @author philoye
 *
 */
class Timeline extends Base
{
    /**
     * The client id to use for the timeline. Optional if using a client api key
     * @var array
     */
    private $_client_id_param;

    /**
     * Constructor.
     * @param array $auth_details Authentication details to use for API calls.
     *        This array must take one of the following forms:
     *        If using OAuth to authenticate:
     *        array(
     *          'access_token' => 'your access token',
     *          'refresh_token' => 'your refresh token')
     *
     *        Or if using an API key:
     *        array('api_key' => 'your api key')
     * @param string $client_id The client id to send email on behalf of
     *        Optional if using a client api key
     * @param LogInterface $log The logger to use. Used for dependency injection
     * @param string $protocol The protocol to use for requests (http|https)
     * @param SerializerInterface|string $host The host to send API requests to. There is no need to change this
     * @param SerializerInterface $serialiser The serialiser to use. Used for dependency injection
     * @param TransportInterface $transport The transport to use. Used for dependency injection
     */
    public function __construct(
        $auth_details, $client_id = null, LogInterface $log, $protocol = 'https', $host = CS_HOST, SerializerInterface $serialiser = null, TransportInterface $transport = null)
    {
        parent::__construct($auth_details, $protocol, $host, $log, $serialiser, $transport);
        $this->set_client($client_id);
    }

    /**
     * Change the client id used for calls after construction
     * Only required if using OAuth or an Account level API Key
     * @param string $client_id
     */
    public function set_client($client_id)
    {
        $this->_client_id_param = array("clientID" => $client_id);
    }

    /**
     * Gets the list of sent messages
     *
     * @param array $query Parameters used to filter results
     *     This should be an array of the form
     *         array(
     *             "status" => string delivered|bounced|spam|all
     *             "count" => integer optional, maximum number of results to return in a single request (default: 50, max: 200)
     *             "sentBeforeID" => string optional,  messageID used for pagination, returns emails sent before the specified message
     *             "sentAfterID" => string optional,  messageID used for pagination, returns emails sent after the specified message
     *             "smartEmaiLID" => string optional, smart email to filter by
     *             "group" => string optional, classic group name to filter by
     *         )
     * @return Result A successful response will be an array of the form
     *     array(
     *         array(
     *             "MessageID" => string
     *             "Status" => string
     *             "SentAt" => string
     *             "Recipient" => string
     *             "From" => string
     *             "Subject" => string
     *             "TotalOpens" => integer
     *             "TotalClicks" => integer
     *             "CanBeResent" => boolean
     *             "Group" => string, optional
     *             "SmartEmailID" => string, optional
     *         )
     *     )
     */
    public function messages($query = array())
    {
        $params = array_merge($this->_client_id_param, $query);
        return $this->get_request_with_params($this->_base_route . 'transactional/messages', $params);
    }

    /**
     * Gets the list of details of a sent message
     * @param $message_id , string Message ID to get the details for
     * @param bool $show_details
     * @return Result The details of the message
     */
    public function details($message_id, $show_details = false)
    {
        $params = array_merge($this->_client_id_param, array("statistics" => $show_details));
        return $this->get_request_with_params($this->_base_route . 'transactional/messages/' . $message_id, $params);
    }

    /**
     * Resend a sent message
     * @param $message_id , string Message ID to resend
     * @return Result The details of the message
     *      array(
     *          "MessageID" => string
     *          "Recipient" => string
     *          "Status" => string
     *      )
     */
    public function resend($message_id)
    {
        $data = array_merge($this->_client_id_param);
        return $this->post_request($this->_base_route . 'transactional/messages/' . $message_id . '/resend', $data);
    }

    /**
     * Gets statistics for sends/bounces/opens/clicks
     * @param array $query Parameters used to filter results
     *     This should be an array of the form
     *         array(
     *             "from" => iso-8601 date, optional, default 30 days ago
     *             "to" => iso-8601 date, optional, default today
     *             "timezone" => client|utc, optional, how to handle date boundaries
     *             "group" => string optional, classic group name to filter by
     *             "smartEmailID" => string optional. smart email to filter results by
     *         )
     * @return Result A successful response will be an array of the form
     *     array(
     *         array(
     *             "MessageID" => string
     *             "Status" => string
     *             "SentAt" => string
     *             "Recipient" => string
     *             "From" => string
     *             "Subject" => string
     *             "TotalOpens" => integer
     *             "TotalClicks" => integer
     *             "CanBeResent" => boolean
     *             "Group" => string, optional
     *             "SmartEmailID" => string, optional
     *         )
     *     )
     */
    public function statistics($query = array())
    {
        $params = array_merge($this->_client_id_param, $query);

        return $this->get_request_with_params($this->_base_route . 'transactional/statistics', $params);
    }
}
