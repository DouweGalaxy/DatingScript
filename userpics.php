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

$userid=$_REQUEST['userid'];

include ( 'sessioninc.php' );

Header("Cache-Control: must-revalidate");

$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() -30) . " GMT";

Header($ExpStr);

if( isset($_GET['del']) && $_GET['del'] == 'yes' ){

	$row = $osDB->getRow( 'SELECT id, picture, tnpicture, picext, tnext FROM ! WHERE userid = ? AND picno = ?', array( USER_SNAP_TABLE, $userid, $_GET['picno'] ) );

	if ($config['images_in_db'] == 'N') {

		if (substr_count($row['picture'], 'file:' )>0 ) {
			$curr_imgfile = ltrim(rtrim(str_replace('file:','',$row['picture'] ) ) );
		}
		if (substr_count($row['tnpicture'],'file:' )>0 ) {
			$curr_tnimgfile = ltrim(rtrim(str_replace('file:','',$row['tnpicture'] ) ) );
		}
	}

	if ($_GET['typ'] == 'tn' or $config['drop_tn_also'] == 'Y') {
		@unlink(USER_IMAGE_DIR.$curr_tnimgfile);
		$osDB->query ('update ! set tnpicture = ?, tnext = ? where userid = ? and picno = ?', array( USER_SNAP_TABLE, '', '', $userid, $_GET['picno'] ) );

	}

	if ($_GET['typ'] == 'pic') {
		@unlink(USER_IMAGE_DIR.$curr_imgfile);
		$osDB->query ( 'update ! set picture = ?, picext = ? where userid = ? and picno = ?', array( USER_SNAP_TABLE, '', '', $userid, $_GET['picno'] ) );

	}

	$recdel = $osDB->getOne('select id from ! where userid = ? and picno = ? and picture = ? and tnpicture = ?', array( USER_SNAP_TABLE, $userid, $_GET['picno'], '','' ) ) ;

	if ($recdel > 0) {

		$osDB->query('delete from ! where userid = ? and picno = ?',array( USER_SNAP_TABLE, $userid, $_GET['picno'] ) );

	}

	updateLoadedPicturesCnt($userid);

	header('location: ?userid='.$userid);

	exit;
}

if( function_exists( 'imagejpeg' ) ) {
	$t->assign( 'editable', 1 );
} else {
	$t->assign( 'editable', 0 );
}

$rows = $osDB->getAll('select picno, picture, tnpicture, ifnull(album_id,0) as album_id, pic_descr, picext, tnext  from ! where userid = ? order by picno', array( USER_SNAP_TABLE, $userid ) );

$userdata = $osDB->getRow('select usr.level, usr.username,  mem.uploadpicture, mem.uploadpicturecnt, mem.allowalbum from ! as usr, ! as mem where mem.roleid = usr.level and usr.id = ?', array(USER_TABLE, MEMBERSHIP_TABLE, $userid ) );

$data = array();
$data[] = " ";
$nextpic=0;
foreach ($rows as $row) {

	$data[] = $row;
	$nextpic = $row['picno'];
}

$t->assign ( 'data', $data );

$nextpic++;

$useralbums = $osDB->getAll('select id, name from ! where username = ? order by name', array(USERALBUMS_TABLE, $userdata['username'] ) );

$useralbums = array_merge(array(array('id'=>'0','name'=>'Profile Pictures'),array('id'=>'999','name'=>'Public')), $useralbums);

$t->assign('max_picture_cnt', (count($data) < $userdata['uploadpicturecnt'])? count($data) : $userdata['uploadpicturecnt'] );

$t->assign('userdata',$userdata);

$t->assign('useralbums',$useralbums);

unset($rows, $data, $useralbums, $userdata);

$t->assign('nextpic',$nextpic);

$t->assign('snapload_msg', str_replace('#MAXSIZE#', floor($config['upload_snap_maxsize']/1000), get_lang('snapload_msg') ) );

$t->assign ( 'lang', $lang );

if (isset($_GET['msg']) && $_GET['msg'] != '' ) {
	$t->assign("error_message", get_lang("errormsgs", $_GET['msg']) );
}

$t->assign('userid', $userid);

$t->assign('rendered_page',$t->fetch('admin/userpics.tpl'));

$t->display ( 'admin/index.tpl' );

?>