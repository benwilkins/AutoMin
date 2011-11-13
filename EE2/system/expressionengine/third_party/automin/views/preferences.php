<?php
// 
//  preferences.php
//  AutoMin Module
//  
//  Created by Jesse Bunch on 2011-04-16.
//  Copyright 2011 Jesse Bunch (www.GetBunch.com). All rights reserved.
// 

/*

	This template allows the user to edit AutoMin preferences

	Template Variables:
	$arrCalloutFieldGroups - An array containing all the callout field groups

*/


// -------------------------------------
// 	Set template
// -------------------------------------

$this->table->set_template($cp_table_template);
$this->table->set_heading(
						lang('setting'), 
						array('data' => lang('value'), 'style' => 'width:65%;')
);

// -------------------------------------
// 	Add Form Rows
// -------------------------------------

$arrYesNo = array('y' => lang('yes'), 'n' => lang('no'));

$this->table->add_row(
	form_label(lang('automin_enabled'), 'enable_automin'),
	form_dropdown('data[automin_enabled]', $arrYesNo, (isset($arrSettings['automin_enabled'])) ? $arrSettings['automin_enabled'] : '')
);

$this->table->add_row(
	form_label(lang('cache_enabled'), 'cache_enabled'),
	form_dropdown('data[cache_enabled]', $arrYesNo, (isset($arrSettings['cache_enabled'])) ? $arrSettings['cache_enabled'] : '')
);

$this->table->add_row(
	form_label(lang('compress_markup'), 'compress_markup') . '<br>' . lang('compress_markup_instructions'),
	form_dropdown('data[compress_markup]', $arrYesNo, (isset($arrSettings['compress_markup'])) ? $arrSettings['compress_markup'] : '')
);

$this->table->add_row(
	form_label(lang('cache_server_path'), 'cache_server_path') . '<br>' . lang('cache_server_path_instructions'),
	form_input('data[cache_server_path]', (isset($arrSettings['cache_server_path'])) ? $arrSettings['cache_server_path'] : '')
);

$this->table->add_row(
	form_label(lang('cache_url'), 'cache_url') . '<br>' . lang('cache_url_instructions'),
	form_input('data[cache_url]', (isset($arrSettings['cache_url'])) ? $arrSettings['cache_url'] : '')
);
	
// -------------------------------------
// 	Write out the view
// -------------------------------------

?>
<?=form_open($_strFormBase.AMP.'method=SetPreferences_Submit');?>
<?=(isset($arrHiddenVars)) ? form_hidden($arrHiddenVars) : '';?>
<?=$this->table->generate();?>
<?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'));?>
<?=form_close();?>