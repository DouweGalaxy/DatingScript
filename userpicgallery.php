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
include('sessioninc.php');

$type='profilepics';

if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'gallery') {
	$type=$_REQUEST['type'];
}

$userid = isset($_REQUEST['id'])?$_REQUEST['id']:$_REQUEST['userid'];

$username = $osDB->getOne('select username from ! where id = ?',array( USER_TABLE, $userid) );

if ($type == 'profilepics') {
	$search=' and (album_id is null or album_id = 0)';
} else {
	$search=' and  album_id > 0 ';

	$useralbums = $osDB->getAll('select id, name, passwd from ! where username = ? ', array(USERALBUMS_TABLE, $username) );

	if (count($useralbums) > 0) {

		foreach ($useralbums as $k => $row) {
			if ($row['passwd'] != '') {
				$useralbums[$k]['password']='';
			}
		}

		$useralbums = array_merge(array(array('id'=>'999','name'=>'Public')), $useralbums);

	}
	$t->assign('useralbums', $useralbums);
}

$album_id = isset($_REQUEST['album_id'])?$_REQUEST['album_id']:0;

if ($album_id != '') {
	$pics = $osDB->getAll('select picno from ! where userid = ? and album_id =?',array( USER_SNAP_TABLE, $userid, $album_id) );
} else {
	$pics = $osDB->getAll('select picno from ! where userid = ? and (album_id is NUll or album_id = ?)',array( USER_SNAP_TABLE, $userid, 0) );
}


$t->assign('username',$username);

$t->assign('pics',$pics);
$t->assign('type',$type);

unset($pics, $useralbums);

$t->assign('userid',$userid);

$t->assign('album_id', $album_id);

$t->display( 'admin/userpicgallery.tpl' );

?>