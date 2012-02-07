<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Automatic CSS/JavaScript combination, minification and caching for ExpressionEngine.
 *
 * @package		Automin
 * @subpackage	ThirdParty
 * @category	Modules
 * @author		Jesse Bunch
 * @link		http://code.getbunch.com/automin/source
 */
class Automin_upd {
		
	var $version        = '1.4.0'; 
	var $module_name = "Automin";
	
    function Automin_upd($switch = TRUE) {
	
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		
    } 

	// ---------------------------------------------------------------------

    /**
     * Installer for the Automin module
     */
    function install() {				
						
		$data = array(
			'module_name' 	 => $this->module_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'y'
		);

		$this->EE->db->insert('modules', $data);		
		
		$this->EE->load->dbforge();
		
		$automin_preferences_fields = array(
		    'row_id' => array(
		        'type' => 'int',
		        'constraint' => '10',
		        'null' => FALSE),
		    'automin_enabled' => array(
		        'type' => 'varchar',
		        'constraint' => '1',
		        'null' => FALSE),
		    'cache_enabled' => array(
		        'type' => 'varchar',
		        'constraint' => '1',
		        'null' => FALSE),
		    'compress_markup' => array(
		        'type' => 'varchar',
		        'constraint' => '1',
		        'null' => FALSE),
		    'cache_server_path' => array(
		        'type' => 'varchar',
		        'constraint' => '255',
		        'null' => FALSE),
		    'cache_url' => array(
		        'type' => 'varchar',
		        'constraint' => '255',
		        'null' => FALSE)
		);
		
		$this->EE->dbforge->add_field($automin_preferences_fields);
		$this->EE->dbforge->add_key('row_id', TRUE);
		$this->EE->dbforge->create_table('automin_preferences');
		
		// Insert default preferences
		$this->EE->load->library('automin_model');
		$this->EE->automin_model->CreateDefaultPreferences();
																									
		return TRUE;
		
	}
	
	// ---------------------------------------------------------------------

	
	/**
	 * Uninstall the Automin module
	 */
	function uninstall() { 				
		
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => $this->module_name));
		
		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');
		
		$this->EE->db->where('module_name', $this->module_name);
		$this->EE->db->delete('modules');
		
		$this->EE->db->where('class', $this->module_name);
		$this->EE->db->delete('actions');
		
		$this->EE->db->where('class', $this->module_name.'_mcp');
		$this->EE->db->delete('actions');
		
		$this->EE->load->dbforge();
		$this->EE->dbforge->drop_table('automin_preferences');
										
		return TRUE;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Update the Automin module
	 * 
	 * @param $current current version number
	 * @return boolean indicating whether or not the module was updated 
	 */
	
	function update($current = '') {
		
		// Update version number in EE
		$this->EE->db->where('module_name', $this->module_name);
		$this->EE->db->update('modules', array('module_version' => $this->version));
		
		return TRUE;
		
	}

	// ---------------------------------------------------------------------

}

/* End of file upd.automin.php */ 
/* Location: ./system/expressionengine/third_party/automin/upd.automin.php */