<?
/*
	This is just a layout for the config file: change this now!
	Rename this file userconf.php as well
*/

/* MySQL users: */
/*
 * The next 5 variables are required for the system to work, the rest make it work properly.
 * Don't touch these if you are using SQLite
 */
$db_host = "localhost"; // database hostname
$db_user = ""; // username for the database
$db_pass = ""; // password for said user
$db_data = ""; // name of the database being used
$db_prefix = ""; // prefix of database tables
/* end MySQL */

$cache_dir = ""; /*
		  * Full path to a cache directory, should not be visible to the public.
		  * Needs trailing slash.
		  * Needs to be writable by the web server
		  */
$cache_time = "0"; // Cache time. Depending on traffic I guess.

/*
 * SQLite users:
 * Uncomment to use SQLite
 */
// $db_host = "${cache_dir}sqlite.db";
/* end SQLite */

$sitename = $cat; // string to use at the top of each page
$secure = false; // Use HTTPS?
$rhurl = ""; // domain name (incl. .) e.g. ".applehq.eu"
$datefmt = "Y-m-d H:i:s"; // Another valid format is "c".
$fuzzy = true; // turn on fuzzy dates. not an expensive operation, there is little reason to have this off
$style = "style"; // stylesheet name
$out = 15; // The number of items to take out of the database
$link = false; // Set true to use mod_rewrite

$menu = array(/*'Link1'=>'http://url.to/link/1'*/); // array containing links for the main menu
$snapcode = ""; // Your snap shots, just the number

$nochat = array('comments'); // categories that will not be easily postable to.

$uploaddir = ''; // world writable folder for uploaded images. System absolute path - past the $hurl. Needs trailing slash
// thumbnail max sizes DO NOT SET $height TO 0 (ZERO)
$width = 500;
$height = 500;

// text replacements
$patterns = array(/*''=>''*/);

// the following variables
// are used for header bits and the RSS feed
$email = "";
$meta_desc = NULL;
$meta_author = NULL;
$meta_copyright = NULL;
$meta_keywords = NULL;

/*
INSTALLATION
Create the world-writeable uploaded folder. It must be named `uploaded`
Create the world-writeable cache folder.
You may want to make a .htaccess rule for Indexes, if wanted.

-- SQL instructions to install the data table
-- If using $db_prefix, change data and transactions to `${db_prefix}data` and `${db_prefix}transactions` respectively.

CREATE TABLE IF NOT EXISTS `data` (
  `id` bigint(4) unsigned NOT NULL auto_increment COMMENT 'Unique ID for the entry',
  `title` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT 'Headline, title or name',
  `date` datetime NOT NULL COMMENT 'Visible date of the entry',
  `lastupd` datetime NOT NULL COMMENT 'Date of last comment',
  `intro` text collate utf8_unicode_ci NOT NULL COMMENT 'Brief introduction of the entry',
  `image` varchar(256) collate utf8_unicode_ci default NULL COMMENT 'Filename of attached image',
  `section` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT 'Section which the entry falls under',
  `commentable` tinyint(1) NOT NULL COMMENT 'Commentable flag, allows items to have comments',
  `commentref` bigint(4) unsigned default NULL COMMENT 'ID of entry that the comment belongs to',
  `sticky` tinyint(1) NOT NULL default '1' COMMENT 'sticky flag, shows at the top',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Main table for the website system';

CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_key` varchar(24) default NULL,
  UNIQUE KEY `uki` (`transaction_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT="Transaction key table";

*/

?>
