----------------------------------------------------------------------------------------------------
joomlaXplorer 1.2 - README
----------------------------------------------------------------------------------------------------
This script is based on QuiXplorer 2.3.1 (available at http://quixplorer.sourceforge.net/).
This is a Component for Use with any joomla version up from 4.5
----------------------------------------------------------------------------------------------------
Installation:
	1. Download the latest version of joomlaXplorer from http://joomlaforge.net/projects/joomlaxplorer/.
	2. Login to joomla's Administration Backend /administrator
	3. Go to Components, Install/Uninstall.
	4. Browse to the local com_joomlaxplorer_xx.tar.gz file and click on Upload File & Install.
	
  Done.
  You can now access joomlaXplorer through "Components" => "joomlaXplorer"

----------------------------------------------------------------------------------------------------
Changes to the original script:

Global
  * PHP5 compatible
  * renamed index.php to admin.joomlaxplorer.php
  * changed configuration to automatically get Path- and URL - settings from the joomla environment
  * changed relative Image URLs to absolute URLs
  * Path Navigation at the top changed to direct clickable directory links
  * Changed Language Files to comply with joomla Language directives (en => english...)

Security
  * added "defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );" to all files
  * added "index.html" to all directories

Layout
  * modified "styles/style.css" to comply with joomla's admin.css
  * changed icons from 16x16 to 22x22 and from .gif to .png
----------------------------------------------------------------------------------------------------
Facts, you should know of:
* If you're running in trouble, because you don't have permissions to chmod() or
  write to files: That's a fact! You'll surely have uploaded those files, you wanted
  to operate on, via FTP! PHP doesn't allow chmod (Changing file permissions on *Nix Systems)
  when you're not the Owner of the file. And you can only become the Owner, when the Superuser
  changes the ownership of those files.
* QuiXplorer is a multi-language Component (currently available in English, Dutch, German, French, Spanish and Russian)
  The Language is automatically picked from $mosConfig_lang. If you still have an english component,
  your language isn't supported.
  
* QuiXplorer comes with an User Management feature. As joomla provides it's own framework, we don't need
  this Login / User Management Feature
  
* Access to this component is restricted to Super Administrators by default. If you want to change this,
  edit the file header of "admin.joomlaxplorer.php" and change the following:
  ****
		if (!$acl->acl_check( 'administration', 'config', 'users', $my->usertype )) {
			mosRedirect( 'index2.php', _NOT_AUTH );
		}
  ****
  to something different.
----------------------------------------------------------------------------------------------------

**** Continue with original QuiXplorer README ****
----------------------------------------------------------------------------------------------------
Troubleshooting:
	* Some browsers (e.g. Konqueror) may want to save a download as index.php.
	  To solve this, just supply the correct name when saving.
	* Internet Explorer may behave strangely when downloading files.
	  If you open the php-file download, the real download window should open.
	* Mozilla may add the extension 'php' to a file being downloaded.
	  Save as 'any file (*.*)' and remove the 'php' extension to get the proper name.
	  (NOTE: for php-files, this extension is correct)
	* If you are unable to perform certain operations,
	  try using an FTP-chmod to set the directories to 755 and the files to 644.
	* If you don't know the full name of a directory on your website,
	  you can use a php-script containing '<?php echo getcwd(); ?>' to get it.
	* The Search Function uses PCRE regex syntax to search; though wildcards like * and ?
	  should work (like with 'ls' on Linux), it may show unexpected behaviour.
	* User-management may logout unexpectedly or show other strange behaviour.
	  This is due to a bug in PHP 4.1.2; we would advise you to upgrade to a higher version.
----------------------------------------------------------------------------------------------------
Users:
	* To enable user-authentication, set "require_login" to true in ".config/conf.php";
	  you should also set the path for the admin user in ".config/.htusers".
	* You can easily magage users using the "admin" section of QuiXplorer.
	* Standard, there is only one user, "admin", with password "pwd_admin";
	  you should change this password immediately.
----------------------------------------------------------------------------------------------------
Languages:
	* You can choose a default language by changing "language" in ".config/conf.php"
	  (to "en", "de", "nl", "fr", "es" or "ru").
	* When using user-authentication, users can select a language on login.
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
adapted for joomla: 	Soeren Eberhardt <soeren@joomla-phpShop.net>
original author:	the QuiX project
e-mail:				quix@free.fr
www:				http://www.quix.tk, http://quixplorer.sourceforge.net
----------------------------------------------------------------------------------------------------
