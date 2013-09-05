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
 * AutoMin Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Jesse Bunch
 * @link		http://getbunch.com/
 */

class Automin_ext {

	public $settings 		= array();
	public $description		= 'AutoMin extension compresses your HTML markup.';
	public $docs_url		= 'https://github.com/bunchjesse/AutoMin';
	public $name			= 'AutoMin';
	public $settings_exist	= 'n';
	public $version			= '2.1.4';

	private $EE;

	/**
	 * Constructor
	 * @param mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '') {
		$this->EE =& get_instance();
		$this->settings = $settings;
	}

	/**
	 * Activate Extension
	 * This function enters the extension into the exp_extensions table
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 * @return void
	 */
	public function activate_extension() {
		// Setup custom settings in this array.
		$this->settings = array();

		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'template_post_parse',
			'hook'		=> 'template_post_parse',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		$this->EE->db->insert('extensions', $data);

	}

	/**
	 * Hook for processing template output.
	 * @param string $template_string The template markup
	 * @param bool $is_embed Is the template an embed?
	 * @param int $site_id Site ID
	 * @return string The final template string
	 */
	public function template_post_parse($template_string, $is_embed, $site_id) {

		$final_string = $template_string;

		// AutoMin model
		$this->EE->load->model('automin_model');

		// Prior output?
		if (isset($this->EE->extensions->last_call)
			&& $this->EE->extensions->last_call) {
			$final_string = $this->EE->extensions->last_call;
		}

		// Is HTML minifcation disabled?
		if (!$this->EE->automin_model->should_compress_markup()) {
			return $final_string;
		}

		// Minify
		if (!$is_embed) {

			// Minify
			$data_length_before = strlen($final_string) / 1024;
			require_once('libraries/class.html.min.php');
			$final_string = Minify_HTML::minify($final_string);
			$data_length_after = strlen($final_string) / 1024;

			// Log results
			$data_savings_kb = $data_length_before - $data_length_after;
			$data_savings_percent = $data_savings_kb / $data_length_before;
			$data_savings_message = sprintf(
				'AutoMin Module HTML Compression: Before: %1.0fkb / After: %1.0fkb / Data reduced by %1.2fkb or %1.2f%%',
				$data_length_before,
				$data_length_after,
				$data_savings_kb,
				$data_savings_percent
			);
			$this->EE->TMPL->log_item($data_savings_message);

		}

		return $final_string;

	}

	/**
	 * Disable Extension
	 * This method removes information from the exp_extensions table
	 * @return void
	 */
	function disable_extension() {
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	/**
	 * Update Extension
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 * @return mixed void on update / false if none
	 */
	function update_extension($current = '') {
		if ($current == '' OR $current == $this->version) {
			return FALSE;
		}
	}

}

/* End of file ext.automin.php */
/* Location: /system/expressionengine/third_party/automin/ext.automin.php */
