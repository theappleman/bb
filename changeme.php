<?
/*
	This is just a layout for the config file: change this now!
	Rename this file userconf.php as well
*/
$db_host = "localhost"; // database hostname
$db_user = ""; // username for the database
$db_pass = ""; // password for said user
$db_data = ""; // name of the database being used
$db_prefix = ""; // prefix of database tables
$cache_dir = ""; // Full path to a cache directory, not web readable.
$cache_time = "300"; // TTL

$sitename = ""; // Kinda obvious? unless you can't read that...
$hurl = ""; // It is best to set this. Very rarely it is ok to leave it blank. Base URL.
$datefmt = "Y-m-d H:i:s";
$default = ""; // default category.
$style = ""; // stylesheet name

$menu = array('Link1'=>'http://url.to/link/1'); // array containing links for the main menu
$snapcode = ""; // Your snap shots, just the number

$nochat = array('comments'); // categories that will not be easily postable to.

$accept = "image/jpeg,image/gif,image/png"; // mime types allowed to be uploaded
$uploaddir = ''; // world writable folder for uploaded images. System absolute path - past the $hurl
// thumbnail max sizes DO NOT SET $height TO 0 (ZERO)
$width = 200;
$height = 200;

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
  `moderated` tinyint(1) default NULL COMMENT 'Moderation flag',
  `section` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT 'Section which the entry falls under',
  `commentable` tinyint(1) NOT NULL COMMENT 'Commentable flag, allows items to have comments',
  `commentref` bigint(4) unsigned default NULL COMMENT 'ID of entry that the comment belongs to',
  `sticky` tinyint(1) NOT NULL default '1' COMMENT 'sticky flag, shows as the top',
  `rateable` tinyint(1) default NULL COMMENT 'rateable flag',
  `rating` bigint(11) NOT NULL default '0' COMMENT 'post rating',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Main table for the website system';

CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_key` varchar(24) default NULL,
  UNIQUE KEY `uki` (`transaction_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT="Transaction key table";

*/

?>
