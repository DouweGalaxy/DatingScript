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

define( 'PAGE_ID', 'calendar_mgt' );
if ( !checkAdminPermission( PAGE_ID ) ) {
	header( 'location: not_authorize.php' );
	exit;
}


if( !isset($_GET['sort'] ) || (isset($_GET['sort']) && $_GET['sort'] == '') ) {
	$sort = 'displayorder asc';
} else {
	$sort = findSortBy();
}

if ( isset($_GET['edit'])&& $_GET['edit']!=''  ) {
	$t->assign( 'data',$osDB->getRow( "SELECT id, ".
			   "       userid, ".
			   "       event, ".
			   "       description, ".
			   "       calendarid, ".
			   "       enabled, ".
			   "       timezone, ".
			   "       datetime_from, ".
			   "       datetime_to, ".
			   "       recurring, ".
			   "       recuroption, ".
			   "       private_to ".
			   "from ! Where id = ?", array( EVENTS_TABLE, $_GET['edit'] ) ) );
	$t->assign( 'lang', $lang );
	if (isset($_GET['errid']) && $_GET['errid']!='') {
		$t->assign( 'error', get_lang('admin_error_msgs', $_GET['errid'] ) );
	}
	$t->assign('calendarname', $osDB->getOne('SELECT calendar FROM ! WHERE id = ?', array( CALENDARS_TABLE, $_REQUEST['calendarid'] ) ) );
	$t->assign('rendered_page', $t->fetch('admin/eventedit.tpl'));
	$t->display( 'admin/index.tpl' );
	exit;
}

if ( isset($_GET['insert']) && $_GET['insert']!='' ) {
	$t->assign('calendarname', $osDB->getOne('SELECT calendar from ! Where id = ?', array( CALENDARS_TABLE, $_GET['calendarid'] ) ) );
	$timestamp = (isset($_GET['timestamp'])?$_GET['timestamp']:time());
	if ($timestamp=='') $timestamp=time();
	$t->assign('timestamp', $timestamp);
	$t->assign( 'lang', $lang );
	if (isset($_GET['errid']) && $_GET['errid']!='') {
		$t->assign( 'error', get_lang('admin_error_msgs', $_GET['errid'] ) );
	}
	$t->assign('rendered_page', $t->fetch('admin/eventins.tpl'));
	$t->display( 'admin/index.tpl' );
	exit;
}

//For Deletion of calendars
if ( isset($_POST['frm'] ) && $_POST['frm']== 'frmDelEvent'){
	$id = $_POST['txtid'];
	$calendar = $_POST['txtcalendarid'];
	// Deleting watches for event
	$osDB->query( 'DELETE FROM ! WHERE eventid = ?', array( WATCHES_TABLE, $id ) );
	// Deleting event
	$osDB->query( 'DELETE FROM ! WHERE id = ? and calendarid = ?', array( EVENTS_TABLE, $id, $calendar ) );
	$_REQUEST['calendarid'] = $_GET['calendarid']=$calendar;
}

if(isset($_REQUEST['filter']) ):
	$start_date=make_datetime_from_smarty("start");
	$end_date=make_datetime_from_smarty("end");
elseif(isset($_SESSION['calendar_start'])):
	$start_date=$_SESSION["calendar_start"];
	$end_date=$_SESSION["calendar_end"];
else:
	$start_date=date("Y-m-d H:i:s",mktime (0,0,0,date("m"),date("d"),date("Y")));
	$end_date=date("Y-m-d H:i:s",mktime (23,59,59,date("m"),date("d"),date("Y")));
endif;
if (isset($_REQUEST['period']) && $_REQUEST['period']!='') {
	if($_REQUEST['period']=="year")
	{	$start_date=date("Y-m-d H:i:s",mktime (0,0,0,date("m"),date("d"),date("Y")-1));
		$end_date=date("Y-m-d H:i:s",mktime (23,59,59,date("m"),date("d"),date("Y")));
	}
	if( $_REQUEST['period'] =="month")
	{	$start_date=date("Y-m-d H:i:s",mktime (0,0,0,date("m")-1,date("d"),date("Y")));
		$end_date=date("Y-m-d H:i:s",mktime (23,59,59,date("m"),date("d"),date("Y")));
	}
	if($_REQUEST['period']=="week")
	{	$start_date=date("Y-m-d H:i:s",mktime (0,0,0,date("m"),date("d")-7,date("Y")));
		$end_date=date("Y-m-d H:i:s",mktime (23,59,59,date("m"),date("d"),date("Y")));
	}
	if($_REQUEST['period']=="day")
	{	$start_date=date("Y-m-d H:i:s",mktime (0,0,0,date("m"),date("d"),date("Y")));
		$end_date=date("Y-m-d H:i:s",mktime (23,59,59,date("m"),date("d"),date("Y")));
	}
}
$_SESSION["calendar_start"]=$start_date;
$_SESSION["calendar_end"]=$end_date;
$t->assign("start_date",$start_date);
$t->assign("end_date",$end_date);

//Get Section id, name

if (isset($_REQUEST['calendarid']) ) {
	$rowcalendar = $osDB->getRow( 'SELECT id, calendar FROM ! WHERE id = ?', array( CALENDARS_TABLE, $_REQUEST['calendarid'] ) );
	$t->assign( 'data',$osDB->getAll("SELECT * ".
       "from ! ".
	   "WHERE calendarid = ? ".
	   "order by datetime_from ", array( EVENTS_TABLE, $_GET['calendarid'] ) ) );
	$t->assign( 'calendarname', $rowcalendar['calendar'] );
	$t->assign( 'calendarid', $rowcalendar['id'] );
}
$t->assign( 'lang', $lang );
unset($rowcalendar);
$t->assign( 'sort_type', (isset($_GET['type'])?checkSortType( $_GET['type'] ):'asc' ) );
$t->assign('rendered_page', $t->fetch('admin/calendarevents.tpl'));
$t->display( 'admin/index.tpl' );
?>