<?php
/**
* Facile Forms - A Joomla Forms Application
* @version 1.4.5
* @package FacileForms
* @copyright (C) 2004-2006 by Peter Koch
* @license Released under the terms of the GNU General Public License
**/
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

require_once($ff_admpath.'/admin/menu.html.php');

class facileFormsMenu
{
	function create($option, $pkg)
	{
		global $database;
		$lists = array();

		$database->setQuery(
			"select id, name, title ".
			"from #__facileforms_forms ".
			"order by ordering"
		);
		$lists['forms'] = $database->loadObjectList();

		$parents = array();
		$parents[] = mosHTML::makeOption(0, _FACILEFORMS_MENUS_TOP);
		$database->setQuery(
			"select id as value, title as text ".
			"from #__facileforms_compmenus ".
			"where parent=0 ".
			"order by title, id"
		);
		$plist = $database->loadObjectList();
		if (count($plist)) foreach ($plist as $obj) $parents[] = $obj;
		$lists['parents'] =
			mosHTML::selectList(
				$parents, 'parent', 'class="inputbox" size="1"',
				'value', 'text', 0
			);

		HTML_facileFormsMenu::create($option, $pkg, $lists);
	} // create

	function edit($option, $pkg, $ids, $formid, $parent)
	{
		global $database;

		$row = new facileFormsMenus($database);
		if ($formid=='')
			$row->load($ids[0]);
		else {
			if ($formid > 0) {
				$form = new facileFormsForms($database);
				$form->load($formid);
				$row->title = $form->title;
				$row->name = $form->name;
			} // if
			$row->parent = $parent;
			$database->setQuery(
				"select max(ordering)+1 ".
				"from #__facileforms_compmenus ".
				"where parent=$parent"
			);
			$row->ordering = $database->loadResult();
		} // if

		$lists = array();

		$parents = array();
		$parents[] = mosHTML::makeOption(0, _FACILEFORMS_MENUS_TOP);
		$database->setQuery(
			"select id as value, title as text ".
			"from #__facileforms_compmenus ".
			"where parent=0 ".
			"order by title, id"
		);
		$plist = $database->loadObjectList();
		if (count($plist)) foreach ($plist as $obj) $parents[] = $obj;
		$lists['parents'] =
			mosHTML::selectList(
				$parents, 'parent', 'class="inputbox" size="1"',
				'value', 'text', intval($row->parent)
			);

		$order =
			mosGetOrderingList(
				"select ordering as value, title as text ".
				"from #__facileforms_compmenus ".
				"where parent=".$row->parent." and package=binary '$pkg' ".
				"order by ordering"
			);
		$lists['ordering'] =
			mosHTML::selectList(
				$order, 'ordering', 'class="inputbox" size="1"',
				'value', 'text', intval($row->ordering)
			);

		$lists['imgs'] = array(
			'js/ThemeOffice/add_section.png',
			'js/ThemeOffice/backup.png',
			'js/ThemeOffice/categories.png',
			'js/ThemeOffice/checkin.png',
			'js/ThemeOffice/component.png',
			'js/ThemeOffice/config.png',
			'js/ThemeOffice/content.png',
			'js/ThemeOffice/controlpanel.png',
			'js/ThemeOffice/credits.png',
			'js/ThemeOffice/db.png',
			'js/ThemeOffice/document.png',
			'js/ThemeOffice/edit.png',
			'js/ThemeOffice/globe1.png',
			'js/ThemeOffice/globe2.png',
			'js/ThemeOffice/globe3.png',
			'js/ThemeOffice/globe4.png',
			'js/ThemeOffice/help.png',
			'js/ThemeOffice/home.png',
			'js/ThemeOffice/install.png',
			'js/ThemeOffice/language.png',
			'js/ThemeOffice/license.png',
			'js/ThemeOffice/mail.png',
			'js/ThemeOffice/mainmenu.png',
			'js/ThemeOffice/mass_email.png',
			'js/ThemeOffice/media.png',
			'js/ThemeOffice/menus.png',
			'js/ThemeOffice/messaging.png',
			'js/ThemeOffice/messaging_config.png',
			'js/ThemeOffice/messaging_inbox.png',
			'js/ThemeOffice/module.png',
			'js/ThemeOffice/preview.png',
			'js/ThemeOffice/query.png',
			'js/ThemeOffice/restore.png',
			'js/ThemeOffice/search_text.png',
			'js/ThemeOffice/sections.png',
			'js/ThemeOffice/statistics.png',
			'js/ThemeOffice/sysinfo.png',
			'js/ThemeOffice/template.png',
			'js/ThemeOffice/tooltip.png',
			'js/ThemeOffice/trash.png',
			'js/ThemeOffice/tux.png',
			'js/ThemeOffice/user.png',
			'js/ThemeOffice/users.png',
			'js/ThemeOffice/users_add.png',
			'js/ThemeOffice/warning.png'
		);
		HTML_facileFormsMenu::edit($option, $pkg, $row, $lists);
	} // edit

