<?php
require_once dirname(__FILE__).'/class/base_classes.php';

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
        $this->_templates_base_route = $this->_base_route.'templates/'.$template_id.'.json';            
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
     *         )
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be the ID of the newly created template
     */
    function create($client_id, $template_details) {
        return $this->post_request($this->_base_route.'templates/'.$client_id.'.json', $template_details);
    }

    /**
     * Updates the current template with the provided code
     * @param array $template_details The details of the template
     *     This should be an array of the form
     *         array(
     *             'Name' => The name of the template
     *             'HtmlPageURL' => The url where the template html can be accessed
     *             'ZipFileURL' => The url where the template image zip can be accessed
     *         )
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function update($template_details) {
        return $this->put_request($this->_templates_base_route, $template_details);
    }

    /**
     * Deletes the current template from the system
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function delete() {
        return $this->delete_request($this->_templates_base_route);
    }

    /**
     * Gets the basic details of the current template
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'TemplateID' => The id of the template
     *     'Name' => The name of the template
     *     'PreviewURL' => A url where the template can be previewed from
     *     'ScreenshotURL' => The url of the template screenshot if one was provided
     * }
     */
    function get() {
        return $this->get_request($this->_templates_base_route);
    }
}