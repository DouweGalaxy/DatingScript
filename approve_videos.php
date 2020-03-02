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

$errid = '';
define( 'PAGE_ID', 'snaps_require_approval' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

if (isset($_POST['action']) && $_POST['action'] == get_lang('approve')) {
/* approve picture */

	$osDB->query( 'update ! set active = ? where id = ?', array( USER_VIDEOS_TABLE, 'Y', $_POST['id'] ) );

	$errid = VIDEO_APPROVED;
	if ($config['newvideo_admin_act_ltr'] == 'Y') {
		sendVideoMail($_POST['id'], 'approved');
	}

} elseif (isset($_POST['action']) && $_POST['action'] == get_lang('reject')) {
/* Remove the picture entry */

	$osDB->query( 'update ! set active = ? where id = ?',array( USER_VIDEOS_TABLE, 'R', $_POST['id'] ) );
	$errid = VIDEO_REJECTED;
	if ($config['newvideo_admin_act_ltr'] == 'Y') {
		sendVideoMail($_POST['id'], 'rejected');
	}
}


/* Now select the pictures which are not yet approved.. */
$rows = $osDB->getAll( 'select id, userid, videono, filename, album_id, video_descr from ! where active = ?  order by ins_time', array( USER_VIDEOS_TABLE, 'N') );

$user_videos = array();

foreach ( $rows as $row ) {

	$user = $osDB->getRow( 'select username, firstname, lastname from ! where id = ?', array( USER_TABLE, $row['userid'] ) );

	$row['username'] = $user['username'];

	$row['fullname'] = $user['firstname'] . ' '. $user['lastname'];

	$row['album_name'] = $osDB->getOne('select name from ! where id = ?', array(USERALBUMS_TABLE, $row['album_id']));

	if (substr_count($row['filename'],'youtube:') > 0) {
		$row['ext'] = 'yt';
		$row['ytref'] = trim(str_replace('youtube:','',$row['filename']));
	} else {
		$row['ext'] = substr($row['filename'],-3);
		$row['fullfilename'] = 'temp/uservideos/'.$row['filename'];
	}
	$user_videos[] = $row;
}

$t->assign('user_videos', $user_videos);

unset($videos, $user_videos, $user);

$t->assign('errid',$errid);

$t->assign("error_message", get_lang('errormsgs', $errid) );

$t->assign('rendered_page', $t->fetch('admin/approve_videos.tpl'));

$t->display('admin/index.tpl');


function sendVideoMail($picid, $type){
	global $osDB, $config, $t;
	$row =& $osDB->getRow('select userid from ! where id = ?',array(USER_VIDEOS_TABLE, $picid ) );
	$user = $osDB->getRow('select username, email, firstname from ! where id = ?',array(USER_TABLE, $row['userid'] ) );
	if ($type == 'rejected') {
		$message = get_lang('videorejected', MAIL_FORMAT);

		$Subject = str_replace('#SITENAME#', $config['site_name'], get_lang('videorejected_sub') );
	} else {
		$message = get_lang('videoapproved', MAIL_FORMAT);

		$Subject = str_replace('#SITENAME#', $config['site_name'], get_lang('videoapproved_sub') );
	}

	$message = str_replace('#FirstName#', $user['firstname'],$message);

	$success = mailSender($config['admin_email'], $user['email'], $user['email'], $Subject, $message);
	unset($message, $Subject, $row, $user);  
}

?>