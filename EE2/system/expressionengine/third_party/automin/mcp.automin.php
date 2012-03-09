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
 * AutoMin Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Jesse Bunch
 * @link		http://getbunch.com/
 */

class Automin_mcp {
	
	public $return_data;
	private $_base_url;
	private $_form_url;
	
	/**
	 * Constructor
	 * @author Jesse Bunch
	*/
	public function __construct() {

		$this->EE =& get_instance();
		
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=automin';
		$this->_form_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=automin';

		$this->EE->load->model('automin_model');
		$this->version = $this->EE->automin_model->version;

	}

	/**
	 * Module settings page
	 * @return void
	 */
	public function index() {

		$this->EE->cp->set_variable('cp_page_title', lang('automin_module_name'));

		return $this->EE->load->view('settings', array(

		), TRUE);

	}
	
}
/* End of file mcp.automin.php */
/* Location: /system/expressionengine/third_party/automin/mcp.automin.php */