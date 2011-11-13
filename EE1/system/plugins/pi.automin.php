<?php

$plugin_info = array(
						'pi_name'			=> 'AutoMin',
						'pi_version'		=> '1.1',
						'pi_author'			=> 'Jesse Bunch (Paramore, the digital agency)',
						'pi_author_url'		=> 'http://www.paramore.is/',
						'pi_description'	=> 'Automatically combines, compiles, and caches your CSS and JavaScript code.',
						'pi_usage'			=> Automin::usage()
					);
					
require_once('automin_inc/class.minify_css_compressor.php');
require_once('automin_inc/class.jsmin.php');
					
/**
 * AutoMin Class
 * @package default
 */
class Automin {
	
	/**
	 * Should we inject the CSS/JS directly into the page? If not, 
	 * the CSS/JS will be written to one file and included on the page.
	 * @var boolean
	 */
	var $boolInject = TRUE;
	
	/**
	 * Server path to the automin cache directory
	 * Set in the object's constructor
	 * @var string
	 */
	var $strCacheDir;
	
	/**
	 * Relative path to the cache directory
	 * Set in object's constructor
	 * @var string
	 */
	var $strRelativeCacheDir;
	
	/**
	 * Set by our script - this is the filename to the cache file
	 * @var string
	 */
	var $strAbsoluteCacheFilename;
	
	/**
	 * Path to the document root - set in constructor
	 * @var string
	 */
	var $strDocumentRootPath;
	
	/**
	 * The major version of the EE install
	 * @var string
	 */
	var $intEEMajorVersion = 2;
	
	// ---------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Automin() {
		
		// Set the cache directory paths
		$this->strDocumentRootPath = $_SERVER['DOCUMENT_ROOT'] . '/';
		$this->strAbsoluteCacheDir = "{$_SERVER['DOCUMENT_ROOT']}/automin/";
		$this->strRelativeCacheDir = '/automin/';
		
		// Get the major version number
		$this->intEEMajorVersion = substr(APP_VER, 0, 1);
		
