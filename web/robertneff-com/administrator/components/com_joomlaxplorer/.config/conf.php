<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

//------------------------------------------------------------------------------
// Configuration Variables
	global $mosConfig_absolute_path,$mosConfig_live_site;
	// login to use QuiXplorer: (true/false)
	$GLOBALS["require_login"] = false;
	
	// language: (en, de, es, fr, nl, ru)
	$GLOBALS["language"] = $mosConfig_lang;
	
	// the filename of the QuiXplorer script: (you rarely need to change this)
	$GLOBALS["script_name"] = "http://".$GLOBALS['__SERVER']['HTTP_HOST'].$GLOBALS['__SERVER']["PHP_SELF"];
	
	// allow Zip, Tar, TGz -> Only (experimental) Zip-support
	if( function_exists("gzcompress")) {
	  $GLOBALS["zip"] = $GLOBALS["tgz"] = true;
	}
	else {
	  $GLOBALS["zip"] = $GLOBALS["tgz"] = false;
	}
	// QuiXplorer version:
	$GLOBALS["version"] = "2.3";
//------------------------------------------------------------------------------
// Global User Variables (used when $require_login==false)
	
	// the home directory for the filemanager: (use '/', not '\' or '\\', no trailing '/')
	$GLOBALS["home_dir"] = $mosConfig_absolute_path;
	
	if( strstr( $GLOBALS["home_dir"], "/" ))
	  $GLOBALS["separator"] = "/";
	else
	  $GLOBALS["separator"] = "\\";
	  
	// the url corresponding with the home directory: (no trailing '/')
	$GLOBALS["home_url"] = $mosConfig_live_site;
	
	// show hidden files in QuiXplorer: (hide files starting with '.', as in Linux/UNIX)
	$GLOBALS["show_hidden"] = true;
	
	// filenames not allowed to access: (uses PCRE regex syntax)
	$GLOBALS["no_access"] = "^\.ht";
	
	// user permissions bitfield: (1=modify, 2=password, 4=admin, add the numbers)
	$GLOBALS["permissions"] = 7;
//------------------------------------------------------------------------------
/* NOTE:
	Users can be defined by using the Admin-section,
	or in the file ".config/.htusers.php".
	For more information about PCRE Regex Syntax,
	go to http://www.php.net/pcre.pattern.syntax
*/
//------------------------------------------------------------------------------
?>
