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

$album_id = isset($_REQUEST['album_id'])? $_REQUEST['album_id']:'0';

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] == get_lang('Delete')) {
/* delete picture */

	$row=$osDB->getRow( 'select * from ! where id=?', array( USER_SNAP_TABLE, $_REQUEST['picid'] ) );

	if (substr_count($row['picture'],'file:') > 0) {
		/* The picture is in file system */
		$imgfile = ltrim(rtrim(str_replace('file:',USER_IMAGE_DIR,$row['picture']) ) );
		unlink($imgfile);
	}
	if (substr_count($row['tnpicture'],'file:') > 0) {
		/* The picture is in file system */
		$imgfile = ltrim(rtrim(str_replace('file:',USER_IMAGE_DIR,$row['tnpicture']) ) );
		unlink($imgfile);
	}

	$osDB->query('delete from ! where id=?',array(USER_SNAP_TABLE, $_REQUEST['picid'] ) );

	$t->assign('errormsg',get_lang('pic_deleted'));
}


/* Now select the pictures for the requested user */
$user = $osDB->getRow( 'select id, username, firstname, lastname from ! where id = ? ', array( USER_TABLE, $_REQUEST['userid'] ) );

$pics = $osDB->getAll( 'select id, userid, picno, ifnull(album_id,0) as album_id, pic_descr from ! where userid = ? and ifnull(album_id,0) = ? order by picno', array( USER_SNAP_TABLE, $_REQUEST['userid'], $album_id ) );

$albs = $osDB->getAll('select id, name from ! order by name', array( USERALBUMS_TABLE ) );

$albums = array();

$albums['0'] = 'Profile Pictures';
$albums['999'] = 'Public';

foreach ($albs as $albm) {
	$albums[$albm['id']] = $albm['name'];
}

$t->assign('albums', $albums);

$user_pics = array();

foreach ( $pics as $row ) {

	$row['username'] = $user['username'];

	$row['fullname'] = $user['firstname'] . ' '. $user['lastname'];

	if ($row['album_id']=='') $row['album_id'] = '0';

	$user_pics[] = $row;
}

//print_r($user_pics);
$t->assign('user_pics', $user_pics);
$t->assign('userrec',$user);
$t->assign('album_id',$album_id);
$t->assign('username',$user['username']);
$t->assign("userid",$_REQUEST['userid']);
unset($user_pics, $pics, $user);

$t->assign('rendered_page', $t->fetch('admin/showpics.tpl'));

$t->display('admin/index.tpl');

?>