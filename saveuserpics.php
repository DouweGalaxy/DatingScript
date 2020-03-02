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

$curr_imgfile = $curr_tnimgfile = '';

if (isset($_POST['txtpicno']) && $_POST['txtpicno'] > 0) {
	$row = $osDB->getRow( 'SELECT id, picture, tnpicture, picext, tnext FROM ! WHERE userid = ? AND picno = ?', array( USER_SNAP_TABLE, $userid, $_POST['txtpicno'] ) );
	if (isset($row)) {
		if ($config['images_in_db'] == 'N') {

			if (isset($row['picture']) && substr_count($row['picture'], 'file:' )>0 ) {
				$curr_imgfile = ltrim(rtrim(str_replace('file:','',$row['picture'] ) ) );
			}
			if (isset($row['tnpicture']) && substr_count($row['tnpicture'],'file:' )>0 ) {
				$curr_tnimgfile = ltrim(rtrim(str_replace('file:','',$row['tnpicture'] ) ) );
			}
		}

	}
} else {
	$row='';
}

$userinfo = $osDB->getRow('select * from ! where id = ?', array( USER_TABLE, $userid) );

$err = 0;

if ($config['snaps_require_approval'] == 'Y') {

	$act = 'N';

} else {

	$act = 'Y';

}

$allwdsize = $config['upload_snap_maxsize'];

if (isset($_POST['album_name']) && $_POST['album_name'] != '') {

/* Add new album first and then process the image */

	$album_id = $osDB->getOne('select id from ! where name = ? and username = ?', array(USERALBUMS_TABLE, $_POST['album_name'], $userinfo['username'] )  );

	if ($album_id > 0 ) {
		null;
	} else {
		$osDB->query('insert into ! (username, name, passwd) values (?, ?, ?)', array( USERALBUMS_TABLE, $userinfo['username'], $_POST['album_name'], md5($_POST['album_passwd'])) );

		$album_id = $osDB->getOne('select id from ! where name = ? and username = ?', array(USERALBUMS_TABLE, $_POST['album_name'], $userinfo['username']  )  );
	}

} else {

	$album_id = isset($_POST['album_id'])?$_POST['album_id']:0;

}

if (isset($_POST['changealbum']) ) {
/* Change album name  */

	$osDB->query("update ! set album_id = ? where userid = ? and picno = ?", array(USER_SNAP_TABLE, $album_id, $userid, $_POST['txtpicno']) );
	header( 'location: userpics.php?userid='.$userid.'&msg='.ALBUM_CHANGED );
	exit;

}

if( is_uploaded_file( $_FILES['txtimage']['tmp_name'] ) && exif_imagetype($_FILES['txtimage']['tmp_name'])!='' ) {

	$img_file = $_FILES['txtimage']['tmp_name'];

	$ext = explode( '/', $_FILES['txtimage']['type'] );

	$picext = strtolower($ext[1]);

	if( $picext == 'pjpeg' || $picext == 'jpeg'){

		$picext = 'jpg';
	}

	if( $picext == 'x-png' ) {
		$picext= 'png';
	}
	//echo "$picext<br>";

	$ext_ok = '0';

	foreach (explode(',',$config['upload_snap_ext']) as $ex) {


		if ( $ex == $picext ) $ext_ok++;

	}

/* bmp files are temporarily removed from valid source */

	if ( $ext_ok <= '0' or $picext == 'bmp') {

		header( 'location: userpics.php?userid='.$userid.'&msg=' .WRONG_TYPE  );
		exit;

	}

	clearstatcache();

	$fstats= stat($img_file);

	$picsize = $fstats[7];

	/* Get current picture size and allowed size. If pic size is more than the allowed size, flag error.. */


	$imginfo = getimagesize($img_file);

	if ( ($picsize > $allwdsize ) || ($imginfo[0] > 6400 || $imginfo[1] > 4800) ) {

		header( 'location: userpics.php?userid='.$userid.'&msg='.BIG_PIC_SIZE );
		exit;

	}

	include_once (OSDATE_INC_DIR."internal/snaps_functions.php");


	$userimagedir = USER_IMAGE_DIR.$userid;
	if (!file_exists($userimagedir)) {
		mkdir($userimagedir, 0777);
		chmod($userimagedir, 0777);
	}
	$userimagedir.='/';

	$pic_descr = isset($_POST['pic_descr'])?$_POST['pic_descr']:' ';


	$tnext = $picext;

	$tnimgx = createResizedPicture($img_file, $config['upload_snap_tnsize'], $config['upload_snap_tnsize'], $tnext);
	$outfile = 'tn_'.$_POST['txtpicno'].'.'.$tnext;

	if ($config['images_in_db'] == 'N') {

		writePictureFile($tnimgx, $userimagedir.$outfile);

		$tnimg = 'file:'.$outfile;
		sleep(2);
	} else {
		writePictureFile($tnimgx, $userimagedir.$outfile);

		$tnimg = base64_encode(file_get_contents($userimagedir.$outfile));

		unlink($userimagedir.$outfile);
		sleep (2);
	}


	if ($config['images_in_db'] == 'N') {

		$imgfile = saveOriginalPictureFile($img_file, $userid, 'pic', $_POST['txtpicno'], $picext, $curr_imgfile);

		$newimg = 'file:'.$imgfile;

		sleep(2);

	} else {

		$newimg = base64_encode(file_get_contents($img_file));
	}

	if ( isset($row['id'])  ) {

		$osDB->query('update ! set picture = ?, ins_time = ?, active=?, picext=?, tnpicture = ?, tnext = ?, album_id = ?, pic_descr=?   where userid = ? and picno = ? and id = ?', array( USER_SNAP_TABLE, $newimg, $time, $act,	$picext, $tnimg, $tnext, $album_id, $pic_descr, $userid, $_POST['txtpicno'], $row['id'] ) );
	} else {

		if ((isset($_POST['txtpicno']) && $_POST['txtpicno'] == '') || !isset($_POST['txtpicno']) ) {
			$_POST['txtpicno'] = $osDB->getOne('select max(picno) from ! where userid = ?',array(USER_SNAP_TABLE,$userid) );
			$_POST['txtpicno']++;
		}

		$osDB->query( 'insert into ! (  userid, picno, picture, ins_time, active, picext, tnpicture, tnext, album_id, pic_descr ) values (  ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )', array( USER_SNAP_TABLE, $userid, $_POST['txtpicno'], $newimg, $time, $act, $picext, $tnimg, $tnext, $album_id, $pic_descr ) );

	}

	updateLoadedPicturesCnt($userid);

	unset($newimg, $tnimg);
	if (isset($img_file) && file_exists($img_file)) unlink($img_file);
	if (isset($tnimg_file) && file_exists($tnimg_file)) unlink($tnimg_file);

	Header("Cache-Control: must-revalidate");

	$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() -30) . " GMT";
	Header($ExpStr);

	header( 'location: userpics.php?userid='.$userid.'&msg='.PICTURE_LOADED );
	exit;

}

