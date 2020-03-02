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


if ( !defined( 'SMARTY_DIR' ) ) {
	include_once( '../init.php' );
}

include ( 'sessioninc.php' );

define( 'PAGE_ID', 'profie_approval' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

if (isset($_GET['act']) && $_GET['act']=='delete' && isset($_GET['id']) ) {

	$osDB->query('delete from ! where id = ?', array(FEATURED_PROFILES_TABLE, $_GET['id']) );

	$t->assign('errmsg', get_lang('feat_prof_deleted'));
}

$sort = findSortBy('username');

$t->assign('featured', $osDB->getAll('select fet.id, fet.userid, fet.start_date, fet.end_date, fet.must_show, fet.req_exposures, fet.exposures, usr.username, usr.firstname, usr.lastname, membr.name as level from ! as fet, ! as usr, ! as membr  where fet.userid = usr.id  and membr.roleid = usr.level order by ! ', array( FEATURED_PROFILES_TABLE, USER_TABLE, MEMBERSHIP_TABLE, $sort ) ) ) ;

$t->assign('lang',$lang);

$t->assign( 'sort_type', (isset($_GET['type'])?checkSortType( $_GET['type'] ):'asc') );

$t->assign('rendered_page', $t->fetch('admin/featured_profiles.tpl'));

$t->display('admin/index.tpl');

exit;
?>