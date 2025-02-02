<?php

/**
* Remove extra white space within the text.
* 
* @author Monte Ohrt <monte@ispi.net> 
* @author Contributions from Lars Noschinski <lars@usenet.noschinski.de> 
* @author Converted to a Savant2 filter by Paul M. Jones <pmjones@ciaweb.net> 
* @license http://www.gnu.org/copyleft/lesser.html LGPL
*
*/

require_once dirname(__FILE__) . '/Filter.php';

class Savant2_Filter_trimwhitespace extends Savant2_Filter {
    /**
    * Removes extra white space within the text.
    * 
    * Trim leading white space and blank lines from template source after it
    * gets interpreted, cleaning up code and saving bandwidth. Does not
    * affect <PRE>></PRE> and <SCRIPT></SCRIPT> blocks.<br>
    * 
    * @access public 
    * @param string $ &$source The source text to be filtered.
    */

    function filter(&$source)
    { 
        // Pull out the script blocks
        preg_match_all("!<script[^>]+>.*?</script>!is", $source, $match);
        $_script_blocks = $match[0];
        $source = preg_replace("!<script[^>]+>.*?</script>!is",
            '@@@SAVANT:TRIM:SCRIPT@@@', $source); 
        // Pull out the pre blocks
        preg_match_all("!<pre[^>]+>.*?</pre>!is", $source, $match);
        $_pre_blocks = $match[0];
        $source = preg_replace("!<pre[^>]+>.*?</pre>!is",
            '@@@SAVANT:TRIM:PRE@@@', $source); 
        // Pull out the textarea blocks
        preg_match_all("!<textarea[^>]+>.*?</textarea>!is", $source, $match);
        $_textarea_blocks = $match[0];
        $source = preg_replace("!<textarea[^>]+>.*?</textarea>!is",
            '@@@SAVANT:TRIM:TEXTAREA@@@', $source); 
        // remove all leading spaces, tabs and carriage returns NOT
        // preceeded by a php close tag.
        $source = trim(preg_replace('/((?<!\?>)\n)[\s]+/m', '\1', $source)); 
        // replace script blocks
        Savant2_Filter_trimwhitespace::_replace("@@@SAVANT:TRIM:SCRIPT@@@", $_script_blocks, $source); 
        // replace pre blocks
        Savant2_Filter_trimwhitespace::_replace("@@@SAVANT:TRIM:PRE@@@", $_pre_blocks, $source); 
        // replace textarea blocks
        Savant2_Filter_trimwhitespace::_replace("@@@SAVANT:TRIM:TEXTAREA@@@", $_textarea_blocks, $source);

        return $source;
    } 

    function _replace($search_str, $replace, &$subject)
    {
        $_len = strlen($search_str);
        $_pos = 0;
        for ($_i = 0, $_count = count($replace); $_i < $_count; $_i++) {
            if (($_pos = strpos($subject, $search_str, $_pos)) !== false) {
                $subject = substr_replace($subject, $replace[$_i], $_pos, $_len);
            } else {
                break;
            } 
        } 
    } 
} 

?>