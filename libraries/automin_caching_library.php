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

class Automin_caching_library {

	private $EE;


	public function __construct() {
		$this->EE = &get_instance();
		$this->EE->load->model('automin_model');
	}
	
	/**
	 * Returns the filename of the cache file for the provided
	 * cache value, if it exists and is readable.
	 * @param string $cache_value
	 * @param integer $timestamp
	 * @author Jesse Bunch
	*/
	public function fetch_cache($cache_key, $cache_value, $timestamp) {

		// Is caching disabled?
		if (!$this->EE->automin_model->is_caching_enabled()) {
			return FALSE;
		}

		$cache_file_path = $this->get_cache_file_path($cache_key);

		if (!file_exists($cache_file_path)) {
			return FALSE;
		}

		if (!is_readable($cache_file_path)) {
			return FALSE;
		}

		$last_modified = @filemtime($cache_file_path);
		if (!$last_modified
			OR $last_modified < $timestamp) {
			return FALSE;
		}

		return $this->_get_cache_url_path($cache_key);

	}

	/**
	 * Writes the provided cache value and returns the filename of
	 * the cache file.
	 * @param string $cache_key A hash of
	 * @return mixed FALSE if failure, string if success
	 * @author Jesse Bunch
	*/
	public function write_cache($cache_key, $cache_value) {

		// Is caching disabled?
		if (!$this->EE->automin_model->is_caching_enabled()) {
			return FALSE;
		}
		
		$cache_file_path = $this->get_cache_file_path($cache_key);

		if (FALSE === file_put_contents($cache_file_path, $cache_value)) {
			return FALSE;
		}

		return $this->_get_cache_url_path($cache_key);

	}
	
	/**
	 * Retruns the cache key for the given cache value
	 * @param string $cache_value
	 * @param string $extension
	 * @return string
	 * @author Jesse Bunch
	*/
	public function get_cache_key($cache_value, $extension) {
		return md5($cache_value).".$extension";
	}

	/**
	 * Returns the full server path to the cache file, should it exist.
	 * Does not check if the file exists.
	 * @param string $cache_key
	 * @return string
	 * @author Jesse Bunch
	*/
	private function get_cache_file_path($cache_key) {
		$cache_path = $this->EE->automin_model->get_cache_path();

		if (version_compare(APP_VER, '2.6', '>=')) {
			$this->EE->load->helper('string');
			$sFixedslashes = reduce_double_slashes("$cache_path/$cache_key");
		} else {
			$sFixedslashes = $this->EE->functions->remove_double_slashes("$cache_path/$cache_key");
		}

		return $sFixedslashes;
	}

	/**
	 * Returns the URL to the cache file, should it exist
	 * @param string $cache_key
	 * @return string
	 * @author Jesse Bunch
	*/
	private function _get_cache_url_path($cache_key) {
		$cache_path = $this->EE->automin_model->get_cache_url();
		
		if (version_compare(APP_VER, '2.6', '>=')) {
			$this->EE->load->helper('string');
			$sFixedslashes = reduce_double_slashes("$cache_path/$cache_key");
		} else {
			$sFixedslashes = $this->EE->functions->remove_double_slashes("$cache_path/$cache_key");
		}
		
		return $sFixedslashes;
	}


}

/* End of file caching_library.php */
/* Location: /system/expressionengine/third_party/automin/libraries/caching_library.php */
