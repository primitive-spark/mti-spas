<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: shop.downloads.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
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
mm_showMyFileName( __FILE__ );

if( isset($_VERSION)) {
	$mainframe->setPageTitle( $VM_LANG->_PHPSHOP_DOWNLOADS_TITLE );
}

if ($perm->check("admin,storeadmin,shopper")) { ?>
	<div style="position:absolute; left:auto; top:375px">
	  <a href = "javascript:history.back()">
	    <img src="<?php echo IMAGEURL ?>ps_image/undo.png" alt="Back" title="Back"  height="32" width="32" border="0" align="left" />
	  </a>
	</div>
	<div style="position:absolute; left:auto; top:405px">
	  <a href = "javascript:history.back()">
	    Go Back
	  </a>
	</div>
	<center>
	<h3><?php echo $VM_LANG->_PHPSHOP_DOWNLOADS_TITLE ?></h3>
    <img src="<?php echo IMAGEURL ?>ps_image/downloads.gif" alt="downloads" border="0" align="center" />
    <br/>
    <br/>
    <?php

  	if (ENABLE_DOWNLOADS == '1') { ?>
	  	<form method="post" action="<?php echo $mm_action_url ?>index.php" name="downloadForm">
		  	<!-- <p><?php //echo $VM_LANG->_PHPSHOP_DOWNLOADS_INFO ?></p> -->
		  	<div align="center">
			    <input type="text" class="inputbox" value="<?php echo @$_GET['download_id'] ?>" size="32" name="download_id" />
			    <br /><br />
			    <input type="submit" onclick="if( document.downloadForm.download_id.value < 12) { alert('<?php echo _CONTACT_FORM_NC ?>');return false;} else return true;" class="button" value="<?php echo $VM_LANG->_PHPSHOP_DOWNLOADS_START ?>" onmouseup="history.back()"/>
			 </div>
		    <input type="hidden" name="func" value="downloadRequest" />
		    <input type="hidden" name="option" value="com_virtuemart" />
		    <input type="hidden" name="page" value="shop.downloads" />
		</form>
   		<?php
	}
}
else {
	$vmLogger->info( _NOT_AUTH."; "._DO_LOGIN );
}

?></center>