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
class ClassicEmail extends Base
{
    /**
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
     * @param string $protocol The protocol to use for requests (http|https)
     * @param string $host The host to send API requests to. There is no need to change this
     * @param LogInterface $log The logger to use. Used for dependency injection
     * @param SerializerInterface $serialiser The serialiser to use. Used for dependency injection
     * @param TransportInterface $transport The transport to use. Used for dependency injection
     */
    public function __construct(
        $auth_details,
        $client_id = NULL,
        $protocol = 'https',
        $host = 'api.createsend.com',
        LogInterface $log = NULL,
        SerializerInterface $serialiser = NULL,
        TransportInterface $transport = NULL)
    {
        parent::__construct($auth_details, $protocol, $host, $log, $serialiser, $transport);
        $this->set_client($client_id);
    }

    /**
     * The client id to use for the timeline. Optional if using a client api key
     * @var array
     */
    private $_client_id_param;

    /**
     * Change the client id used for calls after construction
     * Only required if using OAuth or an Account level API Key
     * @param $client_id
     */
    public function set_client($client_id)
    {
        $this->_client_id_param = array("clientID" => $client_id);
    }

    /**
     * Sends a new classic transactional email
     * @param array $message The details of the template
     *     This should be an array of the form
     *         array(
     *             'From' => string required The From name/email in the form "first last <user@example.com>"
     *             'ReplyTo' => string optional The Reply-To address
     *             'To' => array(
     *                "First Last <user@example.com>", "another@example.com"
     *             ) optional To recipients
     *             'CC' => array(
     *                "First Last <user@example.com>", "another@example.com"
     *             ) optional CC recipients
     *             'BCC' => array(
     *                "First Last <user@example.com>", "another@example.com"
     *             ) optional BCC recipients
     *             'Subject' => string required The subject of the email
     *             'Html' => string The HTML content of the message
     *             'Text' => string optional The text content of the message
     *             'Attachments' => array
     *                "Name" => string required
     *                "Type" => string required
     *                "Content" => string required
     *             ) optional
     *         )
     * @param string $group Optional. Name to group emails by for reporting
     *    For example "Password reset", "Order confirmation"
     * @param string $add_to_list_ID
     * @param array $options optional. Advanced options for sending this email (optional)
     *      This should be an array, each property is optionals
     *          array(
     *            TrackOpens  => whether to track opens, defaults to true
     *            TrackClicks => whether to track clicks, defaults to true
     *            InlineCSS   => whether inline CSS, defaults to true
     *            AddRecipientsToListID => ID of a list to add all recipeints to
     *          )
     * @return Result A successful response will be the include the details of the action, including a Message ID.
     *      array(
     *          array(
     *              "MessageID" => string
     *              "Recipient" => string
     *              "Status" => string
     *          )
     *      )
     */
    public function send($message, $group = null, $add_to_list_ID = null, $options = array())
    {
        $group_param = array("Group" => $group);
        $add_to_list_param = array("AddRecipientsToListID" => $add_to_list_ID);
        $data = array_merge($this->_client_id_param, $message, $group_param, $add_to_list_param, $options);
        return $this->post_request($this->_base_route . 'transactional/classicemail/send', $data);
    }

    /**
     * Gets the list of Classic Groups
     * @return Result A successful response will be an array of the form
     *     array(
     *         array(
     *             "Group" => string
     *             "CreatedAt" => string
     *         )
     *     )
     */
    public function groups()
    {
        $data = array_merge($this->_client_id_param);
        return $this->get_request($this->_base_route . 'transactional/classicemail/groups', $data);
    }
}
