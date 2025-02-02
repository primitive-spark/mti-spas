<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: order.order_list.php,v 1.6.2.1 2006/01/15 19:37:05 soeren_nb Exp $
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
* Modified By: Deneb
* 
*/
mm_showMyFileName( __FILE__ );
global $mosConfig_locale;
setlocale(LC_TIME,$mosConfig_locale);

$show = mosGetParam( $_REQUEST, "show", "" );
$form_code = "";
require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

if (!empty($keyword)) {
		$list  = "SELECT * FROM #__{vm}_orders, #__{vm}_user_info, #__users "; //modded by Deneb
		$count = "SELECT  count(*) as num_rows FROM #__{vm}_orders, #__{vm}_user_info "; //bugfix by Deneb - Task #529
		$q  = "WHERE (#__{vm}_orders.order_id LIKE '%$keyword%' "; //modded by Deneb
        $q .= "OR #__{vm}_orders.order_status LIKE '%$keyword%' ";
		$q .= "OR email LIKE '%$keyword%' "; //added by Deneb
		$q .= "OR username LIKE '%$keyword%' "; //added by Deneb
		$q .= "OR first_name LIKE '%$keyword%' ";
        $q .= "OR last_name LIKE '%$keyword%' ";
        $q .= ") ";
		$q .= "AND #__{vm}_user_info.user_id=#__users.id "; //added by Deneb
        $q .= "AND #__{vm}_orders.user_id=#__{vm}_user_info.user_id ";
        $q .= "AND #__{vm}_orders.vendor_id='".$_SESSION['ps_vendor_id']."' ";
		$q .= "AND #__{vm}_user_info.address_type='BT' "; //added by Deneb
        $q .= "ORDER BY #__{vm}_orders.cdate DESC ";
        $list .= $q . " LIMIT $limitstart, " . $limit;
		$count .= $q;   
}
else {
	$keyword = "";
	$q = "";
	$list  = "SELECT * FROM #__{vm}_orders, #__{vm}_user_info "; //modded by Deneb
	$count = "SELECT count(*) as num_rows FROM #__{vm}_orders, #__{vm}_user_info "; //added by Deneb
	$q  = "LEFT JOIN #__users ON #__{vm}_user_info.user_id = #__users.id "; //added by Deneb
	$q .= "WHERE #__{vm}_orders.user_id=#__{vm}_user_info.user_id "; //added by Deneb
	$q .= "AND #__{vm}_orders.vendor_id='".$_SESSION['ps_vendor_id']."' "; //modded by Deneb
	$q .= "AND #__{vm}_user_info.address_type='BT' "; //added by Deneb
	if (!empty($show)) {  //added '{' by Deneb
		$q .= "AND order_status = '$show' ";
	} //added by Deneb
	$q .= "ORDER BY #__{vm}_orders.cdate DESC ";
	$list .= $q . " LIMIT $limitstart, " . $limit;
	$count .= $q;   
}
$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");
  
// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_ORDER_LIST_LBL, IMAGEURL."ps_image/orders.gif", $modulename, "order_list");

?>
<div align="center">
<?php
$navi_db = new ps_DB;
$q = "SELECT order_status_code, order_status_name ";
$q .= "FROM #__{vm}_order_status WHERE vendor_id = '$ps_vendor_id'";
$navi_db->query($q);
while ($navi_db->next_record()) {  ?> 
  <a href="<?php $sess->purl($_SERVER['PHP_SELF']."?page=$modulename.order_list&show=".$navi_db->f("order_status_code")) ?>">
  <b><?php echo $navi_db->f("order_status_name")?></b></a>
      | 
<?php 
} 
?>
    <a href="<?php $sess->purl($_SERVER['PHP_SELF']."?page=$modulename.order_list&show=")?>"><b>
    <?php echo $VM_LANG->_PHPSHOP_ALL ?></b></a>
</div>
<br />
<?php 

$listObj->startTable();

// these are the columns in the table
$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "width=\"20\"",
					$VM_LANG->_PHPSHOP_ORDER_LIST_ID => '',
					$VM_LANG->_PHPSHOP_CHECK_OUT_THANK_YOU_PRINT_VIEW => '',
					$VM_LANG->_PHPSHOP_USER_LIST_USERNAME => "",
					$VM_LANG->_PHPSHOP_USER_LIST_FULL_NAME => "",
					$VM_LANG->_PHPSHOP_ORDER_LIST_CDATE => '',
					$VM_LANG->_PHPSHOP_ORDER_LIST_MDATE => '',
					$VM_LANG->_PHPSHOP_ORDER_LIST_STATUS => '',
					$VM_LANG->_PHPSHOP_UPDATE => '',
					$VM_LANG->_PHPSHOP_ORDER_LIST_TOTAL => '',
					_E_REMOVE => "width=\"5%\""
				); //modded by Deneb - add: username, full name
$listObj->writeTableHeader( $columns );
 
