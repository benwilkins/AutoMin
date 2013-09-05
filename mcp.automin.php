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

	}

	/**
	 * Module settings page
	 * @return void
	 */
	public function index() {

	        if (version_compare(APP_VER, '2.6', '>=')) {
			$this->EE->cp->cp_page_title = lang('automin_module_name');
	        } else {
			$this->EE->cp->set_variable('cp_page_title', lang('automin_module_name'));
	        }

		return $this->EE->load->view('settings', array(
			'form_action' => $this->_form_url.AMP.'method=index_submit',
			'automin_settings' => $this->EE->automin_model->get_settings()
		), TRUE);

	}

	public function index_submit() {
		
		$settings_array = array(
			'automin_enabled' => $this->EE->input->post('automin_enabled'),
			'caching_enabled' => $this->EE->input->post('caching_enabled'),
			'compress_html' => $this->EE->input->post('compress_html'),
			'cache_path' => $this->EE->input->post('cache_path'),
			'cache_url' => $this->EE->input->post('cache_url'),
		);

		// Update
		$this->EE->automin_model->set_settings($settings_array);
		$this->EE->session->set_flashdata(
			'message_success', 
			lang('settings_updated_successfully')
		);

		$this->EE->functions->redirect($this->_base_url);

	}
	
}
/* End of file mcp.automin.php */
/* Location: /system/expressionengine/third_party/automin/mcp.automin.php */
