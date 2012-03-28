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

class Automin_model {

	/**
	 * Holds our EE instance
	 * @var object
	 * @author Jesse Bunch
	*/
	private $EE;

	/**
	 * Constructor
	 * @return void
	 * @author Jesse Bunch
	*/
	public function __construct() {
		$this->EE = &get_instance();
	}

	/**
	 * Returns the AutoMin cache path for the current site
	 * @author Jesse Bunch
	 * @return string
	*/
	public function get_cache_path() {
		$settings_array = $this->get_settings();
		return $settings_array['cache_path'];
	}

	/**
	 * Returns the AutoMin cache URL for the current site
	 * @author Jesse Bunch
	 * @return string
	*/
	public function get_cache_url() {
		$settings_array = $this->get_settings();
		return $settings_array['cache_url'];
	}

	/**
	 * Is AutoMin enabled?
	 * @author Jesse Bunch
	 * @return bool
	*/
	public function is_automin_enabled() {
		$settings_array = $this->get_settings();
		return ($settings_array['automin_enabled'] == 'y');
	}

	/**
	 * Should we cache AutoMin results?
	 * @author Jesse Bunch
	 * @return bool
	*/
	public function is_caching_enabled() {
		$settings_array = $this->get_settings();
		return ($settings_array['caching_enabled'] == 'y');
	}

	/**
	 * Should we compress HTML markup?
	 * @author Jesse Bunch
	 * @return bool
	*/
	public function should_compress_markup() {
		$settings_array = $this->get_settings();
		return ($settings_array['compress_html'] == 'y');
	}

	/**
	 * Retrieves an array of AutoMin's settings for the current site
	 * @return array
	 * @author Jesse Bunch
	*/
	public function get_settings() {

		static $settings_array;

		// No sense in querying for settings
		// more than once per request.
		if (!$settings_array) {

			// Fetch settings stored in the DB
			$settings_query = $this->EE->db->limit(1)
				->where('site_id', $this->EE->config->item('site_id'));
			$settings_array = $settings_query
				->get('automin_preferences')
				->row_array();

			// If no row was returned, let's create
			// the row for this site
			if (!$settings_array) {
				$values = array(
					'site_id' => $this->EE->config->item('site_id')
				);
				$this->EE->db->insert('automin_preferences', $values);
				$settings_array = $settings_query
					->get('automin_preferences')
					->row_array();
			}	

			// Overwrite with config values
			foreach($settings_array as $key=>$value) {
				if ($this->EE->config->item("automin_$key")) {
					$settings_array[$key] = $this->EE->config->item("automin_$key");
				}
			}

			// Legacy 'automin_cache_enabled'
			$settings_array['caching_enabled'] = ($this->EE->config->item('automin_cache_enabled')) 
				? $this->EE->config->item('automin_cache_enabled')
				: $settings_array['caching_enabled'];

			// Legacy 'automin_compress_markup'
			$settings_array['compress_html'] = ($this->EE->config->item('automin_compress_markup')) 
				? $this->EE->config->item('automin_compress_markup')
				: $settings_array['compress_html'];

			// Legacy 'automin_cache_server_path'
			$settings_array['cache_path'] = ($this->EE->config->item('automin_cache_server_path')) 
				? $this->EE->config->item('automin_cache_server_path')
				: $settings_array['cache_path'];



		}

		return $settings_array;

	}

	/**
	 * Creates or Updates AutoMin settings for the current site
	 * @param array $settings_array
	 * @return TRUE
	 * @author Jesse Bunch
	*/
	public function set_settings($settings_array) {
	
		// Settings exist?
		$current_settings = $this->get_settings();

		// Create or update the settings for this site
		if (empty($current_settings)) {
			
			$settings_array['site_id'] = $this->EE->config->item('site_id');
			$settings_result = $this->EE->db->insert('automin_preferences', $settings_array);

		} else {
			$settings_result = $this->EE->db->limit(1)
				->where('site_id', $this->EE->config->item('site_id'))
				->update('automin_preferences', $settings_array);

		}

		return TRUE;

	}

}

/* End of file automin_model.php */
/* Location: /system/expressionengine/third_party/automin/libraries/automin_model.php */