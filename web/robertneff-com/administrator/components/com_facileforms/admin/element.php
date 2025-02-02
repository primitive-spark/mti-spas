<?php
/**
* FacileForms - A Joomla Forms Application
* @version 1.4.4
* @package FacileForms
* @copyright (C) 2004-2005 by Peter Koch
* @license Released under the terms of the GNU General Public License
**/
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

require_once($ff_admpath.'/admin/form.class.php');
require_once($ff_admpath.'/admin/element.class.php');

$form = mosGetParam($_REQUEST, 'form', '');
$page = mosGetParam($_REQUEST, 'page', 1);
$tabpane = mosGetParam($_REQUEST, 'tabpane', 0);
$pkg = mosGetParam($_REQUEST, 'pkg', '');
switch ($task) {
	case 'editform' :
		facileFormsForm::edit(
			$option, $tabpane, $pkg, array($form),
			"index2.php?option=$option&act=editpage&form=$form&page=$page&pkg=$pkg"
		);
		break;
	case 'edit' :
		facileFormsElement::edit($option, $tabpane, $pkg, $form, $page, $ids, '');
		break;
	case 'new' :
		facileFormsElement::newElement($option, $pkg, $form, $page);
		break;
	case 'newedit' :
		$newtype = mosGetParam($_REQUEST, 'newtype', '');
		facileFormsElement::edit($option, 0, $pkg, $form, $page, $ids, $newtype);
		break;
	case 'save' :
		facileFormsElement::save($option, $pkg, $form, $page);
		break;
	case 'sort' :
		facileFormsElement::sort($option, $pkg, $form, $page);
		break;
	case 'cancel':
		facileFormsElement::cancel($option, $pkg, $form, $page);
		break;
	case 'remove' :
		facileFormsElement::del($option, $pkg, $form, $page, $ids);
		break;
	case 'copy' :
		facileFormsElement::getDestination($option, $pkg, $form, $page, $ids, 'copysave');
		break;
	case 'copysave' :
		$destination = mosGetParam($_REQUEST, 'destination', '');
		facileFormsElement::copy($option, $pkg, $form, $page, $ids, $destination);
		break;
	case 'move' :
		facileFormsElement::getDestination($option, $pkg, $form, $page, $ids, 'movesave');
		break;
	case 'movesave' :
		$destination = mosGetParam($_REQUEST, 'destination', '');
		facileFormsElement::move($option, $pkg, $form, $page, $ids, $destination);
		break;
	case 'movepos':
		facileFormsElement::movePos($option, $pkg, $form, $page, $ids, $task);
		break;
	case 'gridshow':
		facileFormsElement::gridshow($option, $pkg, $form, $page, $ids, $task);
		break;
	case 'publish' :
		facileFormsElement::publish($option, $pkg, $form, $page, $ids, 1);
		break;
	case 'unpublish' :
		facileFormsElement::publish($option, $pkg,  $form, $page, $ids, 0);
		break;
	case 'orderup':
		facileFormsElement::order($option, $pkg, $form, $page, $ids, -1);
		break;
	case 'orderdown':
		facileFormsElement::order($option, $pkg, $form, $page, $ids, 1);
		break;
	case 'addbefore' :
		facileFormsElement::addPageBefore($option, $pkg, $form, $page);
		break;
	case 'addbehind' :
		facileFormsElement::addPageBehind($option, $pkg, $form, $page);
		break;
	case 'delpage' :
		facileFormsElement::delPage($option, $pkg, $form, $page);
		break;
	case 'movepage' :
		facileFormsElement::getPageDestination($option, $pkg, $form, $page);
		break;
	case 'movepagesave' :
		facileFormsElement::movePage($option, $pkg, $form, $page);
		break;
	case 'submit':
		facileFormsElement::listitems($option, $pkg, $form, $page, 'submit');
		break;
	case 'config' :
		$ff_config->edit(
			$option,
			"index2.php?option=$option&act=editpage&form=$form&page=$page",
			$pkg
		);
		break;
	default: // view
		facileFormsElement::listitems($option, $pkg, $form, $page, 'view');
		break;
} // switch
?>