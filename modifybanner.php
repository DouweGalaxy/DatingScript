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

if(  isset($_FILES['txtbanner']['tmp_name']) && !isset($_POST['txtlinkurl'] ) ) {

	$err = LINK_BLANK;

}

if ( $err != 0 ) {

	header( 'location: managebanner.php?edit=' . $_POST['txtid'] . '&errid=' . $err );
	exit;

}

/// Start Date
$sdd = $_POST['txtstartDay'];

$smm = $_POST['txtstartMonth'];

$syy = $_POST['txtstartYear'];

$startdate = mktime(0,0,0,$smm,$sdd,$syy,0);

/// Expity Date
$edd = $_POST['txtendDay'];

$emm = $_POST['txtendMonth'];

$eyy = $_POST['txtendYear'];

$expirydate = mktime(0,0,0,$emm,$edd,$eyy,0);

$linkurl = isset($_POST['txtlinkurl'])?$_POST['txtlinkurl']:'';

$tooltip = isset($_POST['txttooltip'])?addslashes( $_POST['txttooltip'] ):'';

$link_target = isset($_POST['link_target'])?$_POST['link_target']:'';

$bannerlink = '';

$imgsize = '';

$fname = '';

if ( isset($_FILES['txtbanner']['tmp_name']) ) {

	if( is_uploaded_file( $_FILES['txtbanner']['tmp_name'] ) ) {

		$imgw = 0;

		$imgh = 0;

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

		}	else {
			$err = BANNER_WRONG_TYPE;
			header( 'location: managebanner.php?edit=' . $_POST['txtid'] . '&errid=' . $err );
			exit;
		}

		$fname = $_POST['txtid'] . '.' . $ext[1];

		$real_path = BANNER_DIR;

		if( (isset($HTTP_ENV_VARS['OS']) && $HTTP_ENV_VARS['OS'] == 'Windows_NT') || (isset($HTTP_ENV_VARS['SERVER_SOFTWARE']) && $HTTP_ENV_VARS['SERVER_SOFTWARE'] == 'Windows_NT') ){
			
			$real_path= str_replace("\\","\\\\",$real_path);

			$file = $real_path."\\".$fname;

		} else {
			$file = $real_path."/".$fname;
		}

		copy( $_FILES['txtbanner']['tmp_name'], $file);


		$url = str_replace( '/admin', '', HTTP_METHOD . $_SERVER['SERVER_NAME'] . DOC_ROOT );

		if( $ext[1] =='jpg' || $ext[1]=='gif' ){

			$bannerlink="<a href='banclick.php?id=" . $_POST['txtid'] . "' target='".$link_target."'><img src='" . $url. 'temp/banners/' . $fname . "' border='0' width='$imgw' height='$imgh' alt='$tooltip'></a>";
		} elseif( $ext[1] == 'swf' ){

			$bannerlink="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0'>";
			$bannerlink .= "<param name='movie' value='" . $url. 'banners/' . $fname . "'>";
			$bannerlink .="<param name='quality' value='high'>";
			$bannerlink .="<embed src='" . $url. 'banners/' . $fname . "' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash'></embed></object>";
		}
	}

} else if (isset($_POST['textbannertxt']) && $_POST['textbannertxt']!='') {

	$bannerlink = $_POST['textbannertxt'];
	$imgsize = 'text';

}

$row = $osDB->getRow('select * from ! where id = ?',array(BANNER_TABLE, $_POST['txtid']) );


if ( $bannerlink != '' ) {
	$bannerlink = addslashes( $bannerlink );

	$osDB->query('UPDATE !' .
	 " SET	linkurl		= ?,
		 name 		= ?,
		tooltip		= ?,
		size		= ?,
		startdate	= ?,
		expdate		= ?,
		link_target = ?,
		bannerurl = ?
		WHERE id = ?", array( BANNER_TABLE, $linkurl, $fname, $tooltip, $imgsize, $startdate, $expirydate, $link_target, $bannerlink, $_POST['txtid'] ) );

} else {
	$bannerlink = addslashes(str_replace("target='".$row['link_target']."'", "target='".$link_target."'",stripslashes($row['bannerurl'])));

	$osDB->query( 'UPDATE !' .
	 " SET	linkurl		= ?,
		tooltip		= ?,
		startdate	= ?,
		link_target	= ?,
		bannerurl = ?,
		expdate		= ?
		WHERE id = ?", array( BANNER_TABLE, $linkurl, $tooltip,  $startdate, $link_target, $bannerlink, $expirydate, $_POST['txtid'] ) );
}

header( 'location: managebanner.php' );
exit;
?>