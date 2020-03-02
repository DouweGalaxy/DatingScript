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

define( 'PAGE_ID', 'affiliate_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$psize = getPageSize();

$sortby = ' name ';
//Default Sorting
if (isset($_REQUEST['sortby'])) {
	if ($_REQUEST['sortby'] == 'email') {
		$sortby = ' email ';
	} elseif ($_REQUEST['sortby'] == 'register') {
		$sortby = ' regdate ';
	} elseif ($_REQUEST['sortby'] == 'status') {
		$sortby = ' status ';
	}
}

if (!isset($_REQUEST['sortorder']) && isset($_SESSION['sortorder']) && $_SESSION['sortorder'] != '') {
	$sortorder = $_SESSION['sortorder'];
} elseif (isset($_REQUEST['sortorder'])) {
	$sortorder = checkSortType($_REQUEST['sortorder']);
} else {
	$sortorder='asc';
}

//For updating affiliates
if ( isset($_POST['groupaction'])  && isset($_POST['txtchk']) && count($_POST['txtchk']) > 0 ) {

	foreach ($lang['status_act'] as $key => $val ) {
		if ($val == $_POST['groupaction']) {
			$action = $key;
		}
	}

	$sqlwhere = "";

	foreach ( $_POST['txtchk'] as $affid) {
		if ($sqlwhere != "") $sqlwhere .= ' or ';
		$sqlwhere .= " id = '".$affid."' ";
	}

	if ($action != '') {
		$osDB->query( ' UPDATE ! SET status = ? where '.$sqlwhere, array( AFFILIATE_TABLE, $action ) );
	}

	$t->assign('lang',$lang);

	header('location: affiliatesview.php');
	exit;
}

//Paging View style

$t->assign('psize', $psize);

$t->assign('lang',$lang);

$page = isset($_GET['offset'])?(int)$_GET['offset']:0;

if( $page == 0 ) {
	$page = 1;
}

$upr = ($page) * $psize - $psize;
$lwr = ($page) * $psize ;

$t->assign( 'data',$osDB->getAll( 'SELECT * FROM ! ORDER BY ' . $sortby .' '. $sortorder . " LIMIT $upr,$lwr", array( AFFILIATE_TABLE ) ) );

$t->assign( "sortorder",trim($sortorder) );
$t->assign( 'upr', $upr );

$t->assign('rendered_page', $t->fetch('admin/affiliatesview.tpl'));

$t->display( 'admin/index.tpl' );

?>