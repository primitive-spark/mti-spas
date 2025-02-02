<?php
/*
* DOCMan 1.3.0 for Mambo 4.5.1 CMS  
* @version $Id: upload.link.php,v 1.17 2005/08/07 17:40:25 johanjanssens Exp $
* @package DOCMan_1.3.0
* @copyright (C) 2003 - 2005 The DOCMan Development Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Oficial website: http://www.mambodocman.com/
*/

defined('_VALID_MOS') or die('Direct access to this location is not allowed.');

if (defined('_DOCMAN_LINK_TRANSFER')) {
    return true;
} else {
    define('_DOCMAN_LINK_TRANSFER' , 1);
} 

include_once dirname(__FILE__) . '/upload.link.html.php';

class DMUploadMethod 
{
    function fetchMethodForm($uid, $step, $update = false)
    {
        global $task;
        
        switch ($step) 
        {
            case 2: // Input the remote URL(Form)
            {
                $lists = array();
                $lists['action']    = _taskLink($task, $uid, array('step' => $step + 1));
                return HTML_DMUploadMethod::linkFileForm($lists);
            } break;

            case 3: // Create a link
            {
                $url = stripslashes(mosGetParam($_REQUEST , 'url' , 'http://'));
                $err = DMUploadMethod::linkFileProcess($uid, $step, $url);
                if($err['_error']) {
                	_returnTo($task, $err['_errmsg'], '', array("method" => 'link' ,"step" => $step - 1 ,"localfile" => '' , "url" => DOCMAN_Utils::safeEncodeURL($url)));
                }
                
                $uploaded = DOCMAN_Utils::safeEncodeURL(_DM_DOCUMENT_LINK . $url);
                
                $catid = $update ? 0 : $uid;
                $docid = $update ? $uid : 0;
                
                return fetchEditDocumentForm($docid , $uploaded, $catid);
            } break;

            default:
                break;
        } 
        return true;
    }
    
    function linkFileProcess($uid, $step, $url)
    {
        global $_DMUSER, $_DOCMAN;

        if ($url == '') {
        	return array(
				'_error' => 1,
				'_errmsg'=> _FILENAME_REQUIRED
         	);
        } 
        	
    	$path = $_DOCMAN->getCfg('dmpath');
   		
   		//get file validation settings
   		if ($_DMUSER->isAdmin) {
      		$validate = _DM_VALIDATE_ADMIN;
   		} else {
     		if ($_DOCMAN->getCfg('user_all', false)) {
        		$validate = _DM_VALIDATE_USER_ALL ;
      		} else {
           		$validate = _DM_VALIDATE_USER;
       		} 
  		}
  		
  		//upload the file
  		$upload = new DOCMAN_FileUpload();
  		$file = $upload->uploadLINK($url , $validate);

        if (!$file) {
        	
            $msg = _DML_ERROR_LINKING . " - " . $upload->_err;
            
            return array(
				'_error' => 1,
				'_errmsg'=> $msg
         	);
        }
        
       $msg = "&quot;" . $file->name . "&quot; " . _DML_LINKED; 
		
       return array(
			'_error' => 0,
			'_errmsg'=> $msg
         );
    } 
} 

?>
