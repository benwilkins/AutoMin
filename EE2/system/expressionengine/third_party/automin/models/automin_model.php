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
	 * The current AutoMin version
	 * @var string
	 * @author Jesse Bunch
	*/
	public $version = '2.1';

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
	 * Retrieves an array of AutoMin's settings
	 * @return array
	 * @author Jesse Bunch
	*/
	public function get_settings() {

		$settings_result = $this->EE->db->limit(1)
			->where('site_id', $this->config->item('site_id'))
			->get('automin_preferences');

		return $settings_result->row_array();

	}

	/**
	 * Updates AutoMin settings
	 * @param array $settings_array
	 * @return TRUE
	 * @author Jesse Bunch
	*/
	public function set_settings($settings_array) {
		


	}

}

/* End of file automin_model.php */
/* Location: /system/expressionengine/third_party/automin/libraries/automin_model.php */