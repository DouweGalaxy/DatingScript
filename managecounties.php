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

define( 'PAGE_ID', 'cntry_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$sort = findSortBy('name');

$page_size = getPageSize();

$t->assign('countryname', getCountryName($_REQUEST['countrycode'] ) );

$t->assign('countrycode', $_REQUEST['countrycode'] );

$t->assign('statename', getStateName( $_REQUEST['countrycode'], $_REQUEST['statecode'] ) );

$t->assign('statecode', $_REQUEST['statecode'] );

$t->assign('lang',$lang);

if (isset($_REQUEST['action'] )) {
	if ($_REQUEST['action'] == 'add') {

		$t->assign('todo','add');

		$t->assign('rendered_page', $t->fetch('admin/managecounties.tpl'));

		$t->display( 'admin/index.tpl' );

		exit;

	} elseif ($_REQUEST['action'] == 'edit') {

		$t->assign('data', $osDB->getRow( 'select * from ! where id = ?', array( COUNTIES_TABLE, $_REQUEST['id'] ) ) );

		$t->assign('todo','edit');

		$t->assign('rendered_page', $t->fetch('admin/managecounties.tpl'));

		$t->display( 'admin/index.tpl' );

		exit;

	} elseif ( $_REQUEST['action'] == 'added' ) {

		$foundid = $osDB->getOne( 'select id from ! where countrycode = ? and statecode = ? and (code = ? or upper(name) = upper(?) )', array( COUNTIES_TABLE, $_REQUEST['countrycode'], $_REQUEST['statecode'], $_REQUEST['code'], $_REQUEST['name'] ) ) ;

		if ($foundid > 0 ) {

			$t->assign('errmsg', COUNTYCODE_INUSE);

			$t->assign("error_message", get_lang('errormsgs', COUNTYCODE_INUSE) );

			$data['code'] = $_REQUEST['code'];

			$data['name'] = $_REQUEST['name'];

			$t->assign('data', $data);

			unset($data);

			$t->assign('todo','add');

			$t->assign('rendered_page', $t->fetch('admin/managecounties.tpl'));

			$t->display( 'admin/index.tpl' );

			exit;

		} else {

			$osDB->query('insert into !(countrycode, statecode, code, name) values (?, ?, ?, ? )', array( COUNTIES_TABLE, $_REQUEST['countrycode'], $_REQUEST['statecode'], $_REQUEST['code'], $_REQUEST['name'] ) );

			$errmsg = COUNTY_ADDED;

		}

	} elseif ($_REQUEST['action'] == 'edited' ) {

		$foundid = $osDB->getOne( 'select id from ! where countrycode = ? and statecode = ? and ( code = ? or upper(name) = upper(?) ) and id <> ?', array( COUNTIES_TABLE, $_REQUEST['countrycode'], $_REQUEST['statecode'], $_REQUEST['code'], $_REQUEST['name'], $_REQUEST['id'] ) ) ;

		if ($foundid > 0 ) {

			$t->assign('errmsg', COUNTYCODE_INUSE);

			$t->assign("error_message", get_lang('errormsgs', COUNTYCODE_INUSE) );

			$data['code'] = $_REQUEST['code'];

			$data['name'] = $_REQUEST['name'];

			$data['id'] = $_REQUEST['id'];

			$t->assign('data', $data);

			unset($data);

			$t->assign('todo','edit');

			$t->assign('rendered_page', $t->fetch('admin/managecounties.tpl'));

			$t->display( 'admin/index.tpl' );

			exit;

		} else {

			$osDB->query('update ! set code = ?, name = ? where id = ?', array( COUNTIES_TABLE, $_REQUEST['code'], $_REQUEST['name'], $_REQUEST['id'] ) );

			$errmsg = COUNTY_MODIFIED;

		}
	}elseif ( $_REQUEST['action'] == 'delete' ) {

		$osDB->query( 'DELETE FROM ! where id = ?', array( COUNTIES_TABLE, $_REQUEST['id'] ) );

		$errmsg = COUNTY_DELETED;
	}
}
if ( isset($_REQUEST['groupaction']) && $_REQUEST['groupaction'] == get_lang('delete_selected') && isset($_REQUEST['txtcheck']) &&  count($_REQUEST['txtcheck']) > 0 ) {
/* Group delete */

	$del_list = " id in (";
	foreach ($_REQUEST['txtcheck'] as $k => $val) {

		if ($k > 0) { $del_list.= ','; }

		$del_list .= "'".$val."'";

	}

	$del_list .= ')';

	$osDB->query( 'DELETE FROM ! where ! ', array( COUNTIES_TABLE, $del_list ) );

	unset($del_list);

	$errmsg = COUNTY_DELETED;

}

$where='';

if (isset($_REQUEST['searchme'] ) && $_REQUEST['searchme'] == get_lang('show') ) {

	if ($_REQUEST['countycode'] != '') {

		$where = " and upper(code) like upper('%".$_REQUEST['countycode']."%') ";

	} elseif ($_REQUEST['countyname'] != '') {

		$where = " and upper(name) like upper('%".$_REQUEST['countyname']."%') ";

	}

	$t->assign('countycode', $_REQUEST['countycode']);
	$t->assign('countyname', $_REQUEST['countyname']);
}

$page = isset($_GET['offset'])?(int)$_GET['offset']:0;

if( $page == 0 ) { $page = 1; }

$_GET['offset'] = $page;

$upr = ($page)*$page_size - $page_size;

$t->assign( 'countieslist', $osDB->getAll( 'SELECT * FROM ! where countrycode = ? and statecode = ? ! ORDER BY ! LIMIT !,! ', array( COUNTIES_TABLE, $_REQUEST['countrycode'], $_REQUEST['statecode'], $where, $sort, $upr, $page_size ) ) );

$t->assign('total_recs', $osDB->getOne('select count(*) from ! where countrycode = ? and statecode = ?', array( COUNTIES_TABLE, $_REQUEST['countrycode'], $_REQUEST['statecode'] ) ) );

$t->assign( 'sort_type', (isset($_GET['type'])?checkSortType( $_GET['type'] ):'asc') );

$t->assign('countrycode', $_REQUEST['countrycode']);

$t->assign('statecode', $_REQUEST['statecode']);

$t->assign( 'upr', $upr );

$t->assign('page_size', $page_size);

if (isset($errmsg)) {
	$t->assign('errmsg', $errmsg);

	$t->assign("error_message", get_lang('errormsgs', $errmsg) );
}
$t->assign('rendered_page', $t->fetch('admin/managecounties.tpl'));

$t->display( 'admin/index.tpl' );

?>