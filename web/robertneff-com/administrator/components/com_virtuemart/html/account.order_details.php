<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: account.order_details.php,v 1.7 2005/10/24 18:13:07 soeren_nb Exp $
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

require_once(CLASSPATH.'ps_checkout.php');
require_once(CLASSPATH.'ps_product.php');
$ps_product= new ps_product;

global $vendor_currency;


$print = mosgetparam( $_REQUEST, 'print', 0);
$order_id = mosgetparam( $_REQUEST, 'order_id', 0);

$db =& new ps_DB;
$q = "SELECT * FROM `#__{vm}_orders` WHERE ";
$q .= "#__{vm}_orders.user_id='" . $auth["user_id"] . "' ";
$q .= "AND #__{vm}_orders.order_id='$order_id'";
$db->query($q);

if ($db->next_record()) {

	// Get bill_to information
	$dbbt = new ps_DB;
	$q  = "SELECT * FROM `#__{vm}_order_user_info` WHERE order_id='" . $db->f("order_id") . "' ORDER BY address_type ASC";
	$dbbt->query($q);
	$dbbt->next_record();
	$user = $dbbt->record;

	/** Retrieve Payment Info **/
	$dbpm = new ps_DB;
	$q  = "SELECT * FROM `#__{vm}_payment_method`, `#__{vm}_order_payment`, `#__{vm}_orders` ";
	$q .= "WHERE #__{vm}_order_payment.order_id='$order_id' ";
	$q .= "AND #__{vm}_payment_method.payment_method_id=#__{vm}_order_payment.payment_method_id ";
	$q .= "AND #__{vm}_orders.user_id='" . $auth["user_id"] . "' ";
	$q .= "AND #__{vm}_orders.order_id='$order_id' ";
	$dbpm->query($q);
	$dbpm->next_record();

  if (empty( $print )) { ?>
  <a href="<?php $sess->purl(SECUREURL.'index.php?page=account.index'); ?>">
  <img src="<?php echo IMAGEURL ?>ps_image/undo.png" alt="Back" title="Back"  height="32" width="32" border="0" align="left" />
  </a>
    <center>
    <br />
    <script type="text/javascript">
    document.write('<a href="javascript:void window.open(\'<?php echo $mosConfig_live_site."/index2.php?page=account.order_details&order_id=$order_id&pop=1&option=com_virtuemart&print=1" ?>\', \'win2\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=yes,resizable=yes,width=640,height=480,directories=no,location=no\');">');
    </script>
    <noscript><a href="<?php echo $mosConfig_live_site."/index2.php?page=account.order_details&order_id=$order_id&pop=1&option=com_virtuemart&print=1" ?>" target="_blank"></noscript>
    <?php echo $VM_LANG->_PHPSHOP_CHECK_OUT_THANK_YOU_PRINT_VIEW ?>
    </a>
    <br /><br />
  <?php 
  }
?>
<div id="orderdetails-regular">
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td valign="top">
     <h2><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_LBL ?></h2>
     <p><?php
     echo $vendor_name . "<br />";
     echo $vendor_address . "<br />";
     echo $vendor_city . ", ";
     if (CAN_SELECT_STATES == '1')
     echo $vendor_state . " ";
        echo $vendor_zip; ?></p>
    </td>
    <td valign="top" width="10%" align="right"><?php echo $vendor_image; ?></td>
  </tr>
</table>
<div id="orderdetails-special-follow">
<?php
if ( $db->f("order_status") == "P" ) {
	// Copy the db object to prevent it gets altered
	$db_temp = ps_DB::_clone( $db );
 /** Start printing out HTML Form code (Payment Extra Info) **/ ?>
<table width="100%">
  <tr>
    <td width="100%" align="center">
    <?php 
    @include( CLASSPATH. "payment/".$dbpm->f("payment_class").".cfg.php" );

    echo DEBUG ? vmCommonHTML::getInfoField('Beginning to parse the payment extra info code...' ) : '';

    // Here's the place where the Payment Extra Form Code is included
    // Thanks to Steve for this solution (why make it complicated...?)
    if( eval('?>' . $dbpm->f("payment_extrainfo") . '<?php ') === false ) {
    	echo vmCommonHTML::getErrorField( "Error: The code of the payment method ".$dbpm->f( 'payment_method_name').' ('.$dbpm->f('payment_method_code').') '
    	.'contains a Parse Error!<br />Please correct that first' );
    }
      ?>
    </td>
  </tr>
</table>
<?php
$db = $db_temp;
}

/** END printing out HTML Form code (Payment Extra Info) **/
?>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
  <!-- begin customer information --> 
  <tr class="sectiontableheader"> 
    <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ACC_ORDER_INFO ?></th>
  </tr>
  <tr> 
    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_NUMBER?>:</td>
    <td><?php printf("%08d", $db->f("order_id")); ?></td>
  </tr>

  <tr> 
	<td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_DATE ?>:</td>
    <td><?php echo strftime( "%d %B %Y", $db->f("cdate")); ?></td>
  </tr>
  <tr> 
    <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_STATUS ?>:</td>
    <td><?php
    $q = "SELECT order_status_name,order_status_code FROM #__{vm}_order_status WHERE ";
    $q .= "order_status_code = '" . $db->f("order_status") . "'";
    $dbos = new ps_DB;
    $dbos->query($q);
    $dbos->next_record();
    echo $dbos->f("order_status_name");
    ?>

</td>
  </tr>
  <!-- End Customer Information --> 
  <!-- Begin 2 column bill-ship to --> 
  <tr class="sectiontableheader"> 
    <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_INFO_LBL ?></th>
  </tr>
  <tr valign="top"> 
    <td width="50%"> <!-- Begin BillTo -->
      <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr> 
          <td colspan="2"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_BILL_TO_LBL ?></strong></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?> :</td>
          <td><?php $dbbt->p("company"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?> :</td>
          <td><?php 
          $dbbt->p("first_name");
          echo " ";
          $dbbt->p("middle_name");
          echo " ";
          $dbbt->p("last_name");
         ?></td>
        </tr>
        <tr valign="top"> 
          <td><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?> :</td>
          <td><?php 
          $dbbt->p("address_1");
          echo "<br />";
          $dbbt->p("address_2");
         ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?> :</td>
          <td><?php $dbbt->p("city"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_STATE ?></td>
          <td><?php $dbbt->p("state"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?> :</td>
          <td><?php $dbbt->p("zip"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?> :</td>
          <td><?php $dbbt->p("country"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?> :</td>
          <td><?php $dbbt->p("phone_1");
          if( $dbbt->f("phone_2")!="" ) {
              echo ", ".$dbbt->f("phone_2"); } ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?> :</td>
          <td><?php $dbbt->p("fax"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL ?> :</td>
          <td><?php $dbbt->p("user_email"); ?></td>
        </tr>
    <!-- If you do not wish show a EXTRA FIELD add into condition "false && ".
         For example: if( false && $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 != "" ) -->
    <!-- EXTRA FIELD 1 - BEGIN - You can move this section into any other position of form. -->
        <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 != "" ) { ?>
          <tr>
            <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 ?> :</td>
            <td><?php $dbbt->p("extra_field_1"); ?></td>
          </tr>
        <?php } ?>
    <!-- EXTRA FIELD 1 - END -->
    <!-- EXTRA FIELD 2 - BEGIN - You can move this section into any other position of form. -->
        <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_2 != "" ) { ?>
          <tr>
            <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_2 ?> :</td>
            <td><?php $dbbt->p("extra_field_2"); ?></td>
          </tr>
        <?php } ?>
    <!-- EXTRA FIELD 2 - END -->
    <!-- EXTRA FIELD 3 - BEGIN - You can move this section into any other position of form. -->
        <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_3 != "" ) { ?>
          <tr>
            <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_3 ?> :</td>
            <td><?php $dbbt->p("extra_field_3"); ?></td>
          </tr>
        <?php } ?>
    <!-- EXTRA FIELD 3 - END -->
      </table>
      <!-- End BillTo --> </td>
    <td width="50%"> <!-- Begin ShipTo --> <?php
    // Get ship_to information
    $dbbt->next_record();
    $dbst =& $dbbt;

  ?> 
 <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr> 
          <td colspan="2"><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIP_TO_LBL ?></strong></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COMPANY ?> :</td>
          <td><?php $dbst->p("company"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_LIST_NAME ?> :</td>
          <td><?php 
          $dbst->p("first_name");
          echo " ";
          $dbst->p("middle_name");
          echo " ";
          $dbst->p("last_name");
         ?></td>
        </tr>
        <tr valign="top"> 
          <td><?php echo $VM_LANG->_PHPSHOP_ADDRESS ?> :</td>
          <td><?php 
          $dbst->p("address_1");
          echo "<br />";
          $dbst->p("address_2");
         ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CITY ?> :</td>
          <td><?php $dbst->p("city"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_STATE ?></td>
          <td><?php $dbst->p("state"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ZIP ?> :</td>
          <td><?php $dbst->p("zip"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_COUNTRY ?> :</td>
          <td><?php $dbst->p("country"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PHONE ?> :</td>
          <td><?php $dbst->p("phone_1");
          if( $dbst->f("phone_2")!="" )
              echo ", ".$dbst->f("phone_2"); ?></td>
        </tr>
        <tr> 
          <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_FAX ?> :</td>
          <td><?php $dbst->p("fax"); ?></td>
        </tr>
    <!-- If you do not wish show a EXTRA FIELD add into condition "false && ".
         For example: if( false && $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 != "" ) -->
    <!-- EXTRA FIELD 1 - BEGIN - You can move this section into any other position of form. -->
        <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 != "" ) { ?>
          <tr>
            <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_1 ?> :</td>
            <td><?php $dbst->p("extra_field_1"); ?></td>
          </tr>
        <?php } ?>
    <!-- EXTRA FIELD 1 - END -->
    <!-- EXTRA FIELD 2 - BEGIN - You can move this section into any other position of form. -->
        <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_2!= "" ) { ?>
          <tr>
            <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_2 ?> :</td>
            <td><?php $dbst->p("extra_field_2"); ?></td>
          </tr>
        <?php } ?>
    <!-- EXTRA FIELD 2 - END -->
    <!-- EXTRA FIELD 3 - BEGIN - You can move this section into any other position of form. -->
        <?php if( $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_3 != "" ) { ?>
          <tr>
            <td><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_3 ?> :</td>
            <td><?php $dbst->p("extra_field_3"); ?></td>
          </tr>
        <?php } ?>
    <!-- EXTRA FIELD 3 - END -->
      </table>
      <!-- End ShipTo --> 
      <!-- End Customer Information --> 
    </td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <?php if ( $PSHOP_SHIPPING_MODULES[0] != "no_shipping" && $db->f("ship_method_id")) { ?> 
  <tr> 
    <td colspan="2"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="1">
        
        <tr class="sectiontableheader"> 
          <th align="left"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_SHIPPING_LBL ?></th>
        </tr>
        <tr> 
          <td> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_CARRIER_LBL ?></strong></td>
                <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING_MODE_LBL ?></strong></td>
                <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PRICE ?>&nbsp;</strong></td>
              </tr>
              <tr> 
                <td><?php 
                $details = explode( "|", $db->f("ship_method_id"));
                echo $details[1];
                    ?>&nbsp;
                </td>
                <td><?php 
                echo $details[2];
                    ?>
                </td>
                <td><?php 
                echo $CURRENCY_DISPLAY->getFullValue($details[3]);
                    ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        
      </table>
    </td>
  </tr><?php
  }
 
  ?>
 </table>
 
</div>
 <div id="orderdetails-special">
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="sectiontableheader"> 
    <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_ITEM ?></th>
  </tr>
    <!-- BEGIN HACK EUGENE -->
  <tr>
    <td colspan="4">
<?php
$dbdl = new ps_DB;
/* Check if the order has been paid for */
if ($dbos->f("order_status_code") == ENABLE_DOWNLOAD_STATUS && ENABLE_DOWNLOADS) {

		echo "To download an eBook you purchased, click the product name.<br /><i>&nbsp;::eBook Download Notice::</i><br /> &nbsp; - ";

		echo($VM_LANG->_PHPSHOP_DOWNLOADS_SEND_MSG_3.DOWNLOAD_MAX." times.<br /> &nbsp; - The \"Qty\" column shows the remaining number of download attempts for each eBook.<br /> &nbsp; - ");

		$expire = ((DOWNLOAD_EXPIRE / 60) / 60) / 24;
		echo(str_replace("{expire}", $expire, $VM_LANG->_PHPSHOP_DOWNLOADS_SEND_MSG_4));
		echo "<br /><br />";

}
?>
    </td>
  </tr>
  <!-- END HACK EUGENE -->
  <tr> 
    <td colspan="2"> 
      <table width="100%" cellspacing="0" cellpadding="2" border="0">
        <tr align="left"> 
          <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_QTY ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_NAME ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SKU ?></th>
          <th><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PRICE ?></th>
          <th align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL ?>&nbsp;&nbsp;&nbsp;</th>
        </tr>
        <?php 
        $dbcart = new ps_DB;
        $q  = "SELECT * FROM #__{vm}_order_item ";
        $q .= "WHERE #__{vm}_order_item.order_id='$order_id' ";
        $dbcart->query($q);
        $subtotal = 0;
        $dbi = new ps_DB;
        while ($dbcart->next_record()) {

        	/* BEGIN HACK EUGENE */
        	/*HACK SCOTT had to retest order status else unpaid were able to download*/
        	if ($dbos->f("order_status_code") == ENABLE_DOWNLOAD_STATUS && ENABLE_DOWNLOADS) {
        		/* search for download record that corresponds to this order item */
        		$q = "SELECT `download_id` FROM #__{vm}_product_download WHERE";
        		$q .= " `order_id`=" . intval($vars["order_id"]);
        		$q .= " AND `product_id`=". intval($dbcart->f("product_id"));
        		$dbdl->query($q);

        	}
        	/* END HACK EUGENE */

        	$product_id = null;
        	$dbi->query( "SELECT product_id FROM #__{vm}_product WHERE product_sku='".$dbcart->f("order_item_sku")."'");
        	$dbi->next_record();
        	$product_id = $dbi->f("product_id" );
?> 
        <tr align="left"> 
          <td><?php
		  	/* BEGIN HACK ERIK */
			/* search for download record that corresponds to this order item */
			echo "&nbsp;";
			$dbdl2 = new ps_DB;
			$q = "SELECT `download_max` FROM #__{vm}_product_download WHERE";
			$q .= " `order_id`=" . intval($vars["order_id"]);
			$q .= " AND `product_id`=". intval($dbcart->f("product_id"));
			$dbdl2->query($q);
			if ($dbdl2->next_record()){
				$dbdl2->p("download_max");
			}
			else {
				echo "0";
			}
			/* END HACK ERIK */ ?>
		  </td>
          <td><?php 
              if ($dbdl->next_record()) {
        			/* hyperlink downloadable order item */

        			$url = $mosConfig_live_site."/index.php?option=com_virtuemart&page=shop.downloads&download_id=".$dbdl->f("download_id");
        			echo '<table width="100%" border="0" cellspacing="0" cellpadding="1"><tr>';
					echo '<td><a href="'.$url.'"><img src="'.$mosConfig_live_site.'/media/download_icon.gif" border=0 height=25px width=25px /></a>';
					echo '</td><td valign="middle"><a href="'.$url.'">Download Now - ';
        			echo $dbcart->p("order_item_name");
        			echo '</a></td></tr></table>';
				}
        		else {
		        	if( !empty( $product_id )) {
	        			echo '<table width="100%" border="0" cellspacing="0" cellpadding="1"><tr>';
						echo '<td><img src="'.$mosConfig_live_site.'/media/download_icon_no.gif" border=0 height=25px width=25px /></td>';
		          		echo '<td valign="middle"><a href="'.$sess->url( $mm_action_url."index.php?page=shop.product_details&product_id=$product_id") .'" title="'.$dbcart->f("order_item_name").'">Download Expired - ';
		          	}
		          	$dbcart->p("order_item_name");
		          	echo " <div style=\"font-size:smaller;\">" . $dbcart->f("product_attribute") . "</div>";
		          	if( !empty( $product_id )) {
		          		echo "</a></td></tr></table>";
		          	}
        		}
		?>
          </td>
          <td><?php $dbcart->p("order_item_sku"); ?></td>
          <td><?php /*
		$price = $ps_product->get_price($dbcart->f("product_id"));
		$item_price = $price["product_price"]; */
		if( $auth["show_price_including_tax"] ){
			$item_price = $dbcart->f("product_final_price");
		}
		else {
			$item_price = $dbcart->f("product_item_price");
		}
		echo $CURRENCY_DISPLAY->getFullValue($item_price);

           ?></td>
          <td align="right"><?php $total = $dbcart->f("product_quantity") * $item_price; 
          $subtotal += $total;
          echo $CURRENCY_DISPLAY->getFullValue($total);
           ?>&nbsp;&nbsp;&nbsp;</td>
        </tr><?php
        }
?> 
        <tr> 
          <td colspan="4" align="right">&nbsp;&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SUBTOTAL ?> :</td>
          <td align="right"><?php echo $CURRENCY_DISPLAY->getFullValue($subtotal) ?>&nbsp;&nbsp;&nbsp;</td>
        </tr>
<?php 
/* COUPON DISCOUNT */
$coupon_discount = $db->f("coupon_discount");
$order_discount = $db->f("order_discount");

if( PAYMENT_DISCOUNT_BEFORE == '1') {
	if (($db->f("order_discount") != 0)) {
?>
          <tr>
              <td colspan="4" align="right"><?php 
              if( $db->f("order_discount") > 0)
              echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT;
              else
              echo $VM_LANG->_PHPSHOP_FEE;
                ?>:
              </td> 
              <td align="right"><?php
              if ($db->f("order_discount") > 0 )
              echo "- ".$CURRENCY_DISPLAY->getFullValue(abs());
              elseif ($db->f("order_discount") < 0 )
                 echo "+ ".$CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount"))); ?>
              &nbsp;&nbsp;&nbsp;</td>
          </tr>
        
        <?php 
	}
	if( $coupon_discount > 0 ) {
?>
        <tr>
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_COUPON_DISCOUNT ?>:
          </td> 
          <td align="right"><?php
            echo "- ".$CURRENCY_DISPLAY->getFullValue( $coupon_discount ); ?>&nbsp;&nbsp;&nbsp;
          </td>
        </tr>
<?php
	}
}
?>        
        <tr> 
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_SHIPPING ?> :</td>
          <td align="right"><?php 
          $shipping_total = $db->f("order_shipping");
          if ($auth["show_price_including_tax"] == 1)
          $shipping_total += $db->f("order_shipping_tax");
          echo $CURRENCY_DISPLAY->getFullValue($shipping_total);

            ?>&nbsp;&nbsp;&nbsp;</td>
        </tr>
  <?php
  $tax_total = $db->f("order_tax") + $db->f("order_shipping_tax");
  if ($auth["show_price_including_tax"] == 0) {
  ?>
        <tr> 
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL_TAX ?> :</td>
          <td align="right"><?php 

          echo $CURRENCY_DISPLAY->getFullValue($tax_total);

            ?>&nbsp;&nbsp;&nbsp;</td>
        </tr>
<?php
  }
  if( PAYMENT_DISCOUNT_BEFORE != '1') {
  	if (($db->f("order_discount") != 0)) {
?>
          <tr>
              <td colspan="4" align="right"><?php 
              if( $db->f("order_discount") > 0)
              echo $VM_LANG->_PHPSHOP_PAYMENT_METHOD_LIST_DISCOUNT;
              else
              echo $VM_LANG->_PHPSHOP_FEE;
                ?>:
              </td> 
              <td align="right"><?php
              if ($db->f("order_discount") > 0 )
              echo "- ".$CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount")));
              elseif ($db->f("order_discount") < 0 )
                 echo "+ ".$CURRENCY_DISPLAY->getFullValue(abs($db->f("order_discount"))); ?>
              &nbsp;&nbsp;&nbsp;</td>
          </tr>
        
        <?php 
  	}
  	if( $coupon_discount > 0 ) {
?>
        <tr>
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_COUPON_DISCOUNT ?>:
          </td> 
          <td align="right"><?php
            echo "- ".$CURRENCY_DISPLAY->getFullValue( $coupon_discount ); ?>&nbsp;&nbsp;&nbsp;
          </td>
        </tr>
<?php
  	}
  }
?>      <tr> 
          <td colspan="3" align="right">&nbsp;</td>
          <td colspan="2" align="right"><hr/></td>
        </tr>
        <tr> 
          <td colspan="4" align="right">
          <strong><?php echo $VM_LANG->_PHPSHOP_CART_TOTAL .": "; ?></strong>
          </td>
          
          <td align="right"><strong><?php  
          $total = $db->f("order_total");
          echo $CURRENCY_DISPLAY->getFullValue($total);
          ?></strong>&nbsp;&nbsp;&nbsp;</td>
        </tr>
  <?php
  if ($auth["show_price_including_tax"] == 1) {
  ?>
        
        <tr> 
          <td colspan="3" align="right">&nbsp;</td>
          <td colspan="2" align="right"><hr/></td>
        </tr>
        <tr> 
          <td colspan="4" align="right"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_TOTAL_TAX ?> :</td>
          <td align="right"><?php 

          echo $CURRENCY_DISPLAY->getFullValue($tax_total);

            ?>&nbsp;&nbsp;&nbsp;</td>
        </tr>
<?php
  }
  ?>
      </table>
    </td>
  </tr>
 </table>
  <!-- End Order Items Information --> 
</div>
<div id="orderdetails-other">
<!-- Begin Payment Information --> 
<table width="100%">
      <tr class="sectiontableheader"> 
        <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PAYINFO_LBL ?></th>
      </tr>
      <tr> 
        <td width="20%"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_PAYMENT_LBL ?> :</td>
        <td><?php $dbpm->p("payment_method_name"); ?> </td>
      </tr>
	  <?php
	  require_once(CLASSPATH.'ps_payment_method.php');
	  $ps_payment_method = new ps_payment_method;
	  $payment = $dbpm->f("payment_method_id");

	  if ($ps_payment_method->is_creditcard($payment)) {

	  	// DECODE Account Number
	  	$dbaccount = new ps_DB;
	  	$q = "SELECT DECODE(\"". $dbpm->f("order_payment_number")."\",\"".ENCODE_KEY."\") as account_number FROM #__{vm}_order_payment WHERE order_id='".$order_id."'";
	  	$dbaccount->query($q);
        $dbaccount->next_record(); ?>
      <tr> 
        <td width="10%"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ACCOUNT_NAME ?> :</td>
        <td><?php $dbpm->p("order_payment_name"); ?> </td>
      </tr>
      <tr> 
        <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_ACCOUNT_NUMBER ?> :</td>
        <td> <?php echo ps_checkout::asterisk_pad($dbaccount->f("account_number"),4);
    		?>
	    </td>
      </tr>
      <tr> 
        <td><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EXPIRE_DATE ?> :</td>
        <td><?php echo strftime("%m - %Y", $dbpm->f("order_payment_expire")); ?> </td>
      </tr>
	  <?php } ?>

</table>
<!-- end payment information --> 


<?php
    /** Print out the customer note **/
    if ( $db->f("customer_note") ) {
    ?>
    <table width="100%">
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="sectiontableheader">
        <th align="left" colspan="2"><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_CUSTOMER_NOTE ?></th>
      </tr>
      <tr>
        <td colspan="2">
         <?php echo nl2br($db->f("customer_note"))."<br />"; ?>
       </td>
      </tr>
    </table>
    <?php
    }

} /* End of security check */
else {
	echo '<h4>'._LOGIN_TEXT .'</h4><br/>';
	include(PAGEPATH.'checkout.login_form.php');
	echo '<br/><br/>';
} ?>
</div>
</div>
</center>