<?php

class CS_REST_SerialiserFactory {
	function get_available_serialiser($log) {		
		if(@CS_REST_JsonSerialiser::is_available()) {
			return new CS_REST_JsonSerialiser($log);
		} else if (@CS_REST_XmlSerialisation::is_available()) {
			return new CS_REST_XmlSerialisation($log);
		} else {
			trigger_error('No serialiser is available. Please ensure PECL/json or xml extensions are loaded',
				E_ERROR);
		}
	}
}

class CS_REST_JsonSerialiser {
	
	var $_log;
	
	function CS_REST_JsonSerialiser($log) {
		$this->_log = $log;
	}
	
	function get_content_type() {
		return 'application/json';
	}
	
	function get_format() {
		return 'json';
	}
    
    /**
     * Tests if this serialisation scheme is available on the current server
     * @return boolean False if the server doesn't support the serialisation scheme
     */
	function is_available() {
		return function_exists('json_decode') && function_exists('json_encode');
	}
	
	function serialise($data) {
		return json_encode($data);
	}
	
	function deserialise($text) {
		// Decode the string, returning an associative array instead of objects
		$data = json_decode($text, true);
		
		return $data == NULL ? $text : $data;
	}
}

define('CS_REST_CDATA_NAME', '__CDATA');
class CS_REST_XmlSerialiser {
	
	var $_log;
	
	function CS_REST_XmlSerialiser($log) {
		$this->_log = $log;
	}
	
	/**
	 * @access private
	 * @var int
	 */
	var $_level;
	
    /**
     * @access private
     * @var array
     */
	var $_elem_stack;
	
	function get_content_type() {
		return 'text/xml';
	}
	
	function get_format() {
		return 'xml';
	}
	
	/**
	 * Tests if this serialisation scheme is available on the current server
	 * @return boolean False if the server doesn't support the serialisation scheme
	 * @access public
	 */
	function is_available() {
		return function_exists('xml_parser_create');
	}
	
	/**
	 * Serialises the provided associative array into XML
	 * @param $data array
     * @access public
	 */
    function serialise($data, $pretty = false, $indent = '') {
    	if(!is_array($data)) { 
			trigger_error('Data to serialise must be an array', E_ERROR);
    	}

    	$xml = '';
    	foreach ($data as $k => $v) {
    		// If it's not an array then we just need to wrap and write
    		if(!is_array($v)) {
    			$xml .= $this->_prettify(
    			    '<'.$k.'>'.$this->_fix_encoding(htmlspecialchars($v)).'</'.$k.'>',
    			    $pretty, 
    			    $indent);
    		} else {
    			// If it's not an associative array then it's a list and we 
    			// need to re-use the name of the outer element for all 
    			// elements in the list
    			if(isset($v[0])) {
    				foreach($v as $_k => $_v) {
    					if(is_array($_v)) {
		                    $xml .= 
		                        $this->_prettify('<'.$k.'>', $pretty, $indent).
		                        $this->serialise($_v, $pretty, $indent.'\t').
		                        $this->_prettify('</'.$k.'>', $pretty, $indent);    						    
    					} else {
		                    $xml .= 
		                        $this->_prettify('<'.$k.'>', $pretty, $indent).
		                        $this->_fix_encoding(htmlspecialchars($_v)).
		                        $this->_prettify('</'.$k.'>', $pretty, $indent);    						
    					}
    				}    				
    			} else {
    				// Otherwise we can just wrap and recursively serialise the array
    				$xml .= 
    				    $this->_prettify('<'.$k.'>', $pretty, $indent).
    				    $this->serialise($v, $pretty, $indent."\t").
    				    $this->_prettify('</'.$k.'>', $pretty, $indent);
    			}
    		}
    	}
    	
    	return $xml;
    }
    
    /**
     * Re-codes data as UTF-8
     * @param $value
     * @access private
     */
    function _fix_encoding($value) {
    	if((function_exists('mb_detect_encoding') && 
            mb_detect_encoding($value) === 'UTF-8') &&
    	   (!function_exists('mb_check_encoding') ||
    	    mb_check_encoding($value, 'UTF-8'))) {
    	    	return $value;
    	}
    	
    	return utf8_encode($value);
    }
    
    /**
     * Prettifies the xml values if required
     * @param $line
     * @param $pretty
     * @param $indent
     * @access private
     */
    function _prettify($line, $pretty, $indent) {
    	if($pretty) {
    		$line = $indent.$line."\n";
    	} 
    	
    	return $line;
    }
    
    /**
     * Deserialises the provided XML into an associative array
     * @param $text string
     * @access public
     */
    function deserialise($text) {
    	$this->_log->log_message('Attempting to deserialise '.$text, get_class($this), CS_REST_LOG_VERBOSE);
    	$parser = xml_parser_create('utf-8');

    	xml_set_object($parser, $this);
    	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
    	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, true);
    	
