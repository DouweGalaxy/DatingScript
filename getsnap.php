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

ob_start();

if ( !defined( 'SMARTY_DIR' ) ) {
	include_once( 'minimum_init.php' );
}

include(OSDATE_INC_DIR.'internal/snaps_functions.php');

if (isset($_REQUEST['username']) && $_REQUEST['username'] != '') {
	$userid = $osDB->getOne('select id from ! where username = ?',array(USER_TABLE, $_REQUEST['username']) );
} else {
	//  include ( 'sessioninc.php' );

	if( !isset($_GET['id']) || (isset($_GET['id'])&& (int)$_GET['id'] <= 0 ) ) {

		$userid = $_SESSION['UserId'];

	} else {

		$userid = $_GET['id'];

	}
}


if (!isset($_GET['picid']) ) {

	if ((isset($_REQUEST['type']) && $_REQUEST['type'] != 'gallery') || !isset($_REQUEST['type']) ) {

		$defpic = $osDB->getOne('select picno from ! where userid = ? and ( album_id is null or album_id = ?) and default_pic = ? and active = ? ',array(USER_SNAP_TABLE, $userid,'0','Y','Y' ) );

		if ($defpic != '') {
			$picid = $defpic;
		} else {

			$picid = $osDB->getOne('select picno from ! where userid = ? and ( album_id is null or album_id = ?) and active=? order by rand()',array(USER_SNAP_TABLE, $userid,'0','Y' ) );

		}
		unset( $defpic);
	}
} else {

	$picid = $_GET['picid'];

}

$typ = isset( $_GET['typ'])?$_GET['typ']:'pic' ;

$cond = '';

if ( ($config['snaps_require_approval'] == 'Y' || $config['snaps_require_approval'] == '1') && $userid != $_SESSION['UserId'] ) {

	$cond = " and active = 'Y' ";
}

$sql = 'select *  from ! where userid = ? and picno = ? '.$cond;

/* Get the watermarked picture file from cache directory.  */
$row =& $osDB->getRow ( $sql, array( USER_SNAP_TABLE, $userid, $picid ) );

$img = getPicture($userid, $picid, $typ, $row);

$ext = ($typ = 'tn')?$row['tnext']:$row['picext'];

if ( $img != '' && ( ( hasRight('seepictureprofile') && ( $config['snaps_require_approval'] == 'Y' && $row['active'] == 'Y'  ) ||$config['snaps_require_approval'] == 'N' ) || $userid == $_SESSION['UserId']  ) ) {

	$img2 = $img;

} else {

	$gender = $osDB->getOne( 'select gender from ! where id = ?', array( USER_TABLE, $userid ) ) ;

	if ($gender == 'M') {
		$nopic = SKIN_IMAGES_DIR.'male.jpg';
	} elseif ($gender == 'F') {
		$nopic = SKIN_IMAGES_DIR.'female.jpg';
	} elseif ($gender == 'C') {
		$nopic = SKIN_IMAGES_DIR.'couple.jpg';
	}

	$img2 = imagecreatefromjpeg($nopic);
	$ext = 'jpg';
}

ob_end_clean();
header("Pragma: public");
header("Content-Type: image/".$ext);
header("Content-Transfer-Encoding: binary");
header("Cache-Control: must-revalidate");

$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() - 30) . " GMT";

header($ExpStr);
header("Content-Disposition: attachment; filename=profile_".$userid."_".$typ.".".$ext);

/*
 if ($_SESSION['browser'] != 'MSIE') {

	header("Content-Disposition: inline" );
 }
*/
if ($ext == 'jpg') {
	imagejpeg($img2);
} elseif ($ext == 'gif') {
	imagegif($img2);
} elseif ($ext == 'png') {
	imagepng($img2);
} elseif ($ext == 'bmp') {
	imagewbmp($img2);
}
imagedestroy($img2);
?>