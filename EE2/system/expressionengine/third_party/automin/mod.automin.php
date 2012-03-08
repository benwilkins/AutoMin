<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * AutoMin Module Front End File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Jesse Bunch
 * @link		http://getbunch.com/
 */

class Automin {
	
	public $return_data;

	const CODE_TYPE_JS = 'js';
	const CODE_TYPE_CSS = 'css';
	
	/**
	 * Constructor
	 * @author Jesse Bunch
	*/
	public function __construct() {
		$this->EE =& get_instance();
		$this->EE->load->model('automin_model');
		$this->EE->load->library('minification_library');
		$this->version = $this->EE->automin_model->version;
	}

	/**
	 * exp:automin:js
	 * Combines and minifies any script tags or inline JS
	 * @return string
	 * @author Jesse Bunch
	*/
	public function js() {
		
		// Get markup
		$markup_string = $this->EE->TMPL->tagdata;
		$compressed_string = '';
		
		// Parse the HTML DOM
		$dom_document = new DOMDocument();
		$dom_document->loadHTML($markup_string);

		// Get all <script> elements
		$node_results_script = $dom_document->getElementsByTagName('script');
		
		// Hold our final string
		$final_string = '';
		
		// Process each node
		foreach ($node_results_script as $node) {
			
			// Inline JS or a Path?
			if ($node->getAttribute('src')) {
				
				// Normalize the file path
				$file_path_root = $this->_normalize_file_path($node->getAttribute('src'), '', TRUE);

				// Extract the file contents
				$final_string .= $this->_read_file($file_path_root);
				
			} else {
				
				// Get node value
				$final_string .= $node->nodeValue;

			}

		}

		// Minify output
		$final_string = $this->EE->minification_library->minify_js_string($final_string);
		return $this->_format_output($final_string, self::CODE_TYPE_JS);

	}

	/**
	 * exp:automin:css
	 * Combines and minifies any included stylesheet or inline CSS
	 * @return string
	 * @author Jesse Bunch
	*/
	public function css() {
		
		// Get markup
		$markup_string = $this->EE->TMPL->tagdata;
		$compressed_string = '';
		
		// Parse the HTML DOM
		$dom_document = new DOMDocument();
		$dom_document->loadHTML($markup_string);

		// Get all relevant elements
		$node_results_link = $dom_document->getElementsByTagName('link');
		$node_results_style = $dom_document->getElementsByTagName('style');

		// Hold our nodes in order
		$node_results = array();

		// Add nodes by line number
		foreach ($node_results_link as $node_result)
			$node_results[$node_result->getLineNo()] = $node_result;
		foreach ($node_results_style as $node_result)
			$node_results[$node_result->getLineNo()] = $node_result;

		// Sort by line number
		ksort($node_results);
		
		// Hold our final string
		$final_string = '';
		
		// Process each node
		foreach ($node_results as $node) {
			
			// <link> element?
			if ($node->tagName == 'link') {

				// Normalize the file path
				$file_path_relative = $this->_normalize_file_path($node->getAttribute('href'));
				$file_path_root = $this->_normalize_file_path($node->getAttribute('href'), '', TRUE);

				// Extract the file contents
				$file_contents = $this->_read_file($file_path_root);

				// Process @imports
				$file_contents = $this->_parse_css_imports($file_contents, $file_path_relative);

				// Is LESS?
				if ($node->getAttribute('rel') == 'stylesheet/less') {
					$file_contents = $this->EE->minification_library->compile_less_string($file_contents);
				}

				// Compress CSS
				$final_string .= $this->EE->minification_library->minify_css_string($file_contents);

			}

			// <style> element?
			else if ($node->tagName == 'style') {

				$file_contents = $node->nodeValue;

				// Process @imports
				$file_contents = $this->_parse_css_imports($file_contents);

				// Is LESS?
				if ($node->getAttribute('type') == 'text/less') {
					$file_contents = $this->EE->minification_library->compile_less_string($file_contents);
				}

				// Compress CSS
				$final_string .= $this->EE->minification_library->minify_css_string($file_contents);

			}

		}

		// Minify output
		$final_string = $this->EE->minification_library->minify_css_string($final_string);
		return $this->_format_output($final_string, self::CODE_TYPE_CSS);

	}

	private function _format_output($output_string, $type) {

		$inline_output = $this->EE->TMPL->fetch_param('inline');

		if ($type == self::CODE_TYPE_CSS) {
			
			if ($inline_output) {
				return sprintf('<style type="text/css">%s</style>', $output_string);
			} else {
				return sprintf('<link href="%s" rel="stylesheet/less">', '/path/to/file.js');
			}

		} else if ($type == self::CODE_TYPE_JS) {

			if ($inline_output) {
				return sprintf('<script type="text/javascript">%s</script>', $output_string);
			} else {
				return sprintf('<script src="%s" type="text/javascript">', '/path/to/file.js');
			}

		}

	}

	/**
	 * File paths may be in different formats. This function will take
	 * any file path and normalize it to on of two formats depending on the
	 * parameters you pass.
	 * @param string $file_path The path to normalize.
	 * @param string $relative_path If $file_path is a relative path, we need
	 * the path to the relative file. If no path is supplied, the dirname of 
	 * the current URI is used.
	 * @param bool $include_root If TRUE, the full server path is returned. If
	 * FALSE, the path returned is relative to the document root.
	 * @return string
	 * @author Jesse Bunch
	*/
	private function _normalize_file_path($file_path, $relative_path='', $include_root = FALSE) {
		
		// Get the relative path
		if (!$relative_path) {
			$relative_path = $_SERVER['REQUEST_URI'];
		}

		// Relative path should leave out the document root
		$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $relative_path);

		// Parse the path
		$path_parts = pathinfo($relative_path);
		$dirname = $path_parts['dirname'].'/';

		// If not document-root relative, we must add the URI
		// of the calling page to make it document-root relative
		if (substr($file_path, 0, 1) != '/') {
			$file_path = $dirname.$file_path;
		}
	
		// Include full root path?
		if ($include_root) {
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path;
		}

		return $this->EE->functions->remove_double_slashes($file_path);

	}
	
	/**
	 * Reads a file from the filesystem
	 * @param string $file_path Full server path to the file to read
	 * @return string
	 * @author Jesse Bunch
	*/
	private function _read_file($file_path) {
		
		return @file_get_contents($file_path);

	}

	/**
	 * Looks for and parses @imports in the provided string.
	 * @param string $string
	 * @param string $relative_path Passed to _normalize_file_path(). See
	 * that function's documentation for details on this param.
	 * @return string
	 * @author Jesse Bunch
	*/
	function _parse_css_imports($string, $relative_path = '') {
		
		// Get all @imports
		$matches = array();
		preg_match_all('/\@import\s[url\(]?[\'\"]{1}([A-Za-z0-9\.\/\_\-]+)[\'\"]{1}[\)]?[;]?/', $string, $matches);
		$matched_lines = $matches[0];
		$matched_filenames = $matches[1];
		$count = 0;

		// Iterate and parse
		foreach($matched_filenames as $filename) {

			$filename = $this->_normalize_file_path($filename, $relative_path, TRUE);

			// Read the file
			$file_data = $this->_read_file($filename);

			// If we have data, replace the @import
			if ($file_data) {
				$string = str_replace($matched_lines[$count], $file_data, $string);
			}

			$count++;

		}

		return $string;

	}

}
/* End of file mod.automin.php */
/* Location: /system/expressionengine/third_party/automin/mod.automin.php */