<?php
/**
* @version $Id: newsfeeds.searchbot.php 890 2005-11-06 15:56:30Z stingrey $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

$_MAMBOTS->registerFunction( 'onSearch', 'botSearchNewsfeedslinks' );

/**
* Contacts Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param string mathcing option, exact|any|all
* @param string ordering option, newest|oldest|popular|alpha|category
*/
function botSearchNewsfeedslinks( $text, $phrase='', $ordering='' ) {
	global $database, $my;
	
	// load mambot params info
	$query = "SELECT id"
	. "\n FROM #__mambots"
	. "\n WHERE element = 'newsfeeds.searchbot'"
	. "\n AND folder = 'search'"
	;
	$database->setQuery( $query );
	$id 	= $database->loadResult();
	$mambot = new mosMambot( $database );
	$mambot->load( $id );
	$botParams = new mosParameters( $mambot->params );
	
	$limit = $botParams->def( 'search_limit', 50 );
	$limit = "\n LIMIT $limit";	
	
	$text = trim( $text );
	if ($text == '') {
		return array();
	}

	$wheres = array();
	switch ($phrase) {
		case 'exact':
			$wheres2 = array();
			$wheres2[] = "LOWER(a.name) LIKE '%$text%'";
			$wheres2[] = "LOWER(a.link) LIKE '%$text%'";
			$where = '(' . implode( ') OR (', $wheres2 ) . ')';
			break;
			
		case 'all':
		case 'any':
		default:
			$words = explode( ' ', $text );
			$wheres = array();
			foreach ($words as $word) {
				$wheres2 = array();
		  		$wheres2[] = "LOWER(a.name) LIKE '%$word%'";
				$wheres2[] = "LOWER(a.link) LIKE '%$word%'";
				$wheres[] = implode( ' OR ', $wheres2 );
			}
			$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			break;
	}


	switch ( $ordering ) {
		case 'alpha':
			$order = 'a.name ASC';
			break;

		case 'category':
			$order = 'b.title ASC, a.name ASC';
			break;

		case 'oldest':
		case 'popular':
		case 'newest':
		default:
			$order = 'a.name ASC';
	}

	$query = "SELECT a.name AS title,"
	. "\n '' AS created,"
	. "\n a.link AS text,"
	. "\n CONCAT_WS( ' / ','". _SEARCH_NEWSFEEDS ."', b.title )AS section,"
	. "\n CONCAT( 'index.php?option=com_newsfeeds&task=view&feedid=', a.id ) AS href,"
	. "\n '1' AS browsernav"
	. "\n FROM #__newsfeeds AS a"
	. "\n INNER JOIN #__categories AS b ON b.id = a.catid AND b.access IN ( $my->jaclplus )"
	. "\n WHERE ( $where )"
	. "\n AND a.published = 1"
	. "\n ORDER BY $order"
	. $limit
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	return $rows;
}
?>