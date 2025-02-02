<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* PayPal IPN Result Checker
*
* @version $Id: checkout.result.php,v 1.5 2005/10/27 19:45:38 soeren_nb Exp $
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

if( !isset( $_REQUEST["order_id"] ) || empty( $_REQUEST["order_id"] )) {
	echo "Order ID is not set or emtpy!";
}
else {
	include( CLASSPATH. "payment/ps_paypal.cfg.php" );
	$order_id = intval( mosgetparam( $_REQUEST, "order_id" ));

	$q = "SELECT order_status FROM #__{vm}_orders WHERE ";
	$q .= "#__{vm}_orders.user_id= " . $auth["user_id"] . " ";
	$q .= "AND #__{vm}_orders.order_id= $order_id ";
	$db->query($q);
	if ($db->next_record()) {
		$order_status = $db->f("order_status");
		if($order_status == PAYPAL_VERIFIED_STATUS
      || $order_status == PAYPAL_PENDING_STATUS) {  ?> 
        <img src="<?php echo IMAGEURL ?>ps_image/button_ok.png" align="center" alt="Success" border="0" />
        <h2><?php echo $VM_LANG->_PHPSHOP_PAYPAL_THANKYOU ?></h2>
    
    <?php
        ################## JBRIDGE START ###########
        // set product new group (membership) level
        // requires payment module to set session variable 'new_user_level' set to #__core_acl_aro_groups.name
        global $acl, $database, $user_id;
        if ($_SESSION['new_user_level'] && $new_group_id = $acl->get_group_id($_SESSION['new_user_level'])) {
            $database->setQuery( "UPDATE #__users SET gid='$new_group_id' WHERE id='$user_id'" );
            $database->query();
            $database->setQuery( "SELECT aro_id FROM #__core_acl_aro WHERE value = '$user_id'" );
            $aro_id = $database->loadResult();
            $database->setQuery( "UPDATE #__core_acl_groups_aro_map SET group_id = '$new_group_id' WHERE aro_id = '$aro_id'" );
            $database->query();
            $_SESSION['new_user_level'] = '';
        }
    	################## JBRIDGE END ###########
      }
      else { ?>
        <img src="<?php echo IMAGEURL ?>ps_image/button_cancel.png" align="center" alt="Failure" border="0" />
        <span class="message"><? echo $VM_LANG->_PHPSHOP_PAYPAL_ERROR ?></span>
    
    <?php
    } ?>
    <br />
     <p><a href="index.php?option=com_virtuemart&page=account.order_details&order_id=<?php echo $order_id ?>">
     <?php echo $VM_LANG->_PHPSHOP_ORDER_LINK ?></a>
     </p>
    <?php
	}
	else {
		echo "Order not found!";
	}
}
?>