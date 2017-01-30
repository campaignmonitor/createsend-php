<?php

namespace CreateSend\Wrapper;

use CreateSend\Log\LogInterface;
use CreateSend\Serializer\DoNothing;
use CreateSend\Serializer\SerializerInterface;
use CreateSend\Transport\TransportInterface;

/**
 * Class to access general resources from the create send API.
 * @author tobyb
 *
 */
class General extends Base
{

    /**
     * Get the authorization URL for your application, given the application's
     * Client ID, Client Secret, Redirect URI, Scope, and optional state data.
     *
     * @param int $client_id The Client ID of your registered OAuth application.
     * @param string $redirect_uri The Redirect URI of your registered OAuth application.
     * @param string $scope The comma-separated permission scope your application requires.
     *        See http://www.campaignmonitor.com/api/getting-started/#authenticating_with_oauth for details.
     * @param string $state Optional state data to be included in the URL.
     * @return string The authorization URL to which users of your application should be redirected.
     */
    public static function authorize_url(
        $client_id, $redirect_uri, $scope, $state = null)
    {
        $qs = "client_id=" . urlencode($client_id);
        $qs .= "&redirect_uri=" . urlencode($redirect_uri);
        $qs .= "&scope=" . urlencode($scope);
        if ($state) {
            $qs .= "&state=" . urlencode($state);
        }
        return CS_OAUTH_BASE_URI . '?' . $qs;
    }

    /**
     * Exchange a provided OAuth code for an OAuth access token, 'expires in'
     * value and refresh token.
     *
     * @param $client_id int The Client ID of your registered OAuth application.
     * @param $client_secret string The Client Secret of your registered OAuth application.
     * @param $redirect_uri string The Redirect URI of your registered OAuth application.
     * @param $code string The unique OAuth code to be exchanged for an access token.
     * @return Result A successful response will be an object of the form
     * {
     *     'access_token' => The access token to use for API calls
     *     'expires_in' => The number of seconds until this access token expires
     *     'refresh_token' => The refresh token to refresh the access token once it expires
     * }
     */
    public static function exchange_token(
        $client_id, $client_secret, $redirect_uri, $code)
    {

        $body = "grant_type=authorization_code";
        $body .= "&client_id=" . urlencode($client_id);
        $body .= "&client_secret=" . urlencode($client_secret);
        $body .= "&redirect_uri=" . urlencode($redirect_uri);
        $body .= "&code=" . urlencode($code);

        $options = array('contentType' => 'application/x-www-form-urlencoded');

        $wrap = new General(null, 'https', CS_HOST, null, new DoNothing(), null);

        return $wrap->post_request(CS_OAUTH_TOKEN_URI, $body, $options);
    }

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
        TransportInterface $transport = null
    ) {
        parent::__construct($auth_details, $protocol, $host, $log, $serialiser, $transport);
    }

    /**
     * Gets an array of valid timezones
     * @return Result A successful response will be an object of the form
     * array<string>(timezones)
     */
    public function get_timezones()
    {
        return $this->get_request($this->_base_route . 'timezones.json');
    }

    /**
     * Gets the current date in your accounts timezone
     * @return Result A successful response will be an object of the form
     * {
     *     'SystemDate' => string The current system date in your accounts timezone
     * }
     */
    public function get_systemdate()
    {
        return $this->get_request($this->_base_route . 'systemdate.json');
    }

    /**
     * Gets an array of valid countries
     * @return Result A successful response will be an object of the form
     * array<string>(countries)
     */
    public function get_countries()
    {
        return $this->get_request($this->_base_route . 'countries.json');
    }

    /**
     * Gets an array of clients
     * @return Result A successful response will be an object of the form
     * array(
     *     {
     *         'ClientID' => The clients API ID,
     *         'Name' => The clients name
     *     }
     * )
     */
    public function get_clients()
    {
        return $this->get_request($this->_base_route . 'clients.json');
    }

    /**
     * Gets your billing details.
     * @return Result A successful response will be an object of the form
     * {
     *     'Credits' => The number of credits belonging to the account
     * }
     */
    public function get_billing_details()
    {
        return $this->get_request($this->_base_route . 'billingdetails.json');
    }

    /**
     * Gets an array of administrators
     * @return Result A successful response will be an object of the form
     * array(
     *     {
     *         'EmailAddress' => The administrators email address
     *         'Name' => The administrators name
     *         'Status' => The administrators status
     *     }
     * )
     */
    public function get_administrators()
    {
        return $this->get_request($this->_base_route . 'admins.json');
    }

    /**
     * Retrieves the email address of the primary contact for this account
     * @return Result a successful response will be an array in the form:
     *        array('EmailAddress'=> email address of primary contact)
     */
    public function get_primary_contact()
    {
        return $this->get_request($this->_base_route . 'primarycontact.json');
    }

    /**
     * Assigns the primary contact for this account to the administrator with the specified email address
     * @param $emailAddress string The email address of the administrator designated to be the primary contact
     * @return Result a successful response will be an array in the form:
     *        array('EmailAddress'=> email address of primary contact)
     */
    public function set_primary_contact($emailAddress)
    {
        return $this->put_request($this->_base_route . 'primarycontact.json?email=' . urlencode($emailAddress), '');
    }

    /**
     * Get a URL which initiates a new external session for the user with the given email.
     * Full details: http://www.campaignmonitor.com/api/account/#single_sign_on
     *
     * @param $session_options array Options for initiating the external login session.
     *        This should be an array of the form:
     *        array(
     *          'Email' => 'The email address of the Campaign Monitor user for whom the login session should be created',
     *          'Chrome' => 'Which 'chrome' to display - Must be either "all", "tabs", or "none"',
     *          'Url' => 'The URL to display once logged in. e.g. "/subscribers/"',
     *          'IntegratorID' => 'The Integrator ID. You need to contact Campaign Monitor support to get an Integrator ID.',
     *          'ClientID' => 'The Client ID of the client which should be active once logged in to the Campaign Monitor account.' )
     *
     * @return Result A successful response will be an array of the form:
     *        array('SessionUrl'=> 'https://external1.createsend.com/cd/create/ABCDEF12/DEADBEEF?url=FEEDDAD1')
     */
    public function external_session_url($session_options)
    {
        return $this->put_request($this->_base_route . 'externalsession.json', $session_options);
    }
}
