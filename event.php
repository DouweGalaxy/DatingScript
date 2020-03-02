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
	include_once( 'init.php' );
}

include ( 'sessioninc.php' );

// Get user's data
$user = $osDB->getRow("select * from ! where id=?",array(USER_TABLE, $_SESSION["UserId"]));
$t->assign("user",$user);
$lang['tz'] = get_lang_values('tz');

if (isset( $_GET['edit']) ) {
	$data = $osDB->getRow( "SELECT id, ".
			   "       userid, ".
			   "       event, ".
			   "       description, ".
			   "       calendarid, ".
			   "       enabled, ".
			   "       timezone, ".
			   "       DATE_ADD(datetime_from, INTERVAL ! HOUR) as datetime_from, ".
			   "       DATE_ADD(datetime_to, INTERVAL ! HOUR) as datetime_to, ".
			   "       recurring, ".
			   "       recuroption, ".
			   "       private_to ".
			   "from ! Where id = ?", array( $user["timezone"], $user["timezone"], EVENTS_TABLE, $_GET['edit'] ) );
	$t->assign( 'lang', $lang );
	if (isset($_GET['errid'])) {
		$t->assign( 'error', get_lang('admin_error_msgs', $_GET['errid'] ) );
	}
	$t->assign( 'data', $data );
	unset($data, $user);
	$t->assign('rendered_page', $t->fetch('eventedit.tpl'));
	$t->display( 'index.tpl' );
	exit;
} elseif ( isset($_GET['insert']) ) {
	$t->assign( 'lang', $lang );
	if (isset($_GET['timestamp'])) {
		$t->assign('timestamp', date('Y-m-d',$_GET['timestamp']));
	} else {
		$t->assign('timestamp', date('Y-m-d',time()));
	}
	if (isset($_GET['errid'])) {
		$t->assign( 'error', get_lang('admin_error_msgs', $_GET['errid'] ) );
	}
	unset($user);
	$t->assign('rendered_page', $t->fetch('eventins.tpl'));
	$t->display( 'index.tpl' );
	exit;
} elseif ( isset($_GET['delete']) ){
	$id = $_GET['delete'];
	// Deleting watches for event
	$result = $osDB->query( 'DELETE FROM ! WHERE eventid = ?', array( WATCHES_TABLE, $id ) );
	// Deleting event
	$result = $osDB->query( 'DELETE FROM ! WHERE id = ? ', array( EVENTS_TABLE, $id) );
}

// Get event data
$event = $osDB->getRow("select id, userid, event, description, ".
	   "       date_add(datetime_from, interval ! hour) as datetime_from, ".
	   "       date_add(datetime_to, interval ! hour) as datetime_to, ".
	   "       calendarid, timezone, private_to, ".
	   "       enabled, ".
	   "       recurring, ".
	   "       recuroption ".
	   "from ! ".
	   "where id=? ",array($user["timezone"], $user["timezone"], EVENTS_TABLE,$_REQUEST["event_id"]));

if(!$event)
	$t->assign("error",1);
else
{
	$event["watched"]=$osDB->getOne("select count(*) from ! where userid=? and eventid=? ",array(WATCHES_TABLE, $_SESSION["UserId"], $event["id"]));
	$event["username"]=$osDB->getOne("select username from ! where id=?",array(USER_TABLE, $event["userid"]));
	$event['datetime_from'] = strtotime($event['datetime_from']);
	$event['datetime_to'] = strtotime($event['datetime_to']);
	$event['calendar_name'] = $osDB->getOne('select calendar from ! where id = ?',array(CALENDARS_TABLE, $event['calendarid']) );
	if ($event['username'] == '') {
		/* Admin User */
		$event['username'] = $osDB->getOne("select username from ! where id=?",array(ADMIN_TABLE, $event["userid"]));
		$event['usertype'] = 'admin';
	}
}
$t->assign("event",$event);
unset($event, $user);
$t->assign('lang',$lang);
$t->assign('rendered_page', $t->fetch('eventview.tpl') );
$t->display ( 'index.tpl' );

exit;
?>