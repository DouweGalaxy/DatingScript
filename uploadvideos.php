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

$userid = $_REQUEST['userid'];

$videono = isset($_REQUEST['videono'])?$_REQUEST['videono']:0;

if (isset($_REQUEST['act']) && $_REQUEST['act'] != '') {

	$act = ($_REQUEST['act'] == 'activate')?'Y':'N';

	$osDB->query('update ! set active = ? where userid = ? and videono = ?', array(USER_VIDEOS_TABLE, $act, $userid, $videono) );

	header('location: ?userid='.$userid);

	exit;
}

if( isset($_GET['del']) && $_GET['del'] == 'yes' ){

	$row = $osDB->getRow( 'SELECT id, filename  FROM ! WHERE userid = ? AND videono = ?', array( USER_VIDEOS_TABLE, $userid, $videono ) );

	@unlink(USER_VIDEO_DIR.$row['filename']);

	$osDB->query('delete from ! where userid = ? and videono = ?',array( USER_VIDEOS_TABLE, $userid, $videono  ) );

	updateLoadedVideosCnt($userid);

	unset($row);

	header('location: ?userid='.$userid);

	exit;
}

$rows = $osDB->getAll( 'select videono, filename, album_id, active, video_descr from ! where userid = ? order by videono', array( USER_VIDEOS_TABLE, $userid ) );

$userdata = $osDB->getRow('select usr.level, usr.username,  mem.allow_videos, mem.videoscnt, mem.allowalbum from ! as usr, ! as mem where mem.roleid = usr.level and usr.id = ?', array(USER_TABLE, MEMBERSHIP_TABLE, $userid ) );

$data = array();
$data[]="  ";
$nextpic=0;
foreach ($rows as $row) {
	if (substr_count($row['filename'],'youtube:') > 0) {
		$row['ext'] = 'yt';
		$row['ytref'] = trim(str_replace('youtube:','',$row['filename']));
	} else {
		$row['ext'] = substr($row['filename'],-3);
		$row['fullfilename'] = 'temp/uservideos/'.$row['filename'];
	}
	$data[] = $row;
	$nextpic = $row['videono'];
}

$nextpic++;

$useralbums = $osDB->getAll('select id, name from ! where username = ? order by name', array(USERALBUMS_TABLE, $userdata['username'] ) );

if ( function_exists('system')) {
	/* system command is allowed */
	$t->assign('system_allowed','Y');
}


$t->assign('max_picture_cnt', count($data) );

$t->assign('useralbums',$useralbums);

$t->assign('userdata',$userdata);

$t->assign ( 'data', $data );
if (isset($_REQUEST['msg']) && $_REQUEST['msg'] != '') {
	$t->assign("error_message", get_lang('errormsgs',$_GET['msg']) );
}

unset($data, $userdata, $useralbums);

$t->assign ( 'lang', $lang );

$t->assign('nextvideo',$nextpic);

$t->assign('album_id', isset($_REQUEST['album_id'])?$_REQUEST['album_id']:0);

$t->assign('userid', $userid);

$t->assign('rendered_page',$t->fetch('admin/uservideos.tpl'));

$t->display ( 'admin/index.tpl' );

?>