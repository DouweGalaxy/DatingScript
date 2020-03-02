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

$userid 			= 	(trim( $_POST['txtuserid'] ))?trim( $_POST['txtuserid'] ):$_SESSION['AdminId'];
$event 			= 	stripslashes(trim( $_POST['txtevent'] ));
$description 	= 	stripslashes(trim( $_POST['txtdescription'] ));
$calendar 		= 	$_POST['txtcalendar'];
$enabled 		= 	$_POST['txtenabled'];
$timezone 		= 	intval($_POST['txttimezone']);
$day = $_POST['txtdatefromDay'];
$mmm = $_POST['txtdatefromMonth'];
$yyy = $_POST['txtdatefromYear'];
$hhh = $_POST['txtdatefromHour'];
$iii = $_POST['txtdatefromMinute'];
$datefrom = $dat = $yyy."-".$mmm."-".$day." ".$hhh.":".$iii;
$day = $_POST['txtdatetoDay'];
$mmm = $_POST['txtdatetoMonth'];
$yyy = $_POST['txtdatetoYear'];
$hhh = $_POST['txtdatetoHour'];
$iii = $_POST['txtdatetoMinute'];
$dateto = $dat = $yyy."-".$mmm."-".$day." ".$hhh.":".$iii;
$recurring 		= 	$_POST['txtrecurring'];
$recuroption 		= 	$_POST['txtrecuroption'];
$private_to 		= 	stripslashes($_POST['txtprivate_to']);

$osDB->query("INSERT INTO ! ".
	      "SET userid = ?, ".
		  "    event	= ?, ".
		  "    description = ?, ".
		  "    calendarid = ?, ".
		  "    enabled = ?, ".
		  "    timezone = ?, ".
		  "    datetime_from = ?, ".
		  "    datetime_to = ?, ".
		  "    recurring = ?, ".
		  "    recuroption = ?, ".
		  "    private_to = ? " , array( EVENTS_TABLE, $userid, $event, $description, $calendar, $enabled, $timezone, $datefrom, $dateto, $recurring, $recuroption, $private_to ) );
$id=$osDB->getOne("select last_insert_id()");
send_watched_mails($id);
header ( 'location: calendarevents.php?calendarid='.$_POST["txtcalendar"]);
?>