		if ($this->intEEMajorVersion == 2)
			$this->EE =& get_instance();

	}

	// --------------------------------------------------------------------
	
	/**
	 * Minifies CSS and Code
	 * @return void
	 */
	function css() {
		
		$strTags = $this->_fetch_tagdata();
		$strSource = $this->_process_tagdata($strTags, 'css');
		
		if (empty($this->strCacheFilename))
			return sprintf('<style %s>%s</style>', $this->_fetch_parameters(), $strSource);
		else
			return sprintf('<link href="%s" %s>', $this->strCacheFilename, $this->_fetch_parameters());
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Minifies JS
	 * @return void
	 */
	function js() {
		
		$strTags = $this->_fetch_tagdata();
		$strSource = $this->_process_tagdata($strTags, 'js');
		
		if (empty($this->strCacheFilename))
			return sprintf('<script %s>%s</script>', $this->_fetch_parameters(), $strSource);
		else
			return sprintf('<script src="%s" %s></script>', $this->strCacheFilename, $this->_fetch_parameters());
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Loops through the current tag's parameters and creates a string to use
	 * in the output of the final CSS or JS tag. For example: {exp:automin:css type="text/css" title="default"}
	 * would output: <link href="%s" type="text/css" title="default">
	 * @return void
	 */
	function _fetch_parameters() {
		
		$arrTemplateParams = $this->_fetch_tagparams();
		
		// Unset any parameters that shouldn't be sent to the
		// final CSS / JS include HTML tag here
		
		$arrFinalParams = array();
		foreach($arrTemplateParams as $key => $value)
			$arrFinalParams[] = sprintf('%s="%s"', $key, $value);
		
		return implode(' ', $arrFinalParams);
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Central method that does the processing
	 * @param string $strTags The tagdata from EE
	 * @param string $strType The type of tagdata: 'css' or 'js'
	 * @param boolean $boolReturnSource If TRUE, will return the minfied source instead of the file path
	 * @return string
	 */
	function _process_tagdata($strTags, $strType) {
		
		// -------------------------------------
		// 	Generate tag data hash - this will be our cache filename
		// -------------------------------------
		
		// Trim and strip any uneccessary white space from our tags
		// This is so we don't get a false positive from a space or tab
		// when we check to see if anything has changed.
		$strTags = trim($strTags);
		$strTags = str_replace(array("\n", "\r", "\t"), '', $strTags);
		
		// Generate a hash so we know if something has changed
		$strTagHash = md5($strTags);
		
		// Add the extension (js or css)
		$strCacheFilename = $strTagHash . ".$strType";
		
		// Construct the filepath to the cache
		$strCacheFilePath = $this->strAbsoluteCacheDir . $strCacheFilename;
		$this->strCacheFilename = $this->strRelativeCacheDir . $strCacheFilename;
		
		// -------------------------------------
		// 	Extract the file names
		// -------------------------------------
		
		$arrFilenames = $this->_extract_filenames($strTags, $strType);
		
		if (count($arrFilenames) == 0):
			
			return $strTags;
			
		endif;
		
		$intLatestModified = $this->_get_latest_modified_time($arrFilenames);
		
		// -------------------------------------
		// 	Is the cache valid?
		// -------------------------------------

		if (!$this->_validate_cache($strCacheFilePath, $intLatestModified)):
			
			$strData = $this->_regenerate_cache($arrFilenames, $strType);
			
			if ($strData !== FALSE):
			
				$this->_write_file($strData, $strCacheFilePath);

			else:
			
				return $strTags;
				
			endif;
			
			return $strData;
			
		else:
		
			return $this->_read_file($strCacheFilePath);
		
		endif;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Extracts filenames from an HTML string. Prepends the path to the front-end
	 * controller to each path and removes double slashes.
	 * @param string $strTagData 
	 * @return array
	 */
	function _extract_filenames($strTagData, $strType) {
		
		$arrMatches = array();
		
		switch($strType):
		
			case 'css':
				
				preg_match_all("/href\=\"([A-Za-z0-9\.\/\_\-]+)\"/", $strTagData, $arrMatches);
				
			break;
			
			// ------
			
			case 'js':
				
				preg_match_all("/src\=\"([A-Za-z0-9\.\/\_\-]+)\"/", $strTagData, $arrMatches);
			
			break;
			
			// ------
			
			default:
			
				return FALSE;
				
			break;
		
		endswitch;
		
		$arrFileNames = array();
		foreach($arrMatches[1] as $strFileName):
		
			$arrFileNames[] = str_replace('//', '/', $this->strDocumentRootPath . $strFileName);
		
		endforeach;
		
		return $arrFileNames;
		
	}	
	
	// ---------------------------------------------------------------------
	
	/**
	 * From an array of filenames, gets the timestamp of the most recently edited file.
	 * @param array $arrFilenames 
	 * @return int
	 */
	function _get_latest_modified_time($arrFilenames) {
		
		$intLastModified = 0;
		
		foreach($arrFilenames as $strFilename):
		
			$intModified = @filemtime($strFilename);
			
			if ($intModified !== FALSE):
			
				if ($intModified > $intLastModified):
					
					$intLastModified = $intModified;
				
				endif;
				
			endif;
		
		endforeach;
		
		return $intLastModified;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Writes the given data to the cache directory using the filename provided.
	 * If the directory doesn't exist, this function attempts to create it with 777 permissions.
	 * @param string $strData 
	 * @param string $strFilename 
	 * @return boolean
	 */
	function _write_file($strData, $strFilePath) {
		
		if (empty($strData))
			return FALSE;
		
		
		if (!is_dir($this->strAbsoluteCacheDir)):
			
			if (!@mkdir($this->strAbsoluteCacheDir, 0777)):
			
				return FALSE;
				
			endif;
			
			@chmod($this->strAbsoluteCacheDir, 0777);
			
		endif;
		
		if (file_put_contents($strFilePath, $strData, LOCK_EX) === FALSE):
		
			return FALSE;
			
		else:
		
			@chmod($strFilePath, 0777);
			
			return TRUE;
			
		endif;

	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Determines if the cache filename provided eists and is writable. Further,
	 * the function checks the modified time of the cache file to see if it is
	 * later than the unix timestamp provided.
	 * @param string $strCacheFilePath Full server path to the cache file
	 * @param string $intLatestModified UNIX Timestamp. To return TRUE, the cache file's modified
	 * 									timestamp must be after ths value to indicate it's not out of date.
	 * @return boolean
	 */
	function _validate_cache($strCacheFilePath, $intLatestModified) {
		
		if (!@is_dir($this->strAbsoluteCacheDir)):
		
			if (!@mkdir($this->strAbsoluteCacheDir, 0777)):
			
				return FALSE;
			
			else:
			
				@chmod($this->strAbsoluteCacheDir, 0777);
				
			endif;
			
		else:
			
			if (!@is_readable($strCacheFilePath)):
			
				return FALSE;
				
			else:
				
				$intCacheModified = @filemtime($strCacheFilePath);
				
				if ($intCacheModified !== FALSE && $intCacheModified >= $intLatestModified):
				
					return TRUE;
					
				else:
				
					return FALSE;
					
				endif;
				
			endif;
			
		endif;		
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Reads the specified file into memory
	 * @param string $strFilename 
	 * @return string
	 */
	public function _read_file($strFilename) {
		
		return @file_get_contents($strFilename);
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Reads and combines the files in the provided order
	 * @param string $arrFilenames 
	 * @return string
	 */
	public function _combine_files($arrFilenames) {
		
		$strReturnData = '';
		
		foreach($arrFilenames as $strFilename):
		
			$strFileData = $this->_read_file($strFilename);
			
			if ($strFileData !== FALSE):
			
				$strReturnData .= $strFileData . "\n\n";
				
			endif;
		
		endforeach;
		
		return $strReturnData;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Regenerates the cache from the array of filenames provided
	 * @param string $arrFilenames 
	 * @param string $strType Either 'css' or 'js' - Determines the minifier used
	 * @return string
	 */
	public function _regenerate_cache($arrFilenames, $strType) {
		
		@ini_set("memory_limit","12M");
		@ini_set("memory_limit","16M");
		@ini_set("memory_limit","32M");
		@ini_set("memory_limit","64M");
		@ini_set("memory_limit","128M");
		@ini_set("memory_limit","256M");
		
		$strDataToReturn = '';
		$strDataToReturn = $this->_combine_files($arrFilenames);
		
		if ($strDataToReturn === FALSE):
		
			return FALSE;
			
		endif;
		
		switch($strType):
		
			case 'css':
				
				$strDataToReturn = Minify_CSS_Compressor::process($strDataToReturn);

				return $strDataToReturn;
			
			break;
			
			// ------
			
			case 'js':
				
				$strDataToReturn = JSMin::minify($strDataToReturn);
				
				return $strDataToReturn;
			
			break;
			
			// ------
			
			default:
				return FALSE;
			break;
		
		endswitch;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Returns the template tag data (from where depends on the EE version)
	 *
	 * @return string
	 * @author Jesse Bunch
	 */
	function _fetch_tagdata() {
	
		$strTags = '';
		
		switch($this->intEEMajorVersion):
		
			case 1:
			
				global $TMPL;
				$strTags = $TMPL->tagdata;
			
			break;
			
			// ------
				
			case 2:
		
				$strTags = $this->EE->TMPL->tagdata;
			
			break;
			
		
		endswitch;
		
		return html_entity_decode($strTags);
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Returns the template tag params (from where depends on the EE version)
	 *
	 * @return array
	 * @author Jesse Bunch
	 */
	function _fetch_tagparams() {
		
		$arrTags = array();
		
		switch($this->intEEMajorVersion):
		
			case 1:
			
				global $TMPL;
				$arrTags = $TMPL->tagparams;
			
			break;
			
			// ------
				
			case 2:
			
				$arrTags = $this->EE->TMPL->tagparams;
			
			break;
		
		endswitch;
		
		return $arrTags;
		
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Usage
	 *
	 * Plugin Usage
	 *
	 * @access	public
	 * @return	string
	 */
	function usage() {
		ob_start(); 
		?>
		
		AutoMin is an ExpressionEngine plugin that automates the push-to-production task of combining/compiling your JavaScript and CSS files.
		After initial processing, AutoMin caches the result until one of the files is modified. Once a file is modified, Automin regenerates 
		the cache and uses the new versions automatically.
		
		AutoMin replaces the script/css tags with a single tag pointing to the compiled result.
		
		Thanks to the minify project for their CSS compressor and the JSMin project for their JavaScript minifiaction class. I've tried to develop
		this plugin in a clean and pragmatic fasion to make it easy for you to modify, should modification be necessary to work with your setup.
		
		Minify: http://code.google.com/p/minify/
		JSMin: http://www.crockford.com/javascript/jsmin.html
		
		SEE THE README FILE FOR MORE DETAILED INFORMATION
		
		------------------
		EXAMPLE USAGE:
		------------------
		
		{exp:automin:js type="text/javascript"}
			<script type="text/javascript" src="/js/jquery.js"></script>
			<script type="text/javascript" src="/js/jquery.ui.js"></script>
			<script type="text/javascript" src="/js/jquery.ui.mouse.js"></script>
			<script type="text/javascript" src="/js/jquery.ui.position.js"></script>
			<script type="text/javascript" src="/js/jquery.ui.widget.js"></script>
			<script type="text/javascript" src="/js/jquery.ui.draggable.js"></script>
			<script type="text/javascript" src="/js/jquery.regex.js"></script>
			<script type="text/javascript" src="/js/jquery.regex.js"></script>
			<script type="text/javascript" src="/js/cufon.js"></script>
			<script type="text/javascript" src="/js/global.js"></script>
		{/exp:automin:js}
		
		{exp:automin:css type="text/css" title="default" rel="stylesheet" media="screen, projection"}
			<link href="/css/core.css" type="text/css" title="default" rel="stylesheet" media="screen, projection">
			<link href="/css/design.css" type="text/css" title="default" rel="stylesheet" media="screen, projection">
		{/exp:automin:css}
		
		Note: It's important that you don't use absolute paths in your CSS/JS tags and that your paths
		are web-root relative (begin with a forward slash).
		
		------------------
		PARAMETERS:
		------------------
		
		Any parameter that you specify will be included as an attribute to the resulting HTML tag that AutoMin produces. Take the examples above,
		for instance:
		
		{exp:automin:js type="text/javascript"} will output a script tag similar to:
		<script src="/automin/7dc66e1b2104b40a9992a3652583f509.js" type="text/javascript"></script>
		
		{exp:automin:css type="text/css" title="default" rel="stylesheet" media="screen, projection"} will output:
		<link href="/automin/55ed34446f3eac6f869f3fe5b375d311.css" type="text/css" title="default" rel="stylesheet" media="screen, projection">		
		
		------------------
		VARIABLES:
		------------------
		
		There are no variables for AutoMin. Anything inside the tag pair will be replaced with the final output.
		
		<?php
		
		$buffer = ob_get_contents();

		ob_end_clean(); 

		return $buffer;
	}

	// --------------------------------------------------------------------
	
}
// END Automin Class

/* End of file  pi.automin.php */
/* Location: ./system/expressionengine/third_party/automin/pi.automin.php */