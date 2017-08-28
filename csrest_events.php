<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to send event data to the create send API.
 * @author cameronn
 *
 */
if (!class_exists('CS_REST_Events')) {
    class CS_REST_Events extends CS_REST_Wrapper_Base {

        /**
         * The base route of the clients resource.
         * @var string
         * @access private
         */
        var $_events_base_route;

        /**
         * The type of event supports 'shopify', 'identify' and 'custom'
         * @var string
         * @access private
         */
        private $_event_type;

        /**
         * Client ID
         * @var string
         * @access private
         */
        private $_client_id;

        /**
         * Anonymous ID
         * @var string
         * @access private
         */
        private $_anonymous_id;

        /**
         * User ID
         * @var string
         * @access private
         */
        private $_user_id;

        /**
         * Email address
         * @var string
         * @access private
         */
        private $_email;

        /**
         * Indicates invalid Event
         * @var bool
         * @access private
         */
        private $_invalid_event = false;

        /**
         * Constructor.
         *
         * @param $auth_details array Authentication details to use for API calls.
         *        This array must take one of the following forms:
         *        If using OAuth to authenticate:
         *        array(
         *          'access_token' => 'your access token',
         *          'refresh_token' => 'your refresh token')
         *
         *        Or if using an API key:
         *        array('api_key' => 'your api key')
         * @param $client_id string The client id to send event to
         * @param $event_type string The event type we support - `custom`, `identify` and `shopify`
         * @param $protocol string The protocol to use for requests (http|https)
         * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
         * @param $host string The host to send API requests to. There is no need to change this
         * @param $log CS_REST_Log The logger to use. Used for dependency injection
         * @param $serialiser The serialiser to use. Used for dependency injection
         * @param $transport The transport to use. Used for dependency injection
         * @access public
         */
        function __construct (
        $auth_details,
        $client_id,
        $event_type,
        $protocol = 'https',
        $debug_level = CS_REST_LOG_NONE,
        $host = 'api.createsend.com',
        $log = NULL,
        $serialiser = NULL,
        $transport = NULL) {
            parent::__construct($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
            $this->set_client_id($client_id);
            if (!isset($event_type)) {
                trigger_error('$event_type should be one of \'custom\', \'identify\' or \'shopify\'');
            }
            $this->setEventType($event_type);
        }

        /**
         * Change the client id used for calls after construction
         * @param $client_id
         * @access public
         */
        function set_client_id($client_id) {
            if (!isset($client_id)) {
                trigger_error('$client_id needs to be set');
            }
            $this->_events_base_route = $this->_base_route.'events/'.$client_id.'/';
            $this->_client_id = $client_id;
        }

        /**
         * Set the type of event that we support: 'custom', 'identify' and 'shopify'
         * @param $event_type string Event that we support: 'custom', 'identify' and 'shopify'
         * @access private
         */
        private function setEventType($event_type) {
            if (!isset($event_type)) {
                trigger_error('$event_type needs to be set');
                return new CS_REST_Wrapper_Result(null, 400);
            }

            if (strcmp($event_type, "custom") !== 0 &&
                strcmp($event_type,"identify") !== 0 &&
                strcmp($event_type,"shopify") !== 0) {
                trigger_error('$event_type needs to be one of \'custom\', \'identify\' or \'shopify\'');
                $this->_invalid_event = true;
                return new CS_REST_Wrapper_Result(null, 400);
            }
            $this->_event_type = $event_type;
        }


        /*
         * Validate email address
         * @param $email string email address
         * @access private
         */
        private function validateEmail($email) {
            if (!isset($email)) {
                trigger_error('$email needs to be set');
                return new CS_REST_Wrapper_Result(null, 400);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                trigger_error('$email needs to be a valid email address');
                return new CS_REST_Wrapper_Result(null, 400);
            }

            return $email;
        }

        /**
         * Get the event type name
         * @access public
         */
        function getEventType() {
            return $this->_event_type;
        }

        /**
         * Tracks an event
         * @param string $email required email in the form "user@example.com"
         * 
         * @param string $event_name. Name to group events by for reporting max length 1000 
         *    For example "Page View", "Order confirmation"
         *
         * @param array $data optional. Event payload.
         *      This should be an array, with details of the event
         *          array(
         *              'RandomFieldObject'  => array(
         *                                      'Example'' => 'test'
         *                                  ),
         *              'RandomFieldURL' => 'Example',
         *              'RandomArray' => array(1,3,5,6,7),
         *          )
         * @param $anonymous_id string Anonymous ID to use for identify events
         * @param $user_id string User ID to use for identify events
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will include an Event ID.
         *      array(
         *          array(
         *              'EventID' => 'string'
         *          )
         *      )
         */
        function track($email, $event_name, $anonymous_id = NULL, $user_id = NULL, $data = NULL)
        {
            // Basic validation
            if (!isset($event_name)) {
                trigger_error('$event_name needs to be set');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (strlen($event_name) > 1000) {
                trigger_error('$event_name needs to be shorter, max length is 1000 bytes');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (isset($data)) {
                if (!is_array($data)) {
                    trigger_error('$data needs to be a valid array');
                    return new CS_REST_Wrapper_Result(null, 400);
                }
            }
            if (empty($data)) {
                $data = NULL;
            }

            if (strcmp($this->_event_type, "identify") === 0) {
                return $this->sendIdentifyTrack($email, $event_name, $anonymous_id, $user_id, $data);
            } elseif (strcmp($this->_event_type, "custom") === 0 || strcmp($this->_event_type, "shopify") === 0) {
                return $this->sendNonIdentifyTrack($email, $event_name, $anonymous_id, $user_id, $data);
            }

            trigger_error('event type is invalid. Supported - custom, identify or shopify');
            return new CS_REST_Wrapper_Result(null, 400);
        }

        /*
         * Send identify track event
         * @param $email string email address
         * @param $event_name string event name
         * @param $anonymousId string anonymous id
         * @param $userId string user id
         * @param $data array event data
         * @access private
         */
        private function sendIdentifyTrack($email, $event_name, $anonymousId, $userId, $data) {
            if (!isset($email)) {
                trigger_error('email needs to be a set for identify event');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            $minRequiredParam = 1; // anonymous id / user id
            $paramPresent = 0;
            if (isset($anonymousId)) {
                $paramPresent += 1;
            }
            if (isset($userId)) {
                $paramPresent += 1;
            }

            if ($paramPresent < $minRequiredParam) {
                trigger_error('at least one of: anonymous id, user id needs to be set and be a valid string for identify event');
                return new CS_REST_Wrapper_Result(null, 400);
            }

            $this->_anonymous_id = $anonymousId;
            $this->_email = $this->validateEmail($email);
            $this->_user_id = $userId;

            $payload = array(
                'ContactID' =>
                    array(
                        'Email' => $this->_email,
                        'AnonymousID' => $this->_anonymous_id,
                        'UserID' => $this->_user_id,
                    ),
                'EventName' => $event_name,
                'Data' => $data
            );
            return $this->sendTrack($payload);
        }

        /*
         * Send non-identify track event (custom or shopify)
         * @param $email string email
         * @param $event_name string event name
         * @param $data array event data
         */
        private function sendNonIdentifyTrack($email, $event_name, $anonymousId, $userId, $data) {
            $paramPresent = 0;
            if (isset($email)) {
                $this->_email = $this->validateEmail($email);
                $paramPresent += 1;
            } else {
                $this->_email = NULL;
            }
            $minRequiredParam = 1; // anonymous id / user id / email
            if (isset($anonymousId)) {
                $paramPresent += 1;
            }
            if (isset($userId)) {
                $paramPresent += 1;
            }

            if ($paramPresent < $minRequiredParam) {
                trigger_error('at least one of: anonymous id, user id, email needs to be set and be a valid string for identify event');
                return new CS_REST_Wrapper_Result(null, 400);
            }

            $this->_anonymous_id = $anonymousId;
            $this->_user_id = $userId;

            $payload = array(
                'ContactID' =>
                    array(
                        'Email' => $this->_email,
                        'AnonymousID' => $this->_anonymous_id,
                        'UserID' => $this->_user_id
                    ),
                'EventName' => $event_name,
                'Data' => $data
            );
            return $this->sendTrack($payload);
        }

        /*
         * Send track event payload
         * @param $payload array Payload to send to track endpoint
         * @access private
         */
        private function sendTrack($payload) {
            if ($this->_invalid_event) {
                trigger_error('$event_type must be one of \'identify\', \'custom\' or \'shopify\'');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            // Basic validation before finally POST'ing
            if (!isset($this->_base_route) || !isset($this->_event_type) || !isset($this->_client_id)) {
                trigger_error('one of: $_base_route, $_event_type, $_client_id is missing during URL construction');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (isset($payload) && is_array($payload)) {
                $event_url = $this->_base_route . 'events/' . $this->_event_type . '/' . $this->_client_id . '/track';
                return $this->post_request($event_url, $payload);
            }
            trigger_error('$payload needs to be a valid array');
            return new CS_REST_Wrapper_Result(null, 400);
        }
    }
}

