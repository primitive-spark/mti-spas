<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* This file show the customer information in a table
* while checking out
*
* @version $Id: checkout.customer_info.php,v 1.3 2005/09/29 20:02:18 soeren_nb Exp $
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

$db = new ps_DB;
$q  = "SELECT * FROM #__{vm}_user_info WHERE user_id='" . $_SESSION['auth']['user_id'] . "' ";
$q .= "AND address_type='BT'";
$db->query($q);
if (!$db->next_record()){echo "what the..."; }?>

<!-- Customer Information --> 
    <table border="0" cellspacing="0" cellpadding="2" width="100%">
		<?php if ($boo != "urns") {?>
        <tr class="sectiontableheader">
            <th colspan="2" align="left"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_BILLING_LBL ?></th>
        </tr>
        <tr>
		<?php } ?>
           <td nowrap="nowrap" width="10%" align="right" style="padding-right:10px" valign="top"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?> </td>
           <td width="90%"><strong>
           <?php
             $db->p("company");
           ?></strong>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right" style="padding-right:10px" valign="top"><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?> </td>
           <td width="90%"><strong>
		   <?php
             echo $db->f("first_name"). " " . $db->f("middle_name") ." " . $db->f("last_name"); ?></strong>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right" style="padding-right:10px" valign="top"><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?> </td>
           <td width="90%"><strong>
           <?php
             $db->p("address_1");
             echo "<br />";
             $db->p("address_2");
           ?></strong>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right" style="padding-right:10px" valign="top">&nbsp;</td>
           <td width="90%"><strong>
           <?php
             $db->p("city");
             echo ",";
             $db->p("state");
             echo " ";
             $db->p("zip");
             echo "<br /> ";
             $db->p("country");
           ?></strong>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right" style="padding-right:10px" valign="top"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?>: </td>
           <td width="90%"><strong>
           <?php
             $db->p("phone_1");
           ?></strong>
           </td>
        </tr>
        <tr>
           <td nowrap="nowrap" width="10%" align="right" style="padding-right:10px" valign="top"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL ?>: </td>
           <td width="90%"><strong>
           <?php
             $db->p("user_email");
           ?></strong>
           </td>
        </tr>
        <tr><td align="center" colspan="2">
			<?php
			if ($shit!=30){?>
				<a href="<?php echo $sess->purl( SECUREURL ."index.php?page=account.billing&next_page=$page"); ?>">
            (<?php echo $VM_LANG->_PHPSHOP_UPDATE_ADDRESS . "</a>";
			}
			?>
			
            </td>
        </tr>
    </table>
    <!-- customer information ends -->
