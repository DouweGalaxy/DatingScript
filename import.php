<?php
/***********************************************
osDate Open-Source Dating and Matchmaking Script

(c) 2009 TUFaT.com

osDate was created by Darren Gates and Vijay Nair,
and can be downloaded freely from www.TUFaT.com.
It is distributed under the LGPL license.

osDate is free for commercial and non-commercial 
uses. You may modify, re-sell, and re-distribute
osDate. Links back to TUFaT.com are appreciated.

This program is distributed in the hope that it
will be useful, but without any warranty, and 
without even the implied warranty of merchantability
or fitness for a particular purpose. While strong 
efforts have been taken to ensure the reliability,
security, and stability of osDate, all software 
carries risk. Your use of osDate means that you 
understand and accept the risks of using osDate.

For osDate documentation, change log, community
forum, latest updates, and project details,
please go to www.TUFaT.com  The osDate project is
supported through the sale of skins and add-ons,
which are entirely optional but help with the
development and design effort.
***********************************************/

	define( 'PAGE_ID', 'admin_mgt' );
	if ( !defined( 'SMARTY_DIR' ) ) {
		include_once( '../init.php' );
	}
	include ( 'sessioninc.php' );

	$t->assign('rendered_page', $t->fetch('admin/import.tpl'));  
	$t->display ( 'admin/index.tpl' );
	exit;
?>
DROP TABLE IF EXISTS `osdate_imported_questions`;
CREATE TABLE `osdate_imported_questions` (
  `id` int(11) NOT NULL auto_increment,
  `question_id` int(11) NOT NULL default '0',
  `values_ids` text NOT NULL,
  `module` varchar(50) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `id_spr` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `osdate_imported_users`;
CREATE TABLE `osdate_imported_users` (
  `id` int(11) NOT NULL auto_increment,
  `source_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `module` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;