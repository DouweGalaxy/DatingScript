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

define( 'PAGE_ID', 'banner_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;

}

$sortorder = (isset($_REQUEST['sort']) && $_REQUEST['sort']=='asc')?'desc':'asc';

$orderby = 'id';

if (isset($_REQUEST['sortby'])) {
	switch ($_REQUEST['sortby']) {
		case 'size':
			$orderby = 'size '.$sortorder.', id';
			break;
		case 'clicks':
			$orderby = 'clicks '.$sortorder.', id';
			break;
		case 'enabled':
			$orderby = 'enabled '.$sortorder.', id';
			break;
		case 'srno':
		default:
			$orderby = 'id';
			break;
	}
}

$t->assign('sortorder', $sortorder);

//Delete Banner
if ( isset($_POST['txtid']) && $_POST['txtid'] != '') {

	$osDB->query( 'DELETE FROM ! WHERE id = ?', array( BANNER_TABLE, $_POST['txtid'] ) );

} elseif ( isset($_POST['enable']) ) {

	foreach( $_POST['txtcheck'] as $k=>$val ) {

		$osDB->query( 'UPDATE ! SET enabled = ? WHERE id = ?', array( BANNER_TABLE, 'Y', $val ) );
	}
} elseif ( isset($_POST['disable']) && $_POST['disable'] != '') {
	foreach( $_POST['txtcheck'] as $k=>$val ) {

		$osDB->query( 'UPDATE ! SET enabled = ? WHERE id = ?', array( BANNER_TABLE, 'N', $val ) );
	}
} elseif( isset($_GET['edit']) ) {

	$row = $osDB->getRow( 'SELECT * FROM ! WHERE id = ?', array( BANNER_TABLE, $_GET['edit'] ) );

	$t->assign( 'data', checkBannerRow($row) );

	unset($row);

	$t->assign('rendered_page', $t->fetch('admin/banneredit.tpl'));

	$t->display( 'admin/index.tpl' );

	exit;
}

$rs = $osDB->getAll( 'SELECT * FROM ! where language is null order by ! !', array( BANNER_TABLE , $orderby, $sortorder) );

$data = array();

foreach( $rs as $row ) {

	$data[] = checkBannerRow($row);
}

$t->assign( 'data', $data );

unset($data, $rs);

$t->assign('rendered_page', $t->fetch('admin/managebanner.tpl'));

$t->display( 'admin/index.tpl' );

function checkBannerRow($row){
	$row['bannerurl'] = stripslashes( $row['bannerurl'] );
	$row['tooltip'] = stripslashes( $row['tooltip'] );
	if ($row['size'] != 'text') {

		$dim = explode( 'x', $row['size'] );

		$row['width'] = trim( $dim[0] );

		$row['height'] = trim( $dim[1] );

		$row['type'] = substr( $row['name'], -3, 3 );
	} else {

		$row['type'] = 'text';

		$row['width'] = $row['height'] = 'N.A.';
	}

	return $row;
}

exit;
?>