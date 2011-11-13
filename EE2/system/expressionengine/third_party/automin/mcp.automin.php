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
class Automin_mcp {
	
	var $strBase;			// the strBase url for this module			
	var $strFormBase;		// strBase url for forms
	var $module_name = "automin";
	var $strMessage;

	// ---------------------------------------------------------------------

	function Automin_mcp($switch = TRUE) {
		
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance(); 
		$this->strBase	 	 = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;
		$this->strFormBase = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;
		
		// Initialize helpers
		$this->EE->load->library('table');
		$this->EE->load->library('javascript');
		$this->EE->load->helper('form');
		
		// Initialize Model
		$this->EE->load->library('automin_model');
			
	}
	
	// ---------------------------------------------------------------------

	function index() {
		
		$arrVars = array();
		$arrVars['arrSettings'] = $this->EE->automin_model->GetAutoMinPreferences();
		return $this->LoadView('preferences', 'automin_preferences', $arrVars);
		
	}
	
	// ---------------------------------------------------------------------
	
	function SetPreferences_Submit() {
		
		$arrData = $this->EE->input->post('data');
		
		if (is_array($arrData)) {
			
			$this->EE->automin_model->SetAutoMinPreferences($arrData);
			
		}
		
		$this->strMessage = lang('preferences_saved');
		
		return $this->index();
		
	}

	// ---------------------------------------------------------------------
	
	function LoadView($strContentView, $strLangKey, $arrVars = array()) {
		
		$arrVars['_strContentView'] = $strContentView;
		$arrVars['_strBase'] = $this->strBase;
		$arrVars['_strFormBase'] = $this->strFormBase;
		
		$arrVars['_strMessage'] = $this->strMessage;
		$this->strMessage = '';
		
		$this->EE->cp->set_variable('cp_page_title', lang($strLangKey));
		$this->EE->cp->set_breadcrumb($this->strBase, lang('automin_module_name'));

		return $this->EE->load->view('_wrapper', $arrVars, TRUE);
		
	}
	
	// ---------------------------------------------------------------------
	
}

/* End of file mcp.automin.php */ 
/* Location: ./system/expressionengine/third_party/automin/mcp.automin.php */