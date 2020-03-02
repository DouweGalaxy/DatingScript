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

	//Include init.php
if ( !defined( 'SMARTY_DIR' ) ) {
	include_once( '../init.php' );
}

include ( 'sessioninc.php' );

define( 'PAGE_ID', 'calendar_mgt' );
if ( !checkAdminPermission( PAGE_ID ) ) {
	header( 'location: not_authorize.php' );
	exit;
}

//Default Sorting
if( isset($_GET['sort']) ) {
	if( $_GET['sort'] == '' ) {
		$sort = 'displayorder asc ';
	} else if( $_GET['sort'] == get_lang('col_head_name') ) {
			$sort = 'calendar '. checkSortType ( $_GET['type'] );
	} else {
		$sort = findSortBy();
	}
} else {
	$sort = 'displayorder asc ';
}
//For Editing calendars
if (isset( $_GET['edit']) &&  $_GET['edit']!='' ) {
	$t->assign( 'lang', $lang );
	if (isset($_GET['errid'] ) && $_GET['errid'] !='') {
		$t->assign( 'error', get_lang('admin_error_msgs', $_GET['errid'] ) );
	}
	$t->assign( 'data', $osDB->getRow( 'SELECT id, calendar, enabled from ! Where id = ?', array( CALENDARS_TABLE, $_GET['edit'] )) );
	$t->assign('rendered_page', $t->fetch('admin/calendaredit.tpl'));
	$t->display( 'admin/index.tpl' );
	exit;
}

//For Deletion of calendars
if ( isset($_POST['frm']) && $_POST['frm'] == 'frmDelcalendar' && isset($_POST['delaction']) && $_POST['delaction'] == 'Yes') {
	$id = $_POST['txtid'];
	//now delete the record
	// Deleting watched events
	$events = $osDB->getAll("select id from ! WHERE calendarid = ?",array( EVENTS_TABLE, $id ) );
	for($i=0;$i<count($events);$i++)
	{	// Deleting watches for event
		$osDB->query( 'DELETE FROM ! WHERE eventid = ?', array( WATCHES_TABLE, $events[$i]["id"] ) );
	}
	unset($events);
	// Deleting events
	$osDB->query( 'DELETE FROM ! WHERE calendarid = ?', array( EVENTS_TABLE, $id ) );
	// Deleting calendar
	$osDB->query( 'DELETE FROM ! WHERE id = ?', array( CALENDARS_TABLE, $id ) );
	header('location: calendar.php');
	exit;
}

//Insert in calendar with max displayorder
if ( isset($_POST['frm']) && $_POST['frm'] == 'frmAddcalendar') {
	$calendar = stripslashes(trim( $_POST['txtcalendar'] ));
	$enabled = trim( $_POST['txtenabled'] );
	$ordno = $osDB->getOne( 'SELECT MAX(displayorder)+1 as orderno FROM ! ', array( CALENDARS_TABLE ) );
	$osDB->query( 'INSERT INTO ! (calendar, enabled , displayorder) VALUES (?, ?, ? )', array( CALENDARS_TABLE, $calendar, $enabled,  (is_null($ordno)?"0":$ordno) ) );
	header('location: calendar.php');
	exit;
}//End of if

if ( isset($_GET['moveup']) && $_GET['moveup']!='' ) {
	$nrowdisporder = $osDB->getOne( 'SELECT displayorder FROM ! WHERE id = ?', array( CALENDARS_TABLE, $_GET['moveup'] ) );
	//to check whether it is at the highest order
	//if not then move up
	if ( $nrowdisorder != 0){
		$prow = $osDB->getRow( 'SELECT id, displayorder FROM ! WHERE displayorder = ?', array( CALENDARS_TABLE, ($nrowdisporder-1) ) );
		$sqla = 'UPDATE ! SET displayorder = ? WHERE displayorder = ? AND id = ?';
		$osDB->query( $sqla, array( CALENDARS_TABLE, $nrowdisporder, $prow['displayorder'], $prow['id'] ));
		$osDB->query( $sqla, array( CALENDARS_TABLE, $nrowdisporder-1, $nrowdisporder, $_GET['moveup'] ));
		header('location: calendar.php');
		exit;
	}
	header('location: calendar.php?msg=calendar is already at the top');
	exit;
}

if ( isset($_GET['movedown'] ) && $_GET['movedown']!='') {
	$nrowdisporder = $osDB->getOne( 'SELECT displayorder FROM ! WHERE id = ?', array( CALENDARS_TABLE,$_GET['movedown'] ) );
	//get maximum order of calendars
	$maxorder = $osDB->getOne( 'SELECT MAX(displayorder) as maxorder FROM !', array( CALENDARS_TABLE ) );
	//to check whether it is at the lowest order
	//if not then move down
	if ( $nrowdisporder !=  $maxorder['maxorder'] ){
		$prow = $osDB->getRow( 'SELECT id, displayorder FROM ! WHERE displayorder = ?', array( CALENDARS_TABLE,($nrowdisporder+1) ) );
		$sqla = 'UPDATE ! SET displayorder = ? WHERE displayorder = ? AND
			id = ?';
		$osDB->query( $sqla , array( CALENDARS_TABLE, ($nrowdisporder+1), $nrowdisporder, $_GET['movedown'] ));
		$osDB->query( $sqla , array( CALENDARS_TABLE, $nrowdisporder, $prow['displayorder'], $prow['id'] ));
		header('location: calendar.php');
		exit;
	}
	header('location: calendar.php?msg=calendar is already at the bottom');
	exit;
}

if (isset($_GET['errid']) && $_GET['errid'] > 0) {
	$t->assign('error_message', get_lang('admin_error_msgs',$_GET['errid']) );
}
$t->assign( 'lang', $lang );
$t->assign( 'sort_type', (isset($_GET['type'])?checkSortType( $_GET['type'] ):'asc' ) );
$t->assign( 'data', $osDB->getAll( 'SELECT id, calendar, displayorder, enabled from ! order by ' . $sort, array(CALENDARS_TABLE) ) );
$t->assign('rendered_page', $t->fetch('admin/calendar.tpl'));
$t->display( 'admin/index.tpl' );
?>
