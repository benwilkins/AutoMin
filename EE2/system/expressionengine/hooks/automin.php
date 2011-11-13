<?php
// 
//  automin.php
//  AutoMin Module
//  http://code.getbunch.com/automin/
//  
//  Created by Jesse Bunch on 2011-04-16.
//  Copyright 2011 Jesse Bunch (www.GetBunch.com). All rights reserved.
// 

// Load the HTML minification class
if (!class_exists('Minify_HTML'))
	require(PATH_THIRD.'automin/libraries/class.html.min.php');

// Load the AutoMin model
if (!class_exists('Automin_model'))
	require(PATH_THIRD.'automin/libraries/automin_model.php');

/**
 * Compresses the HTML output from the
 * CodeIgniter output class
 *
 * @return void
 * @author Jesse Bunch
 */
function CompressOutput() {
	
	// Get instances
	$CI =& get_instance();
	$EE =& get_instance();
	$objModel = new Automin_model();
	
	// Get Preferences
	$arrPreferences = $objModel->GetAutoMinPreferences();
	
	// Minify, if enabled
	$strOutput = $EE->output->get_output();
	if (isset($arrPreferences['automin_enabled']) && 
			$arrPreferences['automin_enabled'] == 'y' && 
			$arrPreferences['compress_markup'] == 'y' &&
			defined('REQ') &&
			REQ != 'CP') {
		$strOutput = Minify_HTML::minify($strOutput);
	}
	
	// Write output to appropriate method, if available
	if (method_exists($EE->output, '_display')) {
		$EE->output->_display($strOutput);
	} else {
		echo $strOutput;
	}
 	
	
}