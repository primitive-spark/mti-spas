<?php
/*
* DOCMan 1.3.0 for Mambo 4.5.1 CMS  
* @version $Id: DOCMAN_tree.class.php,v 1.8 2005/01/25 15:15:38 johanjanssens Exp $
* @package DOCMan_1.3.0
* @copyright (C) 2003 - 2005 The DOCMan Development Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Oficial website: http://www.mambodocman.com/
*/

defined('_VALID_MOS') or die('Direct access to this location is not allowed.');

if (defined('_DOCMAN_TREE')) {
    return;
} else {
    define('_DOCMAN_TREE', 1);
} 

global $mosConfig_absolute_path;
// XML library
require_once($mosConfig_absolute_path . '/includes/domit/xml_domit_include.php');

/**
* DOCMAN tree  class.
* 
* @desc class purpose is to handle operations with categories trees.
*/

class DOCMAN_Tree {
    var $xmldoc = null; //the generated xml document
    function DOCMAN_Tree()
    { 
        // create domit document
        $this->xmldoc = &new DOMIT_Document(); 
        // generate database data
        $dbdata = $this->createData(); 
        // generate xml document
        $xmldata = "<?xml version=\"1.0\"?>\n";
        $xmldata .= "<!DOCTYPE tree SYSTEM \"tree.dtd\">\n";
        $xmldata .= "<tree>";
        $xmldata .= $this->transformToXML(0, $dbdata, 0);
        $xmldata .= "</tree>";
        $this->xmldoc->parseXML($xmldata, true);
    } 

    function getXMLDoc()
    {
        return $this->xmldoc;
    } 

    function getText()
    {
        return $this->xmldoc->toString();
    } 

    function getNormalizedText()
    {
        return $this->xmldoc->toNormalizedString();
    } 

    function saveAsXML($fileName)
    {
        $success = $this->xmldoc->saveXML($fileName);
        return $success;
    } 

    function saveAsText($fileName)
    {
        $success = $this->xmldoc->saveTextToFile($fileName, $this->xmldoc->toNormalizedString());
        return $success;
    } 

    function createData()
    {
        global $database, $my;
        global $mosConfig_shownoauth;

        /* If a user has signed in, get their user type */
        $intUserType = 0;

        if ($my->gid) {
            switch ($my->usertype) {
                case 'Super Administrator':
                    $intUserType = 0;
                    break;
                case 'Administrator':
                    $intUserType = 1;
                    break;
                case 'Editor':
                    $intUserType = 2;
                    break;
                case 'Registered':
                    $intUserType = 3;
                    break;
                case 'Author':
                    $intUserType = 4;
                    break;
                case 'Publisher':
                    $intUserType = 5;
                    break;
                case 'Manager':
                    $intUserType = 6;
                    break;
            } 
        } else {
            /* user isn't logged in so make their usertype 0 */
            $intUserType = 0;
        } 

        if ($mosConfig_shownoauth) {
            $sql = "\nSELECT id, parent_id, title AS text FROM #__categories";
            $sql .= "\nWHERE section='com_docman' AND published='1' " ; 
            // . "\nAND utaccess >= '$intUserType' "
            $sql .= "\nORDER BY parent_id,ordering";
        } else {
            $sql = "\nSELECT id, parent_id, title AS text FROM #__categories";
            $sql .= "\nWHERE section='com_docman' AND published='1' AND access <= '$my->gid'"; 
            // . "\nAND utaccess >= '$intUserType' "
            $sql .= "\nORDER BY parent_id,ordering";
        } 

        $database->setQuery($sql);

        $rows = $database->loadObjectList('id');
        echo $database->getErrorMsg(); 
        // establish the hierarchy of the menu
        $data = array(); 
        // get children
        foreach ($rows as $row) {
            // generated valid xml link
            $parent_id = $row->parent_id;
            $list = @$data[$parent_id] ? $data[$parent_id] : array();
            array_push($list, $row);
            $data[$parent_id] = $list;
        } 

        return $data;
    } 

    function transformToXML($id, &$data, $level = 0)
    { 
        // $xml = "<tree sublevel=\"$level\">";
        $xml = '';

        if (@$data[$id]) {
            foreach ($data[$id] as $row) {
                $xml .= "<tree";

                $child = (array) $row;
                foreach ($child as $tag => $value) {
                    $xml .= " $tag=\"$value\"";
                } 

                if (@$data[$row->id]) {
                    $xml .= ">\n";
                    $xml .= $this->transformToXML($row->id, $data, $level + 1);
                    $xml .= "</tree>\n";
                } else {
                    $xml .= " />\n";
                } 
            } 
        } 
        // $xml .= "</tree>";
        return $xml;
    } 
} 
// end class
?>