$db->query($list);
$i = 0;
while ($db->next_record()) { 
    
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->rowNumber( $i ) );
		
	// The Checkbox
	$listObj->addCell( mosHTML::idBox( $i, $db->f("order_id"), false, "order_id" ) );
	
	$url = $_SERVER['PHP_SELF']."?page=$modulename.order_print&limitstart=$limitstart&keyword=$keyword&order_id=". $db->f("order_id");
	$tmp_cell = "<a href=\"" . $sess->url($url) . "\">".sprintf("%08d", $db->f("order_id"))."</a><br />";
	$listObj->addCell( $tmp_cell );
	
	$details_url = $sess->url( $_SERVER['PHP_SELF']."?page=order.order_printdetails&amp;order_id=".$db->f("order_id")."&amp;no_menu=1");
    $details_url = stristr( $_SERVER['PHP_SELF'], "index2.php" ) ? str_replace( "index2.php", "index3.php", $details_url ) : str_replace( "index.php", "index2.php", $details_url );
	
    $details_link = "&nbsp;<a href=\"javascript:void window.open('$details_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=yes,resizable=yes,width=800,height=800,directories=no,location=no');\">"; //modded by BMS - make window bigger & show menu bar
    $details_link .= "<img src=\"$mosConfig_live_site/images/M_images/printButton.png\" align=\"center\" height=\"16\" width=\"16\" border=\"0\" /></a>"; 
    $listObj->addCell( $details_link );
	
	//username - added by Deneb
	$user_url = $_SERVER['PHP_SELF']."?option=com_users&amp;task=editA&amp;hidemainmenu=1&amp;id=".$db->f("user_id") ;
	//$user_link = "<a href=\"" . $user_url . "\" title=\"".$VM_LANG->_PHPSHOP_USER_FORM_LBL."\">". $dbu->f("username") . "</a>";
	$user_link = vmPopupLink( $user_url, $db->f("username"), $popupWidth=800, $popupHeight=800, $target='_blank', $VM_LANG->_PHPSHOP_USER_FORM_LBL );
	$listObj->addCell( $user_link );
	//username - end added by Deneb
	
	//full name - added by Deneb
	$shopper_fulname = ucwords ($db->f("last_name") . ", ". $db->f("first_name") . " ". $db->f("middle_name"));
	$shopper_url = $sess->url( $_SERVER['PHP_SELF']."?page=admin.user_form&amp;hidemainmenu=1&amp;user_id=".$db->f("user_id") );
	//$shopper_link = "<a href=\"" . $shopper_url . "\" title=\"".$VM_LANG->_PHPSHOP_SHOPPER_FORM_LBL."\">". $shopper_fulname . "</a>";
	$shopper_link = vmPopupLink( $shopper_url, $shopper_fulname, $popupWidth=800, $popupHeight=800, $target='_blank', $VM_LANG->_PHPSHOP_SHOPPER_FORM_LBL );
	$listObj->addCell( $shopper_link );
	//full name - end added by Deneb
	
	$listObj->addCell( strftime("%d-%b-%y %H:%M", $db->f("cdate")));
    $listObj->addCell( strftime("%d-%b-%y %H:%M", $db->f("mdate")));
	
	$listObj->addCell( $ps_order_status->getOrderStatus($db->f("order_status"), "onchange=\"document.adminForm$i.order_status.selectedIndex = this.selectedIndex;document.adminForm$i.changed.value='1'\""));
	
	$listObj->addCell( '<input type="checkbox" class="inputbox" onclick="if(this.checked==true) {document.adminForm'. $i .'.notify_customer.value = \'Y\';} else {document.adminForm'. $i .'.notify_customer.value = \'N\';}" value="Y" />'
						.$VM_LANG->_PHPSHOP_ORDER_LIST_NOTIFY .'<br />
					<input type="button" class="button" onclick="if(document.adminForm'. $i .'.changed.value!=\'1\') { alert(\''. $VM_LANG->_PHPSHOP_ORDER_LIST_NOTIFY_ERR .'\'); return false;} else adminForm'.$i.'.submit();" name="Submit" value="Update Status" />' );

	$listObj->addCell( $CURRENCY_DISPLAY->getFullValue($db->f("order_total")));
	
	$form_code .= '<form style="float:left;" method="post" action="'. $_SERVER['PHP_SELF'] .'" name="adminForm'. $i .'">';
	$form_code .= $ps_order_status->getOrderStatus($db->f("order_status"), "style=\"visibility:hidden;\" onchange=\"document.adminForm$i.changed.value='1'\"");
	$form_code .= '<input type="hidden" class="inputbox" name="notify_customer" value="N" />
		<input type="hidden" name="page" value="order.order_list" />
		<input type="hidden" name="func" value="orderStatusSet" />
		<input type="hidden" name="changed" value="0" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="order_id" value="'. $db->f("order_id") .'" />
		<input type="hidden" name="current_order_status" value="'. $db->f("order_status").'" />
		</form>';
    
	$listObj->addCell( $ps_html->deleteButton( "order_id", $db->f("order_id"), "orderDelete", $keyword, $limitstart ) );

	$i++; 
}
$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword, "&show=$show" );

echo $form_code;
?>


