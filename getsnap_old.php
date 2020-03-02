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
	include_once( '../minimum_init.php' );
}

if( (int)$_GET['id'] <= 0 ) {

	$userid = $_SESSION['UserId'];

} else {

	$userid = $_GET['id'];

}

$picid = $_GET['picid'];

if (!isset($_GET['picid'])) $picid='1';


$typ = ( $_GET['typ'] != '') ? $_GET['typ'] : 'pic' ;

$gender = $osDB->getOne( 'select gender from ! where id = ?', array( USER_TABLE, $userid ) ) ;

$cond = '';

if ($typ == 'tn') {

	$sql = 'select tnpicture as picture, active, tnext as ext from ! where userid = ? and picno = ? '.$cond;

} else {

	$sql = 'select picture, active, picext as ext from ! where userid = ? and picno = ? '.$cond;

}


$row =& $osDB->getRow ( $sql, array( USER_SNAP_TABLE, $userid, $picid ) );

if (substr($row['picture'],0,5) == 'file:')  {
	/* The picture is in file system */
	$img = file_get_contents(ltrim(rtrim(str_replace('file:',USER_IMAGE_DIR,$row['picture']) ) ) );

} else {

	$img = base64_decode ( $row['picture']  );

}

if ( $row['picture'] != '' ) {

	$img = imagecreatefromstring($img);

	$w = imagesx( $img );

	$h = imagesy( $img );

	$wdth = ($_GET['width']!='')?$_GET['width']:$w;

	$hght = ($_GET['height']!='')?$_GET['height']:$h;

	if ($hght > $config['disp_snap_height']) $hght = $config['disp_snap_height'];
	if ($wdth > $config['disp_snap_width']) $wdth = $config['disp_snap_width'];



	if ($typ == 'pic' and ( $wdth < $w or $hght < $h) ) {

		if( $w > $h ) {
			$ratio = $w / $h;
			$nw = $wdth;
			$nh = $nw / $ratio;
		} else {
			$ratio = $h / $w;
			$nh = $hght;
			$nw = $nh /$ratio;
		}

		$img2 = imagecreatetruecolor( $nw, $nh );

		imagecopyresampled ( $img2, $img, 0, 0, 0 , 0, $nw, $nh, $w, $h );
		$image_height=$nh;
		$image_width=$nw;

	} else {
			if ($wdth > $w) $wdth = $w;
			if ($hght > $h) $hght = $h;

		$img2 = imagecreatetruecolor( $wdth, $hght );

		imagecopyresampled ( $img2, $img, 0, 0, 0 , 0, $wdth, $hght, $w, $h );
		$image_height=$hght;
		$image_width=$wdth;
	}

} else {

	if ($gender == 'M') {
		$nopic = SKIN_IMAGES_DIR.'male.jpg';
	} elseif ($gender == 'F') {
		$nopic = SKIN_IMAGES_DIR.'female.jpg';
	} elseif ($gender == 'C') {
		$nopic = SKIN_IMAGES_DIR.'couple.jpg';
	}

	$img2 = imagecreatefromjpeg($nopic);
}

 ob_end_clean();

 header("Pragma: public");
 header("Content-Type: image/jpg");
 header("Content-Transfer-Encoding: binary");
 header("Cache-Control: must-revalidate");

 $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() - 30) . " GMT";

 header($ExpStr);
// header("Content-Disposition: attachment; filename=profile_".$userid."_".$typ.".jpg");

imagejpeg($img2);

imagedestroy($img2);
?>