if ( is_uploaded_file( $_FILES['tnimage']['tmp_name'] ) && exif_imagetype($_FILES['tnimage']['tmp_name'])!='') {

	$tnimg_file = $_FILES['tnimage']['tmp_name'];

	$ext = explode( '/', $_FILES['tnimage']['type'] );

	$tnext = strtolower($ext[1]);

	$tnsize = $config['upload_snap_tnsize'];

	if( $tnext == 'pjpeg' || $tnext == 'jpeg'){

		$tnext = 'jpg';

	}

	if( $tnext == 'x-png' ) {
		$tnext= 'png';
	}

	$ext_ok = 0;

	foreach (explode(',',$config['upload_snap_ext']) as $ex) {

		if ( $ex == $tnext ) $ext_ok++;

	}

	if ( $ext_ok <= 0 ) {

		header( 'location: userpics.php?userid='.$userid.'&msg=' .WRONG_TYPE  );
		exit;

	}

	clearstatcache();

	$fstats= stat($tnimg_file);

	$picsize = $fstats[7];

	if ($picsize > $allwdsize) {

		header( 'location: userpics.php?userid='.$userid.'&msg='.BIG_PIC_SIZE );
		exit;

	}

	list($tnwidth, $tnheight, $tntype, $tnattr) = getimagesize($tnimg_file);


	if ($tnwidth > $tnsize or $tnheight > $tnsize) {

			header( 'location: userpics.php?userid='.$userid.'&msg='.BIGTHUMBNAIL );
			exit;
	}

	/* Get current picture size and allowed size. If pic size is more than the allowed size, flag error.. */


	include_once (OSDATE_INC_DIR."internal/snaps_functions.php");

	if ($config['images_in_db'] == 'N') {

		$tnimgfile = saveOriginalPictureFile($tnimg_file, $userid, 'tn', $_POST['txtpicno'], $tnext, $curr_tnimgfile);

		$tnimg = 'file:'.$tnimgfile;

	} else {

		$tnimg = base64_encode(createImg($tnimg_file));
	}

	unlink($tnimg_file);
	$pic_descr = isset($_POST['pic_descr'])?$_POST['pic_descr']:' ';

	if ($row) {

		$osDB->query( 'update ! set tnpicture = ?, ins_time = ?, active=?, tnext=?, album_id = ?, pic_descr=? where id = ?', array( USER_SNAP_TABLE, $tnimg, $time, $act, 	$tnext, $album_id, $pic_descr, $row['id'] ) );

	} else {

		if ($_POST['txtpicno'] == '' or !isset($_POST['txtpicno']) ) {
			$_POST['txtpicno'] = $osDB->getOne('select max(picno) from ! where userid = ?',array(USER_SNAP_TABLE,$userid) );
		}


		$osDB->query('insert into ! (  userid, picno, tnpicture, ins_time, active, tnext,
		album_id, pic_descr ) values (  ?, ?, ?, ?, ?, ?, ? ,? )', array( USER_SNAP_TABLE,  $userid, $_POST['txtpicno'], $tnimg, $time, $act, $tnext, $album_id, $pic_descr ) );

	}

	updateLoadedPicturesCnt($userid);

	unset($tnimg);
	header( 'location: userpics.php?userid='.$userid.'&msg='.PICTURE_LOADED );
	exit;

}

header( 'location: userpics.php?userid='.$userid.'&msg='.FAILED_UPLOAD );
exit;


?>