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
         * Indicates invalid Event Type
         * @var bool
         * @access private
         */
        private $_invalid_event_type;

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
                $this->_invalid_event_type = true;
                return new CS_REST_Wrapper_Result(null, 400);
            }
            $this->_event_type = strtolower($event_type);
        }


        /**
         * Get the name of event
         * @access public
         */
        function getEventType() {
            return $this->_event_type;
        }

        /**
         * Set the anonymous ID to use for non-identify events
         * @param $anonymous_id string Anonymous ID to use for non-identify events
         * @access private
         */
        private function setAnonymousID($anon_id) {
            if (!isset($anon_id)) {
                trigger_error('$anonymous_id needs to be set for identify events');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            $this->_anonymous_id = $anon_id;
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
         * @param $anonymous_id string Anonymous ID to use for non-identify events
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will include an Event ID.
         *      array(
         *          array(
         *              'EventID' => 'string'
         *          )
         *      )
         */
        function track($email, $event_name, $anonymous_id = NULL, $data = NULL)
        {
            if (!isset($email)) {
                trigger_error('$email needs to be set');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                trigger_error('$email needs to be a valid email address');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (!isset($event_name)) {
                trigger_error('$event_name needs to be set');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (strlen($event_name) > 1000) {
                trigger_error('$event_name needs to be shorter, max length is 1000 character');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (isset($data)) {
                if (!is_array($data)) {
                    trigger_error('$data needs to be a valid array');
                    return new CS_REST_Wrapper_Result(null, 400);
                }
            }
            if (strcmp($this->_event_type, "identify") === 0 && !isset($anonymous_id)) {
                trigger_error('$anonymous_id needs to be a valid string for identify event');
                return new CS_REST_Wrapper_Result(null, 400);
            }

            if (strcmp($this->_event_type, "identify") === 0 && isset($anonymous_id)) {
                $this->setAnonymousID($anonymous_id);
                $payload = array('ContactID' => array('Email' => $email, 'AnonymousID' => $this->_anonymous_id), 'EventName' => $event_name, 'Data' => $data);
            } else {
                $payload = array('ContactID' => array('Email' => $email), 'EventName' => $event_name, 'Data' => $data);
            }
            return $this->sendTrack($payload);
        }

        /*
         * Send track payload
         * @param $payload array Payload to send to track endpoint
         * @access private
         */
        private function sendTrack($payload = NULL) {
            if ($this->_invalid_event_type) {
                trigger_error('$event_type must be one of \'identify\', \'custom\' or \'shopify\'');
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

