<?php
/*
* DOCMan 1.3.0 for Mambo 4.5.1 CMS
* @version $Id: documents.html.php,v 1.50 2005/08/05 22:51:40 johanjanssens Exp $
* @package DOCMan 1.3.0
* @copyright (C) 2003 - 2005 The DOCMan Development Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Oficial website: http://www.mambodocman.com/
*/
/**
* * edited by mic
*/

defined('_VALID_MOS') or die('Direct access to this location is not allowed.');

if (defined('_DOCMAN_HTML_DOCUMENTS')) {
    return;
} else {
    define('_DOCMAN_HTML_DOCUMENTS', 1);
} 

class HTML_DMDocuments 
{
    function showDocuments($rows, $lists, $search, $pageNav, $number_pending, $number_unpublished, $view_type = 1)
    {
        global $database, $my, $_DOCMAN;
        global $mosConfig_live_site;
        ?>

        <form action="index2.php" method="post" name="adminForm">
        <table class="adminheading">
            <tr>
                <th>DOCMan - <?php echo _DML_DOCS;?></th>
                <td><?php echo _FILTER;?></td>
                <td><input class="text_area" type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" /></td>
                <td><?php echo $lists['catid'];?></td>
            </tr>
        </table>

        <table cellpadding="1" cellspacing="0" border="0" width="100%">
            <tr>
                <td>
                    <p class="small">
      	<?php if ($number_pending > 0) {
            echo "[$number_pending " . _DML_DOCS_NOT_APPROVED . "]";
        } 
        if ($number_unpublished > 0) {
            echo "[$number_unpublished " . _DML_DOCS_NOT_PUBLISHED . "]";
        } 
        if ($number_unpublished < 1 && $number_pending < 1) {
            echo "[" . _DML_NO_PENDING_DOCS . "]";
        } 
        ?>
                    </p>
				</td>
            </tr>
        </table>
		<br />
        <table class="adminlist">
          <tr>
            <th width="2%" align="left" >
            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows);?>);" />
            </th>
            <th width="15%" align="left">
            <a href="index2.php?option=com_docman&section=documents&sort=name"><?php echo _DML_NAME;?></a>
            </th>
            <th width="15%" align="left" >
            <a href="index2.php?option=com_docman&section=documents&sort=filename"><?php echo _DML_FILE;?></a>
            </th>
            <th width="15%" align="left">
            <a href="index2.php?option=com_docman&section=documents&sort=catsubcat"><?php echo _DML_CATEGORY;?></a>
            </th>
            <th width="10%" align="center">
            <a href="index2.php?option=com_docman&section=documents&sort=date"><?php echo _DML_DATE;?></a>
            </th>
            <th width="10%">
            <?php echo _DML_OWNER;?>
            </th>
            <th width="5%">
            <?php echo _DML_PUBLISHED;?>
            </th>
            <th width="5%">
            <?php echo _DML_APPROVED;?>
            </th>
            <th width="5%">
            <?php echo _DML_SIZE;?>
            </th>
            <th width="5%">
            <?php echo _DML_HITS;?>
            </th>
            <th width="5%" nowrap="nowrap">
            <?php echo _DML_CHECKED_OUT;?>
            </th>
          </tr>
          <?php
        $k = 0;
        for ($i = 0, $n = count($rows);$i < $n;$i++) {
            $row = &$rows[$i];
            $task = $row->published ? 'unpublish' : 'publish';
            $img = $row->published ? 'publish_g.png' : 'publish_x.png';
            $alt = $row->published ? _DML_PUBLISHED : _DML_UNPUBLISH ;
            
            $file = new DOCMAN_File($row->dmfilename, $_DOCMAN->getCfg('dmpath'));

            ?><tr class="row <?php echo $k;?>">
                <td width="20">
				<?php echo mosHTML::idBox($i, $row->id, ($row->checked_out && $row->checked_out != $my->id));?>
				</td>
				<td width="15%">
			<?php
            if ($row->checked_out && ($row->checked_out != $my->id)) {
            ?>
					<?php echo $row->dmname;?>
					&nbsp;[ <i><?php echo _DML_CHECKED_OUT;?></i> ]
			<?php
            } else {
            ?>
					<a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','edit')">
					<?php echo $row->dmname;?>
					</a>
					<?php
            } 
            ?>
				</td>
                <td>
                <?php if ($file->exists()) {?>
                    <a href="index2.php?option=com_docman&section=documents&task=download&bid=<?php echo $row->id;?>" target="_blank"><?php echo $row->dmfilename;
                ?></a>
               	<?php
            } else {
                echo _DML_FILE_MISSING;
            } 
            ?>
            	</td>
            	<td width="15%"><?php echo $row->treename ?></td>
               	<td width="10%" align="center"><?php echo mosFormatDate($row->dmdate_published); ?></td>
               	<td align="center"><?php echo DOCMAN_Utils::getUserName($row->dmowner); ?></td>
                <td width="10%" align="center">
					<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
					<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
			<?php
            if (!$row->approved) {
                ?>
	            	<td width="5%" align="center"><a href="#approve" onClick="return listItemTask('cb<?php echo $i;?>','approve')"><img src="images/publish_x.png" border=0 alt="approve" /></a></td>
	            <?php
            } else {
                ?>
	            	<td width="5%" align="center"><img src="images/tick.png" /></td>
	            <?php
            } 
            ?>
	            <td width="5%" align="center">
	       	<?php
            if ($file->exists()) {
                echo $file->getSize();
            } 
            ?>
            </td>
            <td width="5%" align="center"><?php echo $row->dmcounter;?></td>
			<?php
            if ($row->checked_out) {
                ?>
                	<td width="5%" align="center"><?php echo $row->editor;?></td>
            	<?php
            } else {
                ?>
                <td width="5%" align="center">---</td>
                <?php
            } 

            ?></tr><?php
            $k = 1 - $k;
        } 
        ?>
      </table>
      <?php echo $pageNav->getListFooter();?>

      <input type="hidden" name="option" value="com_docman" />
      <input type="hidden" name="section" value="documents" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      </form>

   	  <?php
      require_once ("../components/com_docman/footer.php");
    } 

    function editDocument(&$row, &$lists, $last, $created, &$params)
    {
        global $database, $mosConfig_offset, $mosConfig_live_site, $mosConfig_locale;
        
        $tabs = new mosTabs(0);
        mosMakeHtmlSafe($row); 
        
        $tmp_locale = substr($mosConfig_locale, 0, 2); 
        // now try to get the locale data
        if (file_exists('../includes/js/calendar/lang/calendar-' . $tmp_locale . '.js')) {
            $tmp_cal_source = $mosConfig_live_site . '/includes/js/calendar/lang/calendar-' . $tmp_locale . '.js';
        } else $tmp_cal_source = $mosConfig_live_site . '/includes/js/calendar/lang/calendar-en.js'; 
    
        ?>
    	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
    	<script language="JavaScript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js" type="text/javascript"></script>
    	<script language="JavaScript" type="text/javascript">
    		<!--
    		function submitbutton(pressbutton) {
    		  var form = document.adminForm;
    		  if (pressbutton == 'cancel') {
    			submitform( pressbutton );
    			return;
    		  }
    		  // do field validation
			<?php dmHTML::docEditFieldsJS();/* Include all edits at once */?>
			if ( $msg != "" ){
					$msghdr = "<?php echo _DML_ENTRY_ERRORS;?>";
					$msghdr += '\n=================================';
					alert( $msghdr+$msg+'\n' );
			}else {
			<?php
        	getEditorContents('editor1', 'dmdescription');
        	?>
				submitform( pressbutton );
				}
			}  	
			//--> end submitbutton
    	</script>

    	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $mosConfig_live_site;?>/includes/js/calendar/calendar-mos.css" title="green" />

    	<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/calendar/calendar.js"></script>
    	<script type="text/javascript" src="<?php echo $tmp_cal_source;?>"></script>
    	
    	<style>
			select option.label { background-color: #EEE; border: 1px solid #DDD; color : #333; }
		</style>

    	<table class="adminheading">
    		<tr>
    			<th><?php echo $row->id ? _DML_EDIT : _DML_ADD;?> :: <?php echo _DML_DOCUMENT;?></th>
    		</tr>
    	</table>

    	<form action="index2.php" method="post" name="adminForm" class="adminform" id="dm_formedit">
		<?php
        $tabs->startPane("content-pane");
        $tabs->startTab(_DML_DOC, "document-page");
        
		HTML_DMDocuments::_showTabDocument($row, $lists, $last, $created);
		 
        $tabs->endTab();
        $tabs->startTab(_DML_TAB_PERMISSIONS, "ownership-page");
        
        HTML_DMDocuments::_showTabPermissions($row, $lists, $last, $created);
       
        $tabs->endTab();
        $tabs->startTab(_DML_TAB_LICENSE, "license-page");
        
        HTML_DMDocuments::_showTabLicense($row, $lists, $last, $created);
            
        if(isset($params)) :
        $tabs->endTab();
        $tabs->startTab(_DML_TAB_DETAILS, "details-page");
        
        HTML_DMDocuments::_showTabDetails($row, $lists, $last, $created, $params);
        endif;
        
        $tabs->endTab();
        $tabs->endPane();
        ?>
		<input type="hidden" name="original_dmfilename" value="<?php echo $lists['original_dmfilename'];?>" />
    	<input type="hidden" name="dmsubmitedby" value="<?php echo $row->dmsubmitedby;?>" />
    	<input type="hidden" name="id" value="<?php echo $row->id;?>" />
    	<input type="hidden" name="option" value="com_docman" />
    	<input type="hidden" name="section" value="documents" />
    	<input type="hidden" name="task" value="" />
    	</form>
        <?php
    } 
    
    function _showTabDocument(&$row, &$lists, &$last, &$created)
    {
    	?>
    	<table class="adminform">
    	<tr>
			<th colspan="3"><?php echo _DML_TITLE_DOCINFORMATION ?></th>
		<tr>
    	<tr>
    		<td width="250" align="right"><?php echo _DML_NAME;?></td>
    		<td colspan="2">
    			<input class="inputbox" type="text" name="dmname" size="50" maxlength="100" value="<?php echo $row->dmname ?>" /> 
    		</td>
    	</tr>
    	<tr>
    		<td>
			<?php echo _DML_THUMBNAIL;?>:
			</td>
			<td>
			<?php echo $lists['image'];?>
			</td>
			<td rowspan="4" width="50%">
				<script language="javascript" type="text/javascript">
				<!--
				if (document.forms[0].dmthumbnail.options.value!=''){
					jsimg='../images/stories/' + getSelectedValue( 'adminForm', 'dmthumbnail' );
				} else {
					jsimg='../images/M_images/blank.png';
				}
					document.write('<img src=' + jsimg + ' name="imagelib" width="80" height="80" border="2" alt="Preview" />');
				//-->
			</script>
			</td>
    	</tr>
    	<tr>
    		<td align="right"><?php echo _DML_FILE;?>:</td>
    		<td><?php echo $lists['dmfilename'];?></td>
    	</tr>
   		<tr>
    	  	<td align="right"><?php echo _DML_CAT;?>:</td>
    		<td><?php echo $lists['catid'];?></td>
    	</tr>
    	<tr>
    		<td width="20%" align="right"><?php echo _DATE;?>:</td>
    		<td> <input class="inputbox" type="text" name="dmdate_published" id="dmdate_published" size="25" maxlength="19" value="<?php echo $row->dmdate_published; ?>" />
    		<input type="reset" class="button" value="..." onclick="return showCalendar('dmdate_published', 'y-mm-dd');" /> </td>
    	</tr>
		<tr>
			<td valign="top"><?php echo _DML_DOCURL; ?></td>
			<td>
			<input class="inputbox" type="text" name="document_url" size="50" maxlength="200" value="<?php echo htmlspecialchars($lists['document_url'], ENT_QUOTES); ?>" />
			<td><?php echo mosToolTip(_DML_DOCURL_TOOLTIP . '</span>', _DML_TOOLTIP . '...<br />' . _DML_DOCURL);?></td>
			</td>
		</tr>
    	<tr>
    		<td valign="top"><?php echo _DML_DESCRIPTION;?></td>
    		<td colspan="2">
    		<?php 
        	// parameters : areaname, content, hidden field, width, height, rows, cols
        	editorArea('editor1', $row->dmdescription , 'dmdescription', '500', '200', '50', '5') ;
        	?>
    		</td>
    	</tr>
    	<tr>
    		<td width="250" valign="top"><?php echo _DML_HOMEPAGE;?><br />
    			<i>(<?php echo _DML_MAKE_SURE;?>)</i>
    		</td>
    		<td> 
    			<input class="inputbox" type="text" name="dmurl" size="50" maxlength="200" value="<?php echo htmlspecialchars($row->dmurl, ENT_QUOTES);?>" />
    			<?php echo mosToolTip(_DML_HOMEPAGE_TOOLTIP . '</span>', _DML_TOOLTIP . '...<br />' . _DML_HOMEPAGE);?>
    		</td>
    	</tr>
    	<?php if (!$row->approved) {?>
    	<tr>
    		<td valign="top" align="right"><?php echo _DML_APPROVED;?></td>
    		<td><?php echo $lists['approved'];
            echo mosToolTip(_DML_APPROVED_TOOLTIP . '.</span>', _DML_TOOLTIP . '...<br />' . _DML_APPROVED);
            ?>
    		</td>
    	</tr>
    	<?php } ?>
		<tr>
			<td valign="top" align="right"><?php echo _DML_PUBLISHED; ?></td>
			<td>
			<?php echo $lists['published']; 
        	// echo mosToolTip(_PUBLISHED_TOOLTIP.'.</span>',_DML_TOOLTIP.'...<br />'._DML_PUBLISHED);
			?>
			</td>
		</tr>
    	</table>
    	<?php
    }
    
    function _showTabPermissions(&$row, &$lists, &$last, &$created)
    {
   		?>
    	<table class="adminform">
    	<tr>
			<th colspan="2"><?php echo _DML_TITLE_DOCPERMISSIONS ?></th>
		<tr>
    	<tr>
    		<td width="250" align="right"><?php echo _DML_OWNER;?>:</td>
    		<td>
    		<?php 
    		echo $lists['viewer'];
        	echo mosToolTip(_DML_OWNER_TOOLTIP . '</span>', _DML_TOOLTIP . '...<br />' . _DML_OWNER);
        	?> 
        	</td>
    	</tr>
    	<tr>
    		<td valign="top" align="right"><?php echo _DML_MAINTAINER;?>:</td>
    		<td>
    		<?php 
    		echo $lists['maintainer'];
        	echo mosToolTip(_DML_MANT_TOOLTIP . '</span>', _DML_TOOLTIP . '...<br />' . _DML_MAINTAINER);
        	?> 
        	</td>
    	</tr>
    	<tr>
    		<td valign="top" align="right"><?php echo _DML_CREATED_BY;?>:</td>
    		<td>[<?php echo $created[0]->name;?>] <i>on
    		<?php echo mosFormatDate($row->dmdate_published) ?>
    		</i> </td>
    	</tr>
    	<tr>
    		<td valign="top" align="right"><?php echo _DML_UPDATED_BY;?>:</td>
    		<td>[<?php echo $last[0]->name;?>]
    		<?php
        	if ($row->dmlastupdateon) {
            	echo " <i>on " . mosFormatDate($row->dmlastupdateon);
        	} 
        	?>
    		</i> 
    		</td>
    	</tr>
    	</table>
    	<?php
    }
    
    function _showTabLicense(&$row, &$lists, &$last, &$created)
    {
   		?>
    	<table class="adminform">
    	<tr>
			<th colspan="2"><?php echo _DML_TITLE_DOCLICENSES ?></th>
		<tr>
    	<tr>
    		<td width="250" ><?php echo _DML_LICENSE_TYPE;?></td>
    		<td>
    		<?php 
    		echo $lists['licenses'];
        	echo mosToolTip(_DML_LICENSE_TOOLTIP . '</span>', _DML_TOOLTIP . '...<br />' . _DML_LICENSE_TYPE);
        	?>
    		</td>
    	</tr>
    	<tr>
    		<td><?php echo _DML_DISPLAY_LICENSE;?>:</td>
    		<td>
    		<?php 
    		echo $lists['licenses_display'];
        	echo mosToolTip(_DML_DISPLAY_LIC_TOOLTIP . '</span>', _DML_TOOLTIP . '...<br />' . _DML_DISPLAY_LIC);
        	?>
    		</td>
    	</tr>
    	</table>
    	<?php
    }
    
    function _showTabDetails(&$row, &$lists, &$last, &$created, &$params)
	{
		?>
		<table class="adminform" >
		<tr>
			<th colspan="2"><?php echo _DML_TITLE_DOCDETAILS ?></th>
		<tr>
		<tr>
			<td>
				<?php echo $params->render();?>
			</td>
		</tr>
		</table>
        <?php
	}
    
    function moveDocumentForm($cid, &$lists, &$items)
    {
        ?>
        <table class="adminheading">
		<tr>
			<th class="categories"><?php echo _DML_MOVECAT;?></th>
		</tr>
		</table>
		<form action="index2.php" method="post" name="adminForm" class="adminform" id="dm_moveform">
		<table class="adminform">
		<tr>
			<td align="left" valign="middle" width="10%">
			<strong><?php echo _DML_MOVETOCAT;?>:</strong>
			<?php echo $lists['categories'] ?>
			</td>
			<td align="left" valign="top" width="20%">
			<strong><?php echo _DML_DOCSMOVED;?>:</strong>
			<?php
        	echo "<ol>";
        	foreach ($items as $item) {
            	echo "<li>" . $item->dmname . "</li>";
        	}	 
        	echo "</ol>";?>
			</td>
		</tr>
		</table>
		<input type="hidden" name="option" value="com_docman" />
    	<input type="hidden" name="section" value="documents" />
    	<input type="hidden" name="task" value="move_process" />
		<input type="hidden" name="boxchecked" value="1" />
		<?php
        foreach ($cid as $id) {
            echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
        } 
        ?>
		</form>
		<?php
    } 
} 

?>

