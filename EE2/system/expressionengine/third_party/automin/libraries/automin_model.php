<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// 
//  automin_model.php
//  AutoMin Module
//  
//  Created by Jesse Bunch on 2011-04-16.
//  Copyright 2011 Jesse Bunch (www.GetBunch.com). All rights reserved.
// 


/**
* AutoMin Model
*/
class Automin_model {
	
	private $EE; 
	
	/**
	 * Constructor
	 *
	 * @author Jesse Bunch
	 */
	function __construct() {
		
		$this->EE =& get_instance();
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Returns the preferences row from the DB.
	 *
	 * @return array
	 * @author Jesse Bunch
	 */
	function GetAutoMinPreferences() {
		
		if ($this->isAutominInstalled()) {
			
			$this->EE->db->limit(1);
			$objResult = $this->EE->db->get('automin_preferences');

			if ($objResult->num_rows() !== 1) {

				$this->CreateDefaultPreferences();

				return $this->GetAutoMinPreferences();

			}

			$arrPreferences = $objResult->row_array();

			// If empty, set default server path
			if (empty($arrPreferences['cache_server_path']))
				$arrPreferences['cache_server_path'] = "{$_SERVER['DOCUMENT_ROOT']}/automin/";

			// If empty, set default url to cache dir.
			if (empty($arrPreferences['cache_url']))
				$arrPreferences['cache_url'] = '/automin/';
		
			return $arrPreferences;
			
		} else {
			
			return array();
			
		}
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Updates the module's preferences
	 *
	 * @param string $arrData 
	 * @return void
	 * @author Jesse Bunch
	 */
	function SetAutoMinPreferences($arrData) {
		
		$arrPreferences = $this->GetAutoMinPreferences();
		
		if (count($arrPreferences)) {
			
			$this->EE->db->where('row_id', $arrPreferences['row_id']);
			$this->EE->db->limit(1);
			$this->EE->db->update('automin_preferences', $arrData);
			
		}
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Inserts a new row into the preferences table using default values.
	 *
	 * @return void
	 * @author Jesse Bunch
	 */
	function CreateDefaultPreferences() {
		
		$this->EE->db->insert('automin_preferences', array(
			'automin_enabled' => 'y',
			'cache_enabled' => 'y',
			'compress_markup' => 'y'
		));
		
	}
	
	// ---------------------------------------------------------------------
	
	function isAutominInstalled() {
		
		$this->EE->db->limit(1);
		$this->EE->db->where('module_name', 'Automin');
		$objResults = $this->EE->db->get('modules');
		
		if ($objResults->num_rows() === 1) {
			return TRUE;
		} else {
			return FALSE;
		}
		
	}
	
	
}

/* End of file automin_model.php */ 
/* Location: ./system/expressionengine/third_party/automin/libraries/automin_model.php */
