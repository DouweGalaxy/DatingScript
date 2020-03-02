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
include("../minimum_init.php");

/* Update the questions table for making 'A' as default value for gender */
$osDB->query("update ".DB_PREFIX."_questions set gender='A' where gender is null or gender = ''");

$osDB->query('delete from ! where adminid = ?', array(DB_PREFIX.'_admin_permissions', 1) );
$osDB->query("INSERT INTO `".DB_PREFIX."_admin_permissions` (`id`, `adminid`, `site_stats`, `profie_approval`, `profile_mgt`, `section_mgt`, `affiliate_mgt`, `affiliate_stats`, `news_mgt`, `article_mgt`, `story_mgt`, `poll_mgt`, `search`, `ext_search`, `send_letter`, `pages_mgt`, `chat`, `chat_mgt`, `forum_mgt`, `mship_mgt`, `payment_mgt`, `banner_mgt`, `seo_mgt`, `admin_mgt`, `admin_permit_mgt`, `global_mgt`, `change_pwd`,`cntry_mgt`,`snaps_require_approval`,`featured_profiles_mgt`, `calendar_mgt`, `event_mgt`, `import_mgt`,`profile_ratings`, `plugin_mgt`,`promo_mgt`, `blog_mgt`    ) VALUES (1, 1, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1','1','1','1','1','1','1','1', '1','1','1')");
echo('Updating "admin" user with all privileges... Done<br /><br />');flush();

$payment_modules = $osDB->getAll('select distinct module_key from '.DB_PREFIX.'_payment_config');
foreach ($payment_modules as $key => $val) {
	$osDB->query('update '.DB_PREFIX.'_payment_modules set enabled="Y" where module_key="'.$val['module_key'].'"');
}
echo('Setting installed payment modules... Done<br /><br />');flush();


/* Now update pictures loaded counts for the sample data */
$pics = $osDB->getAll('select userid, count(*) as cnt from ! group by userid', array(USER_SNAP_TABLE));

foreach ($pics as $pic) {
	$osDB->query('update ! set pictures_cnt=? where id=?', array(USER_TABLE, $pic['cnt'], $pic['userid']));
}

/* Update extsearchable flag to 'Y' to enable extended search for all items */
$osDB->query('update ! set extsearchable=?',array(DB_PREFIX.'_questions', 'Y') );

$question_details = $osDB->getAll('select * from ! order by questionid, id',array(OPTIONS_TABLE));

$questionid = '';

$seq = 0;

foreach ($question_details as $k=>$option) {
	if ($questionid != $option['questionid']) {
		$questionid = $option['questionid'];
		$seq = 0;
	}
	$seq++;
	$osDB->query('update ! set displayorder = ? where id = ?', array( OPTIONS_TABLE, $seq, $option['id']) );
}

/* Convert existing buddy_ban_table to user userid as key */

$bbrecs = $osDB->getAll('select * from ! ',array(BUDDY_BAN_TABLE) );
foreach ($bbrecs as $bbrec) {
	if (is_int(trim($bbrec['userid']) ) ) {
		$userid = $bbrec['userid'];
	} else {
		$userid = $osDB->getOne('select id from ! where username = ?',array(USER_TABLE, $bbrec['userid']) );
	}
	if (is_int(trim($bbrec['ref_userid'] )) ) {
		$ref_userid = $bbrec['ref_userid'];
	} else {
		$ref_userid = $osDB->getOne('select id from ! where username = ?',array(USER_TABLE, $bbrec['ref_userid']) );
	}
	if ($userid > 0 and $ref_userid > 0) {
		$osDB->query('update ! set userid=?, ref_userid=? where id=?', array(BUDDY_BAN_TABLE, $userid, $ref_userid, $bbrec['id']) );
	}
}
echo("Update process of buddy_ban table Complete<br />");
/* Update process of buddy_ban table Complete */


?>