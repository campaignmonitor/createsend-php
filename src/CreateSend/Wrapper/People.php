<?php

namespace CreateSend\Wrapper;

use CreateSend\Log\LogInterface;
use CreateSend\Serializer\SerializerInterface;
use CreateSend\Transport\TransportInterface;

/**
 * Class to access the person resources from the create send API.
 * This class includes functions to add and remove people,
 * along with getting details for a single person
 * @author tobyb
 *
 */
class People extends Base
{
    /**
     * The base route of the people resource.
     * @var string
     */
    private $_people_base_route;

    /**
     * Constructor.
     * @param string $client_id The client id that the people belong to
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
        $client_id,
        $auth_details,
        $protocol = 'https',
        $host = CS_HOST,
        LogInterface $log = null,
        SerializerInterface $serialiser = null,
        TransportInterface $transport = null)
    {

        parent::__construct($auth_details, $protocol, $host, $log, $serialiser, $transport);
        $this->set_client_id($client_id);

    }

    /**
     * Change the client id used for calls after construction
     * @param string $client_id
     */
    public function set_client_id($client_id)
    {
        $this->_people_base_route = $this->_base_route . 'clients/' . $client_id . '/people';
    }

    /**
     * Adds a new person to the specified client
     * @param array $person The person details to use during creation.
     *     This array should be of the form
     *     array (
     *         'EmailAddress' => The new person email address
     *         'Name' => The name of the new person
     *         'AccessLevel' => The access level of the new person. See http://www.campaignmonitor.com/api/clients/#setting_access_details for details
     *         'Password' => (optional) if not specified, an invitation will be sent to the person by email
     *     )
     *
     * @return Result A successful response will be empty
     */
    public function add($person)
    {
        return $this->post_request($this->_people_base_route . '.json', $person);
    }

    /**
     * Updates details for an existing person associated with the specified client.
     * @param string $email The email address of the person to be updated
     * @param array $person The updated person details to use for the update.
     *     This array should be of the form
     *     array (
     *         'EmailAddress' => The new  email address
     *         'Name' => The name of the person
     *         'AccessLevel' => the access level of the person
     *         'Password' => (optional) if specified, changes the password to the specified value
     *     )
     *
     * @return Result A successful response will be empty
     */
    public function update($email, $person)
    {
        return $this->put_request($this->_people_base_route . '.json?email=' . urlencode($email), $person);
    }

    /**
     * Gets the details for a specific person
     *
     * @param string $email
     * @return Result A successful response will be an object of the form
     * {
     *     'EmailAddress' => The email address of the person
     *     'Name' => The name of the person
     *     'Status' => The status of the person
     *     'AccessLevel' => The access level of the person
     *     )
     * }
     */
    public function get($email)
    {
        return $this->get_request($this->_people_base_route . '.json?email=' . urlencode($email));
    }

    /**
     * deletes the given person from the current client
     * @param string $email The email address of the person to delete
     * @return Result A successful response will be empty
     */
    public function delete($email)
    {
        return $this->delete_request($this->_people_base_route . '.json?email=' . urlencode($email));
    }
}
