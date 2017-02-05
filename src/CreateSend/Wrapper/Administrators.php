<?php

namespace CreateSend\Wrapper;

use CreateSend\Log\LogInterface;
use CreateSend\Serializer\SerializerInterface;
use CreateSend\Transport\TransportInterface;

/**
 * Class to access the administrator resources from the create send API.
 * This class includes functions to add and remove administrators,
 * along with getting details for a single administrator
 * @author pauld
 *
 */
class Administrators extends Base
{
    /**
     * The base route of the people resource.
     * @var string
     */
    private $_admins_base_route;

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
     * @param string $protocol The protocol to use for requests (http|https)
     * @param string $host The host to send API requests to. There is no need to change this
     * @param LogInterface $log The logger to use. Used for dependency injection
     * @param SerializerInterface $serialiser The serialiser to use. Used for dependency injection
     * @param TransportInterface $transport The transport to use. Used for dependency injection
     */
    public function __construct(
        $auth_details,
        $protocol = 'https',
        $host = CS_HOST,
        LogInterface $log = null,
        SerializerInterface $serialiser = null,
        TransportInterface $transport = null)
    {

        parent::__construct($auth_details, $protocol, $host, $log, $serialiser, $transport);
        $this->_admins_base_route = $this->_base_route . 'admins';
    }

    /**
     * Adds a new administrator to the current account
     * @param array $admin The administrator details to use during creation.
     *     This array should be of the form
     *     array (
     *         'EmailAddress' => The new administrator email address
     *         'Name' => The name of the new administrator
     *     )
     * @return Result A successful response will be empty
     */
    public function add($admin)
    {
        return $this->post_request($this->_admins_base_route . '.json', $admin);
    }

    /**
     * Updates details for an existing administrator associated with the current account
     * @param string $email The email address of the administrator to be updated
     * @param array $admin The updated administrator details to use for the update.
     *     This array should be of the form
     *     array (
     *         'EmailAddress' => The new email address
     *         'Name' => The updated name of the administrator
     *     )
     * @return Result A successful response will be empty
     */
    public function update($email, $admin)
    {
        return $this->put_request($this->_admins_base_route . '.json?email=' . urlencode($email), $admin);
    }

    /**
     * Gets the details for a specific administrator
     * @param string $email
     * @return Result A successful response will be an object of the form
     * {
     *     'EmailAddress' => The email address of the administrator
     *     'Name' => The name of the administrator
     *     'Status' => The status of the administrator
     *     )
     * }
     */
    public function get($email)
    {
        return $this->get_request($this->_admins_base_route . '.json?email=' . urlencode($email));
    }


    /**
     * deletes the given administrator from the current account
     * @param string $email The email address of the administrator to delete
     * @return Result A successful response will be empty
     */
    public function delete($email)
    {
        return $this->delete_request($this->_admins_base_route . '.json?email=' . urlencode($email));
    }
}
