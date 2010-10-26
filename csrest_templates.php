<?php
require_once 'csrest.php';

/**
 * Class to access a templates resources from the create send API.
 * This class includes functions to create update and delete templates
 * @author tobyb
 *
 */
class CS_REST_Templates extends CS_REST_Wrapper_Base {

    /**
     * The base route of the lists resource.
     * @var string
     * @access private
     */
    var $_templates_base_route;

    /**
     * Constructor.
     * @param $list_id string The template id to access (Ignored for create requests)
     * @param $api_key string Your api key (Ignored for get_apikey requests)
     * @param $protocol string The protocol to use for requests (http|https)
     * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
     * @param $host string The host to send API requests to. There is no need to change this
     * @param $log CS_REST_Log The logger to use. Used for dependency injection
     * @param $serialiser The serialiser to use. Used for dependency injection
     * @param $transport The transport to use. Used for dependency injection
     * @access public
     */
    function CS_REST_Templates (
    $template_id,
    $api_key,
    $protocol = 'https',
    $debug_level = CS_REST_LOG_NONE,
    $host = 'api.createsend.com',
    $log = NULL,
    $serialiser = NULL,
    $transport = NULL) {
        	
        $this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
        $this->set_template_id($template_id);
    }

    /**
     * Change the template id used for calls after construction
     * @param $template_id
     * @access public
     */
    function set_template_id($template_id) {
        $this->_templates_base_route = $this->_base_route.'templates/'.$template_id.
            '.'.$this->_serialiser->get_format();            
    }

    /**
     * Creates a new template for the specified client based on the provided data
     * @param string $client_id The client to create the template for
     * @param array $template_details The details of the template
     *     This should be an array of the form
     *         array(
     *             'Name' => The name of the template
     *             'HtmlPageURL' => The url where the template html can be accessed
     *             'ZipFileURL' => The url where the template image zip can be accessed
     *             'ScreenshotURL' => The url of a screenshot of the template
     *         )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (201)
     *     'response' => string The ID of the newly created template
     * )
     */
    function create($client_id, $template_details, $call_options = array()) {
        $list_details = $this->_serialiser->format_item('Template', $template_details);

        $call_options['route'] = $this->_base_route.'templates/'.$client_id.'.'.$this->_serialiser->get_format();
        $call_options['method'] = CS_REST_POST;
        $call_options['data'] = $this->_serialiser->serialise($template_details);

        return $this->_call($call_options);
    }

    /**
     * Updates the current template with the provided code
     * @param array $template_details The details of the template
     *     This should be an array of the form
     *         array(
     *             'Name' => The name of the template
     *             'HtmlPageURL' => The url where the template html can be accessed
     *             'ZipFileURL' => The url where the template image zip can be accessed
     *             'ScreenshotURL' => The url of a screenshot of the template
     *         )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The http response (Will be empty)
     * )
     */
    function update($template_details, $call_options = array()) {
        $list_details = $this->_serialiser->format_item('Template', $template_details);

        $call_options['route'] = $this->_templates_base_route;
        $call_options['method'] = CS_REST_PUT;
        $call_options['data'] = $this->_serialiser->serialise($template_details);

        return $this->_call($call_options);
    }

    /**
     * Deletes the current template from the system
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The http response (Will be empty)
     * )
     */
    function delete($call_options = array()) {
        $call_options['route'] = $this->_templates_base_route;
        $call_options['method'] = CS_REST_DELETE;

        return $this->_call($call_options);
    }

    /**
     * Gets the basic details of the current template
     * @param unknown_type $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'TemplateID' => The id of the template
     *         'Name' => The name of the template
     *         'PreviewURL' => A url where the template can be previewed from
     *         'ScreenshotURL' => The url of the template screenshot if one was provided
     *     )
     * )
     */
    function get($call_options = array()) {
        $call_options['route'] = $this->_templates_base_route;
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }
}