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

define( 'PAGE_ID', 'snaps_require_approval' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

if (isset($_POST['action']) && $_POST['action'] == get_lang('Approve')) {
/* approve picture */

	$osDB->query( 'update ! set active = ? where id = ?', array( USER_SNAP_TABLE, 'Y', $_POST['id'] ) );

	$errid = PICTURE_APPROVED;
	if ($config['newpic_admin_act_ltr'] == 'Y') {
		sendSnapMail($_POST['id'], 'approved');
	}
} elseif (isset($_POST['action']) && $_POST['action'] == get_lang('reject')) {
/* Remove the picture entry */
	$osDB->query( 'update ! set active = ? where id = ?',array( USER_SNAP_TABLE, 'R', $_POST['id'] ) );
	$errid = PICTURE_REJECTED;
	if ($config['newpic_admin_act_ltr'] == 'Y') {
		sendSnapMail($_POST['id'], 'rejected');
	}
} elseif (isset($_POST['groupaction']) && $_POST['groupaction'] == get_lang('Approve')) {
	if (count($_POST['txtchk']) > 0) {
		foreach($_POST['txtchk'] as $picid) {
			$osDB->query( 'update ! set active = ? where id = ?', array( USER_SNAP_TABLE, 'Y', $picid ) );
			if ($config['newpic_admin_act_ltr'] == 'Y') {
				sendSnapMail($picid, 'approved');
				sleep(1);
			}
		}
		$errid = PICTURE_APPROVED;
	}
} elseif (isset($_POST['groupaction']) && $_POST['groupaction'] == get_lang('reject')) {
	if (count($_POST['txtchk']) > 0) {
		foreach($_POST['txtchk'] as $picid) {
			$osDB->query( 'update ! set active = ? where id = ?', array( USER_SNAP_TABLE, 'R', $picid ) );
			if ($config['newpic_admin_act_ltr'] == 'Y') {
				sendSnapMail($picid, 'rejected');
				sleep(1);
			}
		}
		$errid = PICTURE_REJECTED;
	}
}

/* Now select the pictures which are not yet approved.. */
$pics = $osDB->getAll( 'select id, userid, picno, album_id from ! where active = ? order by ins_time', array( USER_SNAP_TABLE, 'N' ) );

$user_pics = array();

foreach ( $pics as $row ) {

	$user = $osDB->getRow( 'select username, firstname, lastname from ! where id = ?', array( USER_TABLE, $row['userid'] ) );

	$row['username'] = $user['username'];

	$row['fullname'] = $user['firstname'] . ' '. $user['lastname'];

	$row['album_name'] = $osDB->getOne('select name from ! where id = ?', array(USERALBUMS_TABLE, $row['album_id']));

	$user_pics[] = $row;
}

$t->assign('user_pics', $user_pics);

unset($pics, $user_pics, $user);

if (isset($errid) && $errid > 0) {
	$t->assign('errid',$errid);

	$t->assign("error_message", get_lang('errormsgs', $errid) );
}
$t->assign('rendered_page', $t->fetch('admin/approve_snaps.tpl'));

$t->display('admin/index.tpl');

function sendSnapMail($picid, $type){
	global $osDB, $config, $t;
	$row =& $osDB->getRow('select userid from ! where id = ?',array(USER_SNAP_TABLE, $picid ) );
	$user = $osDB->getRow('select username, email, firstname from ! where id = ?',array(USER_TABLE, $row['userid'] ) );
	if ($type == 'rejected') {
		$message = get_lang('snaprejected', MAIL_FORMAT);

		$Subject = str_replace('#SITENAME#', $config['site_name'], get_lang('snaprejected_sub') );
	} else {
		$message = get_lang('snapapproved', MAIL_FORMAT);

		$Subject = str_replace('#SITENAME#', $config['site_name'], get_lang('snapapproved_sub') );
	}

	$message = str_replace('#FirstName#', $user['firstname'],$message);

	$success = mailSender($config['admin_email'], $user['email'], $user['email'], $Subject, $message);
	unset($message, $Subject, $row, $user);  
}



?>