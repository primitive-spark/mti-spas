<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: admin.virtuemart.php,v 1.14 2005/11/22 18:30:07 soeren_nb Exp $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

defined( '_PSHOP_ADMIN' ) or define( '_PSHOP_ADMIN', '1' );

$no_menu = mosGetParam( $_REQUEST, 'no_menu', 0 );
global $VM_LANG;
/*** INSTALLER SECTION ***/
include( $mosConfig_absolute_path.'/administrator/components/com_virtuemart/install.virtuemart.php' );

if (isset($_REQUEST['install_type']) && file_exists( $mosConfig_absolute_path.'/administrator/components/'.$option.'/install.php' )) {
	virtuemart_is_installed();
	include( $mosConfig_absolute_path.'/administrator/components/'.$option.'/install.php' );

	/** can be update and newinstall **/
	$install_type = mosgetparam( $_REQUEST, 'install_type', 'newinstall' );

	/** true or false **/
	$install_sample_data = mosgetparam( $_GET, 'install_sample_data', false );

	installvirtuemart( $install_type, $install_sample_data );
	$error = "";
	$page = "store.index";

	$installfile = dirname( __FILE__ ) . "/install.php";
	if( !@unlink( $installfile ) ) {
		echo "<br /><span class=\"message\">Something went wrong when trying to delete the file <strong>install.php</strong>!<br />";
		echo "You'll have to delete the file manually before being able to use VirtueMart!</span>";
	}

}
elseif( file_exists( $mosConfig_absolute_path.'/administrator/components/'.$option.'/install.php' )) {
	
	com_install();
	exit();
	
}
/*** END INSTALLER ***/


/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/'.$option.'/virtuemart_parser.php' );

$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
$limitstart = $mainframe->getUserStateFromRequest( "view{$page}limitstart", 'limitstart', 0 );

if (defined('_DONT_VIEW_PAGE') && !isset($install_type) ) {
	echo "<script type=\"text/javascript\">alert('$error. Your permissions: ".$_SESSION['auth']['perms']."')</script>\n";
}


// renew Page-Information
$my_page= explode ( '.', $page );
$modulename = $my_page[0];
$pagename = $my_page[1];

$_SESSION['last_page'] = $page;

if( $no_menu != 1 ) {
	include(ADMINPATH.'header.php');
}
// Include the Stylesheet
echo '<link rel="stylesheet" href="components/'.$option.'/admin.styles.css" type="text/css" />';
echo '<link href="../components/'.$option.'/css/shop.css" type="text/css" rel="stylesheet" media="screen, projection" />';
echo '<script type="text/javascript" src="../components/'.$option.'/js/functions.js"></script>';

// Load PAGE
include( PAGEPATH.$modulename.".".$pagename.".php" );
include_once( ADMINPATH. 'version.php' );

echo '<br style="clear:both;"/><div class="smallgrey" align="center"><a href="http://virtuemart.net/index.php?option=com_versions&amp;catid=1&amp;myVersion='.@$VMVERSION->RELEASE.'" target="_blank">Check for latest VirtueMart version</a></div>';

if( DEBUG == '1' ) {
	// Load PAGE
	include( PAGEPATH."shop.debug.php" );
}
if( defined( 'vmToolTipCalled')) {
	echo '<script language="Javascript" type="text/javascript" src="'. $mosConfig_live_site.'/components/'.$option.'/js/wz_tooltip.js"></script>';
}
?>