	function save($option, $pkg)
	{
		global $database;
		$row = new facileFormsMenus($database);

		// bind it to the table
		if (!$row->bind($_POST)) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		} // if

		// store it in the db
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		} // if
		$row->updateOrder('parent='.$row->parent);
		updateComponentMenus();
		mosRedirect("index2.php?option=$option&act=managemenus&pkg=$pkg", _FACILEFORMS_MENUS_SAVED);
	} // save

	function cancel($option, $pkg)
	{
		mosRedirect("index2.php?option=$option&act=managemenus&pkg=$pkg");
	} // cancel

	function copy($option, $pkg, $ids)
	{
		global $database;
		$total = count($ids);
		$row = new facileFormsMenus($database);
		$child = new facileFormsMenus($database);
		if (count($ids)) foreach ($ids as $id) {
			$row->load(intval($id));
			$row->id       = NULL;
			$row->ordering = 999999;
			$row->store();
			$row->updateOrder('parent=0');
			$database->setQuery("select id from #__facileforms_compmenus where parent=$id");
			$cids = $database->loadObjectList();
			for ($i = 0; $i < count($cids); $i++) {
				$cid = $cids[$i];
				$child->load(intval($cid->id));
				$child->id      = NULL;
				$child->parent  = $row->id;
				$child->store();
			} // for
		} // foreach
		$msg = $total.' '._FACILEFORMS_MENUS_SUCOPIED;
		updateComponentMenus();
		mosRedirect("index2.php?option=$option&act=managemenus&pkg=$pkg&mosmsg=$msg");
	} // copy

	function del($option, $pkg, $ids)
	{
		global $database;
		if (count($ids)) {
			$ids = implode(',', $ids);
			$database->setQuery("delete from #__facileforms_compmenus where parent in ($ids)");
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			} // if
			$database->setQuery("delete from #__facileforms_compmenus where id in ($ids)");
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			} // if
		} // if
		updateComponentMenus();
		mosRedirect("index2.php?option=$option&act=managemenus&pkg=$pkg");
	} // del

	function order($option, $pkg, $ids, $inc)
	{
		global $database;
		$row = new facileFormsMenus($database);
		$row->load($ids[0]);
		$row->move($inc, "package='".mysql_escape_string($pkg)."' and parent=".$row->parent);
		updateComponentMenus();
		mosRedirect("index2.php?option=$option&act=managemenus&pkg=$pkg");
	} // order

	function publish($option, $pkg, $ids, $publish)
	{
		global $database, $my;
		$ids = implode( ',', $ids );
		$database->setQuery(
			"update #__facileforms_compmenus set published='$publish' where id in ($ids)"
		);
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		} // if
		updateComponentMenus();
		mosRedirect( "index2.php?option=$option&act=managemenus&pkg=$pkg" );
	} // publish

	function listitems($option, $pkg)
	{
		global $database;

		$database->setQuery(
			"select distinct binary package as name ".
			"from #__facileforms_compmenus ".
			"where package is not null and package!='' ".
			"order by name"
		);
		$pkgs = $database->loadObjectList();
		if ($database->getErrorNum()) { echo $database->stderr(); return false; }
		$pkgok = $pkg=='';
		if (!$pkgok && count($pkgs)) foreach ($pkgs as $p) if ($p->name==$pkg) { $pkgok = true; break; }
		if (!$pkgok) $pkg = '';
		$pkglist = array();
		$pkglist[] = array($pkg=='', '');
		if (count($pkgs)) foreach ($pkgs as $p) $pkglist[] = array($p->name==$pkg, $p->name);

		$database->setQuery(
			"select ".
				"m.id as id, ".
				"if(m.parent,concat(p.title,'/',m.title),m.title) as title, ".
				"m.img as img, ".
				"m.name as name, ".
				"m.frame as frame, ".
				"m.border as border, ".
				"m.params as params, ".
				"m.published as published ".
			"from #__facileforms_compmenus as m ".
				"left join #__facileforms_compmenus as p on m.parent=p.id ".
			"where m.package = binary '".mysql_escape_string($pkg)."' ".
			"order by ".
				"if(m.parent,p.ordering,m.ordering), ".
				"if(m.parent,m.ordering,-1)"
		);
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) { echo $database->stderr(); return false; }

		HTML_facileFormsMenu::listitems($option, $rows, $pkglist);
	} // listitems

} // class facileFormsMenu
?>