Introduction
---
AutoMin is an ExpressionEngine module and extension that automates the combination and compression of your source files and currently supports CSS, JavaScript, LESS, and HTML compression.

AutoMin is smart enough to know when you've changed your source files and will automatically regenerate it's cache when appropriate.

For support, please file a bug report or feature request at the AutoMin repository on Github: https://github.com/bunchjesse/AutoMin/issues

Please note: I work on AutoMin in my spare time so I may not be able to address your issues right away. This is why AutoMin is free. The code is well organized and documented so feel free to poke around the and submit pull requests for any improvements you make.

Special Thanks
---
Thanks to the minify project for their CSS compressor and the JSMin project for their JavaScript minifiaction class. Also, thanks goes to leafo for the PHP LESS processor. I've tried to develop this plugin in a clean and pragmatic fasion to make it easy for you to modify, should modification be necessary to work with your setup.

 - Minify: http://code.google.com/p/minify/
 - JSMin: http://www.crockford.com/javascript/jsmin.html
 - LESS for PHP: http://leafo.net/lessphp/

Changelog
---
### Version 2.1.4
- Fixed Deprecation notice #27 for optimal compatibility with versions >= 2.6. 

### Version 2.1.3
- Fixed Bug #23. Updated LessPHP to support the latest version of Twitter Bootsrap.

### Version 2.1.2
 - Fixed Bug #10. When compiling less, the .less extension is no longer given to the cache file.

### Version 2.1 - Please read carefully!
 - A general rewrite of the code was done to improve performance and make room for new features that are in the works. Please read these notes prior to upgrading!
 - Relative file paths are now supported in both your markup and in CSS @imports. You should still use document-root relative paths in your CSS files as those paths are **NOT** rewritten.
 - Tag attributes have changed. You must now prefix the attributes you want to be passed along to AutoMin's output with the string `attribute:`. Here's an example: `{exp:automin:js attribute:type="text/javascript"}`
 - Config overrides have changed. Please see the notes below. The old config overrides will continue to work, but it is recommended that you go ahead and update your config file now.
 - Fixed Bug #6. Multiple sites are now supported.
 - Fixed Bug #7. Config overrides are now reflected in the CP.
 - Fixed Bug #8. Less now parses @import statements. Bootstrap is now supported.
 - Added Feature Request #4. Markup compression no longer requires the CodeIgniter hook. Thanks to a nifty new extension hook in EE 2.4. Please remove the `automin.php` file from your `system/expressionengine/hooks` directory and remove the AutoMin line from your `system/expressionengine/config/hooks.php` file. Requires ExpressionEngine 2.4+.
 - If you experience any funkyness after updating AutoMin to v2.1, try the following: 1) Uninstall and then re-install the module and extension. 2) Make sure you've changed your template tags to use the new attribute syntax. 3) Make sure your CSS and JS pass lint without any errors. 4) Check the template log for any errors processing your template tags.

### Version 2.0
 - Added support for LESS compilation! Updated the CSS and JS compression classes.
 - Added ability to override AutoMin's configuration options in the config.php file.
 - Refactored function names to adhere to EllisLab coding guidelines.
 - Removed an unnecessary file read when returning cached data. When returning compiled source code, the timestamp of the latest-modified file is appended to the compiled source file so that browsers will cache files as long as possible---but not too long.

### Version 1.3.1
 - Fixed an issue where Automin bypassed the EE Output class when HTML compression was enabled

### Version 1.3.0
 - Added support for EE globals, path variables, stylesheet tags, etc

###Version 1.2.4
 - Fixed a HTML minification compatibility issue with Low Reorder

### Version 1.2.3
 - Hotfix for logic error in AutoMin hook

### Version 1.2.2
 - Hotfix for HTML markup compression issues

### Version 1.2.0
 - Added markup minification. Added module settings for EE2. Added support for @import in CSS (see issue #4). Added template debugging and file size reporting.

### Version 1.1.0
 - Added support for EE 1.X

### Version 1.0.1
 - Fixed Bug #1: Issue with regex not matching filenames with a hypen

### Version 1.0.0
 - Initial Release


Installation (EE 2.X)
---
1. Download and extract the automin folder to your `/system/expressionengine/third_party` directory.
2. Create the AutoMin cache directory somewhere below your document root. Make sure it is writable by Apache (most of the time this means giving the folder 777 permissions). I usually create a folder called `automin` just inside my `public_html` or `httpdocs` directory.
3. In EE, navigate to Addons->Modules. Click install next to AutoMin. If you'd like to compress your HTML, install the extension as well.
4. In the settings page for AutoMin, enter the server path and url to your AutoMin cache directory. Be sure to enable AutoMin and caching. All of these options may be overridden in your config file.
5. Add the AutoMin template tags to your ExpressionEngine templates. I recommend that you either separate your CSS and JS into an embed template that isn't cached as it will prevent EE from caching AutoMin's output, which can prevent AutoMin from noticing when you change your source code.
6. Refresh your site. If all goes well, you'll see your CSS and JS code combined and compressed. See the template debugger for error messages. Note: the first page load after changing your source files or the AutoMin template tags could take longer than usual while your code is compressed.

Example Usage
---

####JavaScript

	{exp:automin:js
		attribute:type="text/javascript"
	}
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

####CSS

	{exp:automin:css
		attribute:type="text/css"
		attribute:title="default"
		attribute:rel="stylesheet"
		attribute:media="screen, projection"
	}
		<link href="/css/core.css" type="text/css" title="default" rel="stylesheet" media="screen, projection">
		<link href="/css/design.css" type="text/css" title="default" rel="stylesheet" media="screen, projection">
	{/exp:automin:css}

####LESS
	{exp:automin:less
		attribute:type="text/css"
		attribute:title="default"
		attribute:rel="stylesheet"
		attribute:media="screen, projection"
	}
		<link rel="stylesheet/less" type="text/css" href="/css/styles.less">
	{/exp:automin:less}

Currently, you may not combine LESS code with normal CSS code. Use two separate AutoMin tags to compile both CSS and LESS.

Tag Parameters
---
Any parameter that you specify beginning with `attribute:` will be included as an attribute to the resulting HTML tag that AutoMin produces. Take the examples below,
for instance:

This tag:

	{exp:automin:js attribute:type="text/javascript"}`

Outputs something similar to:

	<script src="/automin/7dc66e1b2104b40a9992a3652583f509.js?modified=8832678882928" type="text/javascript"></script>

And this tag:

	{exp:automin:css attribute:type="text/css" attribute:title="default" attribute:rel="stylesheet" attribute:media="screen, projection"}`

Outputs something similar to:

	<link href="/automin/55ed34446f3eac6f869f3fe5b375d311.css?modified=8832678882928" type="text/css" title="default" rel="stylesheet" media="screen, projection">

Config Overrides
---
Any option that is set in AutoMin's settings page can be overriden in your config file like so:

	// Automin config overrides
	$config['automin_automin_enabled'] = 'y';
	$config['automin_cache_enabled'] = 'y';
	$config['automin_compress_markup'] = 'y';
	$config['automin_cache_server_path'] = "{$_SERVER['DOCUMENT_ROOT']}/automin/";
	$config['automin_cache_url'] = '/automin/';

Compiling LESS
---
If you use AutoMin to compile your LESS source files, you DO NOT need to include the less.js parser file. AutoMin will parse your LESS source file and then compress the CSS output before sending it to your browser.

Variables
---
There are no variables for AutoMin. Anything inside the tag pair will be replaced with the final output.

A note about EE 1.X
---
I've stopped maintaining the EE 1 plugin. It still has support for the basics, but I will not be building any new features into the EE 1.X version of AutoMin.

Troubleshooting
---
Turn on template debugging in Admin->Output and Debugging to view detailed log messages. When reporting issues, please include your template log.

Make sure your cache directory is set in the module's settings and that the directory is writeable by PHP. In most cases, you'll need to assign that directory writable permissions. Usually this is 777.

If AutoMin breaks your CSS or JS code, make sure that your code contains no syntax errors. In your JS, you need to make sure that you always terminate JS statements with a semi-colon. Try running your source code through the relevant lint program for a validity check.

Make sure that your CSS images are web-root relative. Use URLs like: `url('/css/img/myimage.jpg')` instead of `url('img/myimage.jpg')`