    	xml_set_element_handler($parser, '_start_element', '_end_element');
    	xml_set_character_data_handler($parser, '_cdata');
    	
    	$this->_data = array();
    	$this->_elem_stack = array();
    	$this->_level = 0;
    	   	
    	if(!xml_parse($parser, $text, true)) {
    		$this->_log->log_message('Failed to parse xml', get_class($this), CS_REST_LOG_WARNING);
    		$error_code = xml_get_error_code($parser);
    		
    		// If it's just a plain string input then we want to return the string.
    		if($error_code === 5 && strlen(trim($text)) > 0) {
    			$this->_log->log_message('Invalid xml, returning text', get_class($this), CS_REST_LOG_WARNING);
    			return trim($text);
    		}
    		
    		trigger_error('Failed to parse xml with error '.$error_code.': '.xml_error_string($error_code), E_ERROR);
    	}
    	
    	xml_parser_free($parser);
    	
    	$data = $this->_fix_data($this->_elem_stack[0]);
    	$elem = $data['data'];
    	$this->_log->log_message('Checking for single list', get_class($this), CS_REST_LOG_VERBOSE);
    	while(is_array($elem)) {
    		if(isset($elem[0])) {
    			$this->_log->log_message('Found single list', get_class($this), CS_REST_LOG_VERBOSE);
    			return $elem;
    		} else if(count($elem) > 1) {
    			break;
    		}
    		
    		$elem = array_pop($elem);
    	}
    	
    	return array($data['name'] => $data['data']);
    }	
    
    /**
     * Start element handler for xml parsing
     * @access private
     */
    function _start_element($parser, $name, $attrs) {
    	// Just push the element on the stack and increment the level.
    	$this->_log->log_message('Got start element: '.$name, get_class($this), CS_REST_LOG_VERBOSE);
    	array_push($this->_elem_stack, 
    	    array(
    	        'name' => $name, 
    	        'level' => $this->_level,
    	        'data' => array()
    	    ));
    	$this->_level++;
    }
    
    /**
     * End element handler for xml parsing
     * @access private
     */
    function _end_element($parser, $name) {
    	$this->_log->log_message('Got end element: '.$name, get_class($this), CS_REST_LOG_VERBOSE);
    	$reverse_elems = array();
    	// Grab all the elements on the stack at this level
    	while(($elem = array_pop($this->_elem_stack)) && $elem['level'] == $this->_level)  {
    		array_push($reverse_elems, $elem);
    	}    	

    	// At this point $elem will be the parent. i.e the top most element on the 
    	// stack from the next level. 
    	// We need to push all the child elements into it's data array
    	while($child = array_pop($reverse_elems)) {
    		$child = $this->_fix_data($child);
    		
    		// If we've already got an element of that name then create an array
    		if(isset($elem['data'][$child['name']])) {
    			$this->_log->log_message('Element already exists in parent.', get_class($this), CS_REST_LOG_VERBOSE);
    			// If the current array is already indexed numerically then just push the new element
    			if(isset($elem['data'][$child['name']][0])) {
    			    $this->_log->log_message('Existing element is already a list, appending.', 
    			        get_class($this), CS_REST_LOG_VERBOSE);
    				array_push($elem['data'][$child['name']], $child['data']);
    			// Otherwise we want to create a numerically indexed array for the list
    			} else {
    			    $this->_log->log_message('Existing element is a normal node, creating list.', 
    			        get_class($this), CS_REST_LOG_VERBOSE);
    				$elem['data'][$child['name']] = array(
    				    $elem['data'][$child['name']],
    				    $child['data']
    				);
    			}
    		} else {
    		    $elem['data'][$child['name']] = $child['data'];
    		}
    	}
    	
    	// Make sure we push the parent element back onto the stack
    	array_push($this->_elem_stack, $elem);
    	$this->_level--;
    }
    
    /**
     * Removes the array for text nodes
     * @param array $elem
     * @access private
     */
    function _fix_data($elem) {
        if(is_array($elem['data']) &&
           count($elem['data']) === 1 && 
           isset($elem['data'][CS_REST_CDATA_NAME])) {
            $elem['data'] = $elem['data'][CS_REST_CDATA_NAME];
        }    

        return $elem;
    }
    
    
    /**
     * CDATA handler for xml parsing
     * @access private
     */
    function _cdata($parser, $data) {
    	$this->_log->log_message('Got cdata: '.$data, get_class($this), CS_REST_LOG_VERBOSE);
    	if(strlen(trim($data)) > 0) {
    		$last = array_pop($this->_elem_stack);
    		
    		if($last['name'] === CS_REST_CDATA_NAME) {
    			$last['data'] .= $data;
    			array_push($this->_elem_stack, $last);
    		} else {    		
    			array_push($this->_elem_stack, $last);
		    	array_push($this->_elem_stack, array(
		    	    'name' => CS_REST_CDATA_NAME,
		    	    'level' => $this->_level,
		    	    'data' => $data));
    		}
    	}
    }
}