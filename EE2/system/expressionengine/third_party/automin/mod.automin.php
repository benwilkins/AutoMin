<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('libraries/class.minify_css_compressor.php');
include('libraries/class.jsmin.php');
include('libraries/lessphp/lessc.inc.php');
					
/**
 * AutoMin Class
 * @package default
 */
class Automin {
	
	/**
	 * Path to the document root - set in constructor
	 * @var string
	 */
	var $strDocumentRootPath;
	
	/**
	 * Holds AutoMin Preferences
	 * @var array
	 */
	var $arrPreferences = array();
	
	// ---------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Automin() {
		
		// Set default AutoMin Preferences
		$this->strDocumentRootPath = $_SERVER['DOCUMENT_ROOT'] . '/';
		$this->arrPreferences['cache_server_path'] = "{$_SERVER['DOCUMENT_ROOT']}/automin/";
		$this->arrPreferences['cache_url'] = '/automin/';
		
		// Get EE object
		$this->EE =& get_instance();
		
		// Load libraries
		$this->EE->load->library('automin_model');
		
		// Load preferences
		$this->arrPreferences = $this->EE->automin_model->GetAutoMinPreferences();
		
		$this->_write_to_template_log('AutoMin Module Loaded');

	}

	// --------------------------------------------------------------------

	/**
	 * Minifies LESS
	 * @return void
	 */
	function less() {
		
		$this->_write_to_template_log('Processing LESS');
		
		$strTags = $this->_get_ee_tagdata();
		
		if ($this->_preference_value_for_key('automin_enabled') == 'y') {
			
			if (!$this->_check_for_required_libraries()) {
				$this->_write_to_template_log('AutoMin dependencies are missing. Make sure you installed them correctly. The original tag data is being returned for safety.');
				return $strTags;
			}
			
			$strSource = $this->_process_tag_data($strTags, 'less');
			
			if (!empty($this->strCacheFilename) && $strSource) {
				
				$this->_write_to_template_log('AutoMin was successful. Writing new tags to output.');
				return sprintf('<link href="%s" %s>', $this->strCacheFilename, $this->_fetch_tag_parameters());
				
			} else {
				
				$this->_write_to_template_log('An error occurred. Your LESS code has not been compiled.');
				return $strTags;
				
			}
			
		} else {
			
			$this->_write_to_template_log('AutoMin not enabled. Returning original tag contents.');
			return $strTags;
			
		}
		
	}
	
	/**
	 * Minifies CSS and Code
	 * @return void
	 */
	function css() {
		
		$this->_write_to_template_log('Processing CSS');
		
		$strTags = $this->_get_ee_tagdata();
		
		if ($this->_preference_value_for_key('automin_enabled') == 'y') {
			
			if (!$this->_check_for_required_libraries()) {
				$this->_write_to_template_log('AutoMin dependencies are missing. Make sure you installed them correctly. The original tag data is being returned for safety.');
				return $strTags;
			}
			
			$strSource = $this->_process_tag_data($strTags, 'css');
			
			if (!empty($this->strCacheFilename) && $strSource) {
				
				$this->_write_to_template_log('AutoMin was successful. Writing new tags to output.');
				return sprintf('<link href="%s" %s>', $this->strCacheFilename, $this->_fetch_tag_parameters());
				
			} else {
				
				$this->_write_to_template_log('An error occurred. Returning original tags for safety.');
				return $strTags;
				
			}
			
		} else {
			
			$this->_write_to_template_log('AutoMin not enabled. Returning original tag contents.');
			return $strTags;
			
		}
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Minifies JS
	 * @return void
	 */
	function js() {
		
		$this->_write_to_template_log('Processing JavaScript');
		
		$strTags = $this->_get_ee_tagdata();
		
		if ($this->_preference_value_for_key('automin_enabled') == 'y') {
			
			if (!$this->_check_for_required_libraries()) {
				$this->_write_to_template_log('AutoMin dependencies are missing. Make sure you installed them correctly. The original tag data is being returned for safety.');
				return $strTags;
			}
			
			$strSource = $this->_process_tag_data($strTags, 'js');
			
			if (!empty($this->strCacheFilename) && $strSource) {
				
				$this->_write_to_template_log('AutoMin was successful. Writing new tags to output.');
				return sprintf('<script src="%s" %s></script>', $this->strCacheFilename, $this->_fetch_tag_parameters());
				
			} else {
				
				$this->_write_to_template_log('An error occurred. Returning original tags for safety.');
				return $strTags;
				
			}
			
		} else {
			
			$this->_write_to_template_log('AutoMin not enabled. Returning original tag contents.');
			return $strTags;
			
		}
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Checks for the required libraries. Useful when debugging installations
	 *
	 * @return void
	 * @author Jesse Bunch
	 */
	function _check_for_required_libraries() {
		
		$this->_write_to_template_log('Checking for required libraries');
		
		// CSS Minifier
		if (!class_exists('Minify_CSS_Compressor')) {
			$this->_write_to_template_log('Minify_CSS_Compressor class was not loaded correctly.');
			return FALSE;
		}
		
		// JS Minifier
		if (!class_exists('JSMin')) {
			$this->_write_to_template_log('JSMin class was not loaded correctly.');
			return FALSE;
		}

		// LESS Parser
		if (!class_exists('lessc')) {
			$this->_write_to_template_log('The LESS compiler class was not loaded correctly.');
			return FALSE;
		}
		
		return TRUE;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Loops through the current tag's parameters and creates a string to use
	 * in the output of the final CSS or JS tag. For example: {exp:automin:css type="text/css" title="default"}
	 * would output: <link href="%s" type="text/css" title="default">
	 * @return void
	 */
	function _fetch_tag_parameters() {
		
		$this->_write_to_template_log('Featching AutoMin tag parameters');
		
		$arrTemplateParams = $this->_get_ee_tag_params();
		
		// Unset any parameters that shouldn't be sent to the
		// final CSS / JS include HTML tag here
		
		$arrFinalParams = array();
		foreach($arrTemplateParams as $key => $value)
			$arrFinalParams[] = sprintf('%s="%s"', $key, $value);
		
		return implode(' ', $arrFinalParams);
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Central method that does the processing
	 * @param string $strTags The tagdata from EE
	 * @param string $strType The type of tagdata: 'css' or 'js'
	 * @param boolean $boolReturnSource If TRUE, will return the minfied source instead of the file path
	 * @return string
	 */
	function _process_tag_data($strTags, $strType) {
		
		$this->_write_to_template_log('Processing EE tag data');
		
		// -------------------------------------
		//  Pre-Process EE Global Vars
		// -------------------------------------
		
		$strTags = $this->EE->TMPL->parse_globals($strTags);
		
		// -------------------------------------
		//  Generate tag data hash - this will be our cache filename
		// -------------------------------------
		
		// Trim and strip any uneccessary white space from our tags
		// This is so we don't get a false positive from a space or tab
		// when we check to see if anything has changed.
		$strTags = trim($strTags);
		$strTags = str_replace(array("\n", "\r", "\t"), '', $strTags);
		
		// Generate a hash so we know if something has changed
		$strTagHash = md5($strTags);
		
		// Add the extension (js or css)
		$strFileType = $strType;
		if ($strType == 'less') {
			$strFileType = 'css';
		}
		$strCacheFilename = $strTagHash . ".$strFileType";
		
		// Construct the filepath to the cache
		$strCacheFilePath = $this->_preference_value_for_key('cache_server_path') . $strCacheFilename;
		$this->strCacheFilename = $this->_preference_value_for_key('cache_url') . $strCacheFilename;
		
		// -------------------------------------
		//  Extract the file names
		// -------------------------------------
		
		$arrFilenames = $this->_extract_files_names_from_tag_data($strTags, $strType);
		
		if (count($arrFilenames) == 0) {
			
			$this->_write_to_template_log('ERROR: No file names were matched in the tags.');
			return FALSE;
			
		}
		
		$intLatestModified = $this->_get_latest_modified_timestamp_for_files_array($arrFilenames);
		
		// -------------------------------------
		//  Is the cache valid?
		// -------------------------------------

		if (!$this->_is_cache_valid($strCacheFilePath, $intLatestModified)) {

			$this->_write_to_template_log('Cache isn\'t valid. Regenerating...');
			
			$strData = $this->_regenerate_cache_from_files($arrFilenames, $strType);
			
			if ($strData) {
				
				$this->_write_to_template_log('Writing source to cache');
				
				if (!$this->_write_string_to_file($strData, $strCacheFilePath)) {
					$this->_write_to_template_log('An error occurred while trying to save the compiled source.');
					return FALSE;
				} else {

					$this->strCacheFilename .= '?modified=' . $intLatestModified;
					return TRUE;
				}

			} else {
			
				$this->_write_to_template_log('ERROR: Invalid data returned from cache generator');
				return FALSE;
				
			}
			
			return $strData;
			
		} else {
			
			// Append the latest modified time
			$this->strCacheFilename .= '?modified=' . $intLatestModified;
			$this->_write_to_template_log('Valid cache found. Returning cached data.');
			return TRUE;
		
		}
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Extracts filenames from an HTML string. Prepends the path to the front-end
	 * controller to each path and removes double slashes.
	 * @param string $strTagData 
	 * @param string $strType 
	 * @return array
	 */
	function _extract_files_names_from_tag_data($strTagData, $strType) {
		
		$this->_write_to_template_log('Attempting to extract file names from tag data');
		
		$arrMatches = array();
		
		switch($strType) {
		
			case 'less':
				
				preg_match_all("/href\=\"([A-Za-z0-9\.\/\_\-\?\=\:]+.less)\"/", $strTagData, $arrMatches);
				
			break;

			case 'css':
				
				preg_match_all("/href\=\"([A-Za-z0-9\.\/\_\-\?\=\:]+.css)\"/", $strTagData, $arrMatches);
				
			break;
			
			// ------
			
			case 'js':
				
				preg_match_all("/src\=\"([A-Za-z0-9\.\/\_\-\?\=\:]+.js)\"/", $strTagData, $arrMatches);
			
			break;
			
			// ------
			
			default:
			
				return FALSE;
				
			break;
		
		}
		
		$this->_prep_filenames($arrMatches[1]);
		
		$this->_write_to_template_log(sprintf('Found %s filenames', count($arrMatches[1])));
		
		return $arrMatches[1];
		
	}	
	
	// ---------------------------------------------------------------------
	
	/**
	 * Converts document root relative file names to server root relative file names
	 *
	 * @param array &$arrFileNames 
	 * @return void
	 * @author Jesse Bunch
	 */
	function _prep_filenames(&$arrFileNames) {
		
		$this->_write_to_template_log('Creating server root relative filenames');
		
		$strURL = $this->_get_current_url();
		
		foreach($arrFileNames as &$strFileName) {
			
			// For now, we don't support full URLs, so strip them down to the doc root
			$strFileName = str_replace($strURL, '', $strFileName);
		
			// Make the URL doc-root relative
			$strFileName = str_replace('//', '/', $this->strDocumentRootPath . $strFileName);
		
		}
		
		// var_dump($arrFileNames);
		// exit;
		
	}
	
	// ---------------------------------------------------------------------	
	
	/**
	 * Parses the css @imports in a string
	 *
	 * @param string $strFileData 
	 * @return string The new file data with @imports parsed
	 * @author Jesse Bunch
	 */
	function _process_css_imports_in_string($strFileData) {
		
		$this->_write_to_template_log('Looking for CSS @imports');
		
		$arrMatches = array();
		preg_match_all('/\@import\s[url\(]?[\'\"]{1}([A-Za-z0-9\.\/\_\-]+)[\'\"]{1}[\)]?[;]?/', $strFileData, $arrMatches);
		
		$arrLines = $arrMatches[0];
		$arrFileNames = $arrMatches[1];
		$intCount = 0;
		
		if (count($arrFileNames)) {
			
			$this->_write_to_template_log('Found @imports. Processing...');
			
			$this->_prep_filenames($arrFileNames);

			foreach($arrFileNames as $strFilename) {

				// Read the file
				$strData = $this->_read_file($strFilename);

				// If we have data, replace the @import
				if ($strData) {
					$strFileData = str_replace($arrLines[$intCount], $strData, $strFileData);
				}

				$intCount++;

			}
			
		} else {
			
			$this->_write_to_template_log('No @imports were found');
			
		}
		
		return $strFileData;

	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * From an array of filenames, gets the timestamp of the most recently edited file.
	 * @param array $arrFilenames 
	 * @return int
	 */
	function _get_latest_modified_timestamp_for_files_array($arrFilenames) {
		
		$this->_write_to_template_log('Getting the most recently modified file\'s modification timestamp.');
		
		$intLastModified = 0;
		
		foreach($arrFilenames as $strFilename) {
		
			$intModified = @filemtime($strFilename);
			
			if ($intModified !== FALSE) {
			
				if ($intModified > $intLastModified) {
					
					$intLastModified = $intModified;
				
				}
				
			}
		
		}
		
		return $intLastModified;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Writes the given data to the cache directory using the filename provided.
	 * If the directory doesn't exist, this function attempts to create it with 777 permissions.
	 * @param string $strData 
	 * @param string $strFilename 
	 * @return boolean
	 */
	function _write_string_to_file($strData, $strFilePath) {
		
		$this->_write_to_template_log(sprintf('Attempting to write to file: %s', $strFilePath));
		
		if (empty($strData)) {
			$this->_write_to_template_log('ERROR: No data to write');
			return FALSE;
		}
		
		if (!is_dir($this->_preference_value_for_key('cache_server_path'))) {
			
			$this->_write_to_template_log('Attempting to create cache directory');
			
			if (!@mkdir($this->_preference_value_for_key('cache_server_path'), 0777)) {
				
				$this->_write_to_template_log('Unable to create the cache directory');
				return FALSE;
				
			}
			
			$this->_write_to_template_log('Cache directory created successfully. Making it writable.');
			@chmod($this->_preference_value_for_key('cache_server_path'), 0777);
			
		}
		
		if (file_put_contents($strFilePath, $strData, LOCK_EX) === FALSE) {
			
			$this->_write_to_template_log('Unable to write to file.');
			return FALSE;
			
		} else {
			
			$this->_write_to_template_log('File write was successful. Setting permissions just in case.');
			@chmod($strFilePath, 0777);
			
			return TRUE;
			
		}

	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Determines if the cache filename provided eists and is writable. Further,
	 * the function checks the modified time of the cache file to see if it is
	 * later than the unix timestamp provided.
	 * @param string $strCacheFilePath Full server path to the cache file
	 * @param string $intLatestModified UNIX Timestamp. To return TRUE, the cache file's modified
	 * 									timestamp must be after ths value to indicate it's not out of date.
	 * @return boolean`
	 */
	function _is_cache_valid($strCacheFilePath, $intLatestModified) {
		
		// Caching disabled?
		if ($this->_preference_value_for_key('cache_enabled') == 'n') {
			$this->_write_to_template_log('Caching disabled in preferences. Therefore cache is invalid.');
			return FALSE;
		}
		
		if (!@is_dir($this->_preference_value_for_key('cache_server_path'))) {
			
			$this->_write_to_template_log('ERROR: Cache directory doesn\'t exist. Therefore the cache is invalid.');
			
			return FALSE;
			
		} else {
			
			if (!@is_readable($strCacheFilePath)) {
				
				$this->_write_to_template_log('ERROR: The cache file isn\'t readable');
			
				return FALSE;
				
			} else {
				
				$this->_write_to_template_log('Cache file found and is readable. Validating...');
				
				$intCacheModified = @filemtime($strCacheFilePath);
				
				if ($intCacheModified !== FALSE && $intCacheModified >= $intLatestModified) {
					
					$this->_write_to_template_log('Cache is newer than the latest file modification. All is good.');
					return TRUE;
					
				} else {
					
					$this->_write_to_template_log('Cache is out of date.');
					return FALSE;
					
				}
				
			}
			
		}		
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Reads the specified file into memory
	 * @param string $strFilename 
	 * @return string
	 */
	public function _read_file($strFilename) {
		
		$this->_write_to_template_log(sprintf('Attempting to read file: %s', $strFilename));
		return @file_get_contents($strFilename);
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Reads and combines the files in the provided order
	 * @param string $arrFilenames 
	 * @param string $strType 
	 * @return string
	 */
	public function _combine_data_in_files($arrFilenames, $strType) {
		
		$this->_write_to_template_log(sprintf('Attempting to combine %s files', count($arrFilenames)));
		
		$strReturnData = '';
		
		foreach($arrFilenames as $strFilename) {
		
			$strFileData = $this->_read_file($strFilename);
			
			if ($strFileData) {
			
				$strReturnData .= $strFileData . "\n\n";
				
			} else {
				
				$this->_write_to_template_log('File was empty.');
				
			}
		
		}
		
		// if CSS, we need to check for @imports
		if ($strType == 'css') {
			$strReturnData = $this->_process_css_imports_in_string($strReturnData);
		}
		
		return $strReturnData;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Regenerates the cache from the array of filenames provided
	 * @param string $arrFilenames 
	 * @param string $strType Either 'css' or 'js' - Determines the minifier used
	 * @return string
	 */
	public function _regenerate_cache_from_files($arrFilenames, $strType) {
		
		$this->_write_to_template_log('Attempting to regenerate the cache');
		
		@ini_set("memory_limit","12M");
		@ini_set("memory_limit","16M");
		@ini_set("memory_limit","32M");
		@ini_set("memory_limit","64M");
		@ini_set("memory_limit","128M");
		@ini_set("memory_limit","256M");
		
		$strDataToReturn = '';
		$strDataToReturn = $this->_combine_data_in_files($arrFilenames, $strType);
		
		if ($strDataToReturn === FALSE) {
			
			$this->_write_to_template_log('ERROR: No data was returned after combining the files.');
			return FALSE;
			
		}
		
		switch($strType) {

			case 'less':
			
				$this->_write_to_template_log('Compiling LESS');
				$intOldSize = strlen($strDataToReturn);
				$objLess = new lessc();

				try {
					$strDataToReturn = $objLess->parse($strDataToReturn);		
				} catch (Exception $e) {
					$this->_write_to_template_log('LESS compilation failed with error: ' . $e->getMessage());
					return FALSE;
				}
				
				$this->_write_to_template_log('Compressing CSS returned from LESS compiler');
				$strDataToReturn = Minify_CSS_Compressor::process($strDataToReturn);

				$intNewSize = strlen($strDataToReturn);
				
				$this->_write_to_template_log(sprintf('Compilation has finished. %s bytes became %s bytes or a %s%% savings', $intOldSize, $intNewSize, (($intNewSize/$intOldSize) * 100)));
				
				return $strDataToReturn;
			
			break;

			// -------
		
			case 'css':
			
				$this->_write_to_template_log('Compressing CSS');
				
				$intOldSize = strlen($strDataToReturn);
				$strDataToReturn = Minify_CSS_Compressor::process($strDataToReturn);
				$intNewSize = strlen($strDataToReturn);
				
				$this->_write_to_template_log(sprintf('Compression finished. %s bytes became %s bytes or a %s%% savings', $intOldSize, $intNewSize, (($intNewSize/$intOldSize) * 100)));
				
				return $strDataToReturn;
			
			break;
			
			// ------
			
			case 'js':
				
				$this->_write_to_template_log('Compressing JavaScript');
				
				$intOldSize = strlen($strDataToReturn);
				$strDataToReturn = JSMin::minify($strDataToReturn);
				$intNewSize = strlen($strDataToReturn);
				
				$this->_write_to_template_log(sprintf('Compression finished. %s bytes became %s bytes or a %s%% savings', $intOldSize, $intNewSize, (($intNewSize/$intOldSize) * 100)));
				
				return $strDataToReturn;
			
			break;
			
			// ------
			
			default:
			
				$this->_write_to_template_log('ERROR: Invalid compression type specified.');
				return FALSE;
				
			break;
		
		}
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Returns the template tag data
	 *
	 * @return string
	 * @author Jesse Bunch
	 */
	function _get_ee_tagdata() {
		
		$this->_write_to_template_log('Attempting to fetch EE tagdata');
		
		$strTags = $this->EE->TMPL->tagdata;
		
		return html_entity_decode($strTags);
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Returns the template tag params
	 *
	 * @return array
	 * @author Jesse Bunch
	 */
	function _get_ee_tag_params() {
		
		$this->_write_to_template_log('Fetching EE tag params.');
		
		$arrTags = array();
		
		$arrTags = $this->EE->TMPL->tagparams;
		
		return $arrTags;
		
	}
	
	// ---------------------------------------------------------------------
	
	function _get_current_url($boolIncludeURI = FALSE) {
			
		$strURL = 'http';

		if (!empty($_SERVER['HTTPS'])) {
			$strURL .= "s";
		}
		
		$strURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80") {
			
			$strURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
			
		} else {
			
			$strURL .= $_SERVER["SERVER_NAME"];
			
		}
		
		if ($boolIncludeURI) {
			return $strURL.$_SERVER["REQUEST_URI"];
		} else {
			return $strURL;
		}
		
		
	}

	// ---------------------------------------------------------------------

	/**
	 * Retrieves an AutoMin preference, checking for an override in the
	 * user's configuration file.
	 * @param $strKey string
	 * @return string
	 * @author Jesse Bunch
	*/
	function _preference_value_for_key($strKey) {
		
		$strReturnValue = FALSE;

		if (isset($this->arrPreferences[$strKey])) {
			$strReturnValue = $this->arrPreferences[$strKey];			
		}
		
		$CI =& get_instance();
		$strConfigKey = 'automin_' . $strKey;
		if ($CI->config->item($strConfigKey)) {
			$strReturnValue = $CI->config->item($strConfigKey);
		}

		return $strReturnValue;

	}
	

	// ---------------------------------------------------------------------

	/**
	 * Helper funciton for template logging
	 */	
	function _write_to_template_log($strMessage) {
		
		$this->EE->TMPL->log_item("AutoMin Module: $strMessage");	
			
	}
	
	// ---------------------------------------------------------------------
	
}

/* End of file mod.automin.php */ 
/* Location: ./system/expressionengine/third_party/automin/mod.automin.php */