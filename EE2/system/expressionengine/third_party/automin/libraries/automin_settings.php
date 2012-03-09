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

class Automin_settings {
	
	/**
	 * Holds our EE instance
	 * @var EE
	 * @author Jesse Bunch
	*/
	private $EE;

	/**
	 * Holds our settings
	 * @var array
	 * @author Jesse Bunch
	*/
	private $_settings_array;

	/**
	 * Constructor
	 * @return void
	 * @author Jesse Bunch
	*/
	public function __construct() {
		$this->EE = &get_instance();
	}

	/**
	 * Is AutoMin enabled?
	 * @param mixed $value
	 * @return mixed See _get_or_set_value_by_key
	 * @author Jesse Bunch
	*/
	public function automin_enabled($value = NULL) {
		return $this->_get_or_set_value_by_key('automin_enabled', $value);
	}

	/**
	 * Is caching enabled?
	 * @param mixed $value
	 * @return mixed See _get_or_set_value_by_key
	 * @author Jesse Bunch
	*/
	public function caching_enabled($value = NULL) {
		return $this->_get_or_set_value_by_key('cache_enabled', $value);
	}

	/**
	 * Should we compress HTML?
	 * @param mixed $value
	 * @return mixed See _get_or_set_value_by_key
	 * @author Jesse Bunch
	*/
	public function compress_html($value = NULL) {
		return $this->_get_or_set_value_by_key('compress_markup', $value);
	}

	/**
	 * Get the path to the cache directory
	 * @param mixed $value
	 * @return mixed See _get_or_set_value_by_key
	 * @author Jesse Bunch
	*/
	public function cache_path($value = NULL) {
		return $this->_get_or_set_value_by_key('cache_server_path', $value);
	}

	/**
	 * Get the URL to the cache directory
	 * @param mixed $value
	 * @return mixed See _get_or_set_value_by_key
	 * @author Jesse Bunch
	*/
	public function cache_url($value = NULL) {
		return $this->_get_or_set_value_by_key('cache_url', $value);
	}

	/**
	 * Gets or sets a value in the settings array
	 * @param mixed $value
	 * @return mixed FALSE if failed. TRUE if success. Value if get.
	 * @author Jesse Bunch
	*/
	private function _get_or_set_value_by_key($key, $value = NULL) {
		
		if (array_key_exists($key, $this->_settings_array)) {
			
			if (!is_null($value)) {
				$this->_settings_array[$key] = $value;
				return TRUE;
			} else {
				return $this->_settings_array[$key];
			}

		}

		return FALSE;

	}




}

/* End of file automin_settings.php */
/* Location: /system/expressionengine/third_party/automin/libraries/automin_settings.php */