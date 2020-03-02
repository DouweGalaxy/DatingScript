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

define( 'PAGE_ID', 'banner_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$err = 0;

$newBanner['txtbanner'] = isset($_FILES['txtbanner'])?$_FILES['txtbanner']:'';
$newBanner['textbannertxt'] = isset($_POST['textbannertxt'])?$_POST['textbannertxt']:'';
$newBanner['txtlinkurl'] = isset($_POST['txtlinkurl'])?$_POST['txtlinkurl']:'';
$newBanner['link_target'] = isset($_POST['link_target'])?$_POST['link_target']:'';
/// Start Date
$startdate = mktime(0,0,0,$_POST['txtstartMonth'],$_POST['txtstartDay'],$_POST['txtstartYear'],0);
$newBanner['txtstart'] = $startdate;
/// Expity Date
$expirydate = mktime(0,0,0,$emm = $_POST['txtendMonth'],$_POST['txtendDay'],$_POST['txtendYear'],0);
$newBanner['txtend'] = $expirydate;

$newBanner['txttooltip'] = isset($_POST['txttooltip'])?$_POST['txttooltip']:'';

$_SESSION['newBanner'] = $newBanner;

if (!isset($_FILES['txtbanner']) || ( $_FILES['txtbanner'] == '' && $newBanner['textbannertxt'] == '' ) ) {

	$err = BANNER_BLANK;	// change to a constant later
}
if (!isset($_FILES['txtbanner']) || ( $_FILES['txtbanner'] == '' && $newBanner['txtlinkurl'] == '' )) {

	$err = LINK_BLANK;	// change to a constant later
}

if ( $err != 0 ) {

	header( 'location: addbanner.php?errid=' . $err );
	exit;
}
$linkurl = HTTP_METHOD . $newBanner['txtlinkurl'];

if( isset($_FILES['txtbanner']) && is_uploaded_file( $_FILES['txtbanner']['tmp_name'] ) ) {
	$imgw = 0;

	$imgh = 0;

	$imgsize = '';

	$ext = explode( "/", $_FILES['txtbanner']['type'] );

	$size = getimagesize(	$_FILES['txtbanner']['tmp_name'] );

	if($ext[1] == 'pjpeg' || $ext[1]=='jpeg'){

		$imgw =  $size[0];

		$imgh =  $size[1];

		$ext[1] = 'jpg';

		$imgsize = $imgw . ' x '  . $imgh;

	} elseif( $ext[1] == 'x-shockwave-flash' ){

		$ext[1] = 'swf';

	} elseif( $ext[1] == 'gif' ){

		$imgw =  $size[0];

		$imgh =  $size[1];

		$ext[1] = 'gif';

		$imgsize = $imgw . ' x ' . $imgh;

	} elseif( $ext[1] == 'bmp' ){

		$imgw =  $size[0];

		$imgh =  $size[1];

		$ext[1] = 'bmp';

		$imgsize = $imgw . ' x ' . $imgh;

	} elseif( $ext[1] == 'x-png' || $ext[1] == 'png' ){

		$imgw =  $size[0];

		$imgh =  $size[1];

		$ext[1] = 'png';

		$imgsize = $imgw . ' x ' . $imgh;

	} else {

		$err = BANNER_WRONG_TYPE;
		header( 'location: addbanner.php?errid=' . $err );
		exit;

	}

	if ($imgw > $config['banner_width'] || $imgh > $config['banner_height']) {
		$err = BANNER_WRONG_SIZE;
		header( 'location: addbanner.php?errid=' . $err );
		exit;
	}

	$tooltip = $_POST['txttooltip'];

	$osDB->query( "INSERT INTO ! (  linkurl, tooltip, size, startdate, expdate, link_target ) VALUES (  ?, ? , ?, ?, ?, ? )", array( BANNER_TABLE, $linkurl, $_POST['txttooltip'], $imgsize, $startdate, $expirydate, $newBanner['link_target'] ) );

	$lastid = $osDB->getOne('select id from ! where linkurl = ?',array( BANNER_TABLE, $linkurl)) ;

	$fname = $lastid . '.' . $ext[1];

	$bannerlink = '';

	$url = str_replace( '/admin', '', HTTP_METHOD . $_SERVER['SERVER_NAME'] . DOC_ROOT );

	if( $ext[1] == 'jpg' || $ext[1] == 'gif' || $ext[1] == 'bmp' || $ext[1] == 'x-png' || $ext[1] == 'png'){

		$bannerlink="<a href='banclick.php?id=$lastid' target='".$newBanner['link_target']."'><img src='" . $url. 'temp/banners/' . $fname . "' border='0' width='$imgw' height='$imgh' alt=\"$tooltip\" /></a>";
	}
	elseif( $ext[1] == 'swf' ){

		$bannerlink ="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0'>";
		$bannerlink .= "<param name='movie' value='" . $url. 'banners/' . $fname . "'>";
		$bannerlink .="<param name='quality' value='high'>";
		$bannerlink .="<embed src='" . $url. 'banners/' . $fname . "' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'></embed></object>";

	}
	$bannerlink = addslashes( $bannerlink );

	$osDB->query( 'UPDATE ! SET name = ?, bannerurl = ? WHERE id = ?', array( BANNER_TABLE, $fname, $bannerlink, $lastid ) );

	$real_path = BANNER_DIR;

	if(	(isset($HTTP_ENV_VARS['OS']) && $HTTP_ENV_VARS['OS'] == 'Windows_NT') || (isset($HTTP_ENV_VARS['SERVER_SOFTWARE']) && $HTTP_ENV_VARS['SERVER_SOFTWARE'] == 'Windows_NT') ){

		$real_path= str_replace("\\","\\\\",$real_path);

		$file = $real_path."\\".$fname;
	}
	else {

		$file = $real_path."/".$fname;

	}

	copy( $_FILES['txtbanner']['tmp_name'], $file);

} elseif ($newBanner['textbannertxt']!='') {
	/* This is a text banner */
	$osDB->query( "INSERT INTO ! (  bannerurl, linkurl, tooltip, size, startdate, expdate, link_target ) VALUES (  ?, ?, ? , ?, ?, ?, ? )", array( BANNER_TABLE, $newBanner['textbannertxt'], $linkurl, $newBanner['txttooltip'], 'text', $startdate, $expirydate, $newBanner['link_target'] ) );
}
header( 'location: managebanner.php' );

exit;
?>