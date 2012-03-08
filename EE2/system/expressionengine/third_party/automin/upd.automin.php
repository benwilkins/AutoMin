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
 * AutoMin Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Jesse Bunch
 * @link		http://getbunch.com/
 */

class Automin_upd {
	
	/**
	 * Provides the version number to EE
	 * Set in the constructor since it comes from the model.
	 * @var string
	 * @author Jesse Bunch
	*/
	public $version;

	/**
	 * Holds the EE instance
	 * @var EE
	 * @author Jesse Bunch
	*/
	private $EE;
	
	/**
	 * Constructor
	 * @author Jesse Bunch
	*/
	public function __construct() {
		$this->EE =& get_instance();
		$this->EE->load->model('automin_model');
		$this->version = $this->EE->automin_model->version;
	}
	
	/**
	 * Installation routine
	 * @return bool TRUE
	 * @author Jesse Bunch
	*/
	public function install() {

		$module_data = array(
			'module_name' => 'Automin',
			'module_version' => $this->version,
			'has_cp_backend' => "y",
			'has_publish_fields' => 'n'
		);
		
		$this->EE->db->insert('modules', $module_data);
		
		return TRUE;
	}
	
	/**
	 * Uninstallation routine
	 * @return bool TRUE
	 * @author Jesse Bunch
	*/
	public function uninstall() {

		$module_id = $this->EE->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> 'Automin'
								))->row('module_id');
		
		$this->EE->db->where('module_id', $module_id)
					 ->delete('module_member_groups');
		
		$this->EE->db->where('module_name', 'Automin')
					 ->delete('modules');
		
		return TRUE;
	}
	
	/**
	 * Update routine
	 * @param string $current The version to upgrade to.
	 * @return bool TRUE
	 * @author Jesse Bunch
	*/
	public function update($current = '') {
		
		// If you have updates, drop 'em in here.
		return TRUE;
	}
	
}
/* End of file upd.automin.php */
/* Location: /system/expressionengine/third_party/automin/upd.automin.php */