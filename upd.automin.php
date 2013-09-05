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
	public $version = '2.1.4';

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

		// AutoMin table
		$sql[] = "CREATE TABLE IF NOT EXISTS `exp_automin_preferences` (
					  `site_id` int(10) NOT NULL,
					  `automin_enabled` varchar(1) NOT NULL DEFAULT 'n',
					  `caching_enabled` varchar(1) NOT NULL DEFAULT 'n',
					  `compress_html` varchar(1) NOT NULL DEFAULT 'n',
					  `cache_path` varchar(255) NOT NULL DEFAULT '',
					  `cache_url` varchar(255) NOT NULL DEFAULT '',
					  PRIMARY KEY (`site_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$sql[] = "INSERT INTO exp_automin_preferences(site_id) VALUES({$this->EE->config->item('site_id')})";

		foreach($sql as $query) {
			$this->EE->db->query($query);
		}


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

		$this->EE->db->query("DROP TABLE IF EXISTS exp_automin_preferences");

		return TRUE;
	}

	/**
	 * Update routine
	 * @param string $current The version to upgrade to.
	 * @return bool TRUE
	 * @author Jesse Bunch
	*/
	public function update($current = '') {

		if (version_compare($current, '2.1', '<')) {
			$this->uninstall();
			$this->install();
			return TRUE;
		}

		return TRUE;
	}

}
/* End of file upd.automin.php */
/* Location: /system/expressionengine/third_party/automin/upd.automin.php */
