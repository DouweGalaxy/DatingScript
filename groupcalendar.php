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

define( 'PAGE_ID', 'calendar_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$arr = $_POST[ 'txtcheck' ];

if ( count( $arr ) == 0 ) {

	header( 'location: calendar.php?msg=' . urlencode(get_lang('no_select_msg')) );
	exit;
}

$sql = 'update ! set enabled=? where id=?';

if ( $_POST['groupaction'] == get_lang('enable_selected') ) {

	foreach ( $arr as $id ) {

		$osDB->query( $sql, array( CALENDARS_TABLE, 'Y', $id) );
	}
	header ( 'location: calendar.php?errid=5' );
	exit;

} elseif ($_POST['groupaction'] == get_lang('disable_selected') ) {

	foreach ( $arr as $id ) {

		$osDB->query( $sql, array( CALENDARS_TABLE, 'N', $id) );
	}
	header ( 'location: calendar.php?errid=6' );
	exit;

}

// Editing calendar
foreach ( $arr as $calendarid ) {

	$row = $osDB->getRow( 'SELECT id, calendar, enabled from ! Where id = ?', array( CALENDARS_TABLE, $calendarid) );

	$data[] = $row;

}

$t->assign( 'lang', $lang );

$t->assign( 'error', get_lang('admin_error_msgs', $_GET['errid'] ) );

$t->assign( 'data', $data );

unset($data, $arr);

if ( $_POST['groupaction'] == get_lang('change_selected') ) {

	$t->assign('rendered_page', $t->fetch('admin/groupcalendaredit.tpl' ));

} elseif ($_POST['groupaction'] == get_lang('delete_selected') ) {

	$t->assign('rendered_page', $t->fetch('admin/groupcalendardel.tpl' ));

}

$t->display('admin/index.tpl');

?>