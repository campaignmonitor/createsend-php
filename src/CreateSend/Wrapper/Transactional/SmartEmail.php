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
class SmartEmail extends Base
{
    /**
     * The client id to use for this mailer. Optional if using a client api key.
     * @var array
     */
    private $_client_id_param;

    /**
     * The base route of the smartemail resource.
     * @var string
     */
    private $_smartemail_base_route;

    /**
     * Constructor.
     * @param string $smartemail_id The smart email id to access (Ignored for list requests)
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
        $smartemail_id,
        $auth_details,
        $client_id = null,
        $protocol = 'https',
        $host = CS_HOST,
        LogInterface $log = null,
        SerializerInterface $serialiser = null,
        TransportInterface $transport = null)
    {
        parent::__construct($auth_details, $protocol, $host, $log, $serialiser, $transport);
        $this->set_client($client_id);
        $this->set_smartemail_id($smartemail_id);
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
     * Change the smart email id used for calls after construction
     * @param string $smartemail_id
     */
    public function set_smartemail_id($smartemail_id)
    {
        $this->_smartemail_base_route = $this->_base_route . 'transactional/smartEmail/' . $smartemail_id;
    }

    /**
     * Gets a list of smart emails
     * @param array $options optional array Query params to filter list
     *     This should be an array of the form
     *         array(
     *             "status" => "all|drafts|active"
     *         )
     * @return Result A successful response will be an object of the form
     *     array(
     *        array (
     *             'ID' => string
     *             'Name' => string
     *             'CreatedAt' => datestring
     *             'Status' => string
     *        )
     *     )
     */
    public function get_list($options = array())
    {
        $data = array_merge($this->_client_id_param, $options);
        return $this->get_request_with_params($this->_base_route . 'transactional/smartemail', $data);
    }

    /**
     * Sends a new smart transactional email
     * @param array $message The details of the template
     *     This should be an array of the form
     *         array(
     *             'To' => array(
     *                  "First Last <user@example.com>", "another@example.com"
     *             ) optional To recipients
     *             'CC' => array(
     *                  "First Last <user@example.com>", "another@example.com"
     *             ) optional CC recipients
     *             'BCC' => array(
     *                  "First Last <user@example.com>", "another@example.com"
     *             ) optional BCC recipients
     *             'Attachments' => array(
     *                  array(
     *                      "Name" => string
     *                      "Type" => string mime type
     *                      "Content" => string base64-encoded
     *                  )
     *             ) optional
     *             'Data' => array(
     *                  "variable" => "value",
     *                  "variable" => "value",
     *             )
     *         )
     * @param boolean $add_to_list optional. Whether to add all recipients to the list specified for the smart email
     * @return Result A successful response will be the include the details of the action, including a Message ID.
     *      array(
     *          array(
     *              "MessageID" => string
     *              "Recipient" => string
     *              "Status" => string
     *          )
     *      )
     */
    public function send($message, $add_to_list = true)
    {
        $data = array_merge($message, array("AddRecipientsToList" => $add_to_list));
        return $this->post_request($this->_smartemail_base_route . '/send.json', $data);
    }

    /**
     * Gets the details of Smart Email
     * @return Result A successful response will be an array of the form
     *     array(
     *        "SmartEmailID" => string
     *        "Name" => string
     *        "CreatedAt" => string
     *        "Status" => stirng
     *        "Properties" => array (
     *            "From" =. string
     *            "ReplyTo" => string
     *            "Subject" => string
     *            "Content": array(
     *                "HTML": string
     *                "Text": string
     *                "EmailVariables": array(
     *                    "username",
     *                    "reset_token"
     *                ),
     *                "InlineCss": boolean
     *            },
     *            "TextPreviewUrl": string
     *            "HtmlPreviewUrl": string
     *        ),
     *        "AddRecipientsToList": string
     *    }
     */
    public function get_details()
    {
        return $this->get_request($this->_smartemail_base_route);
    }
}
