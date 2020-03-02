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

if ( !defined( 'SMARTY_DIR' ) )

	include_once( '../init.php' );

include ( 'sessioninc.php' );

define( 'PAGE_ID', 'mship_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$vchat 					= (isset($_POST['chat']) && $_POST['chat']== 'on') ? 1 : 0;

$vhide 					= (isset($_POST['hide']) && $_POST['hide'] == 'on') ? 1 : 0;

$vforum 				= (isset($_POST['forum']) && $_POST['forum'] == 'on') ? 1 : 0;

$vblog 				= (isset($_POST['blog']) && $_POST['blog'] == 'on') ? 1 : 0;

$vpoll 				= (isset($_POST['poll']) && $_POST['poll'] == 'on') ? 1 : 0;

$vincludeinsearch 		= (isset($_POST['includeinsearch']) && $_POST['includeinsearch'] == 'on') ? 1 : 0;

$vmessage 				= (isset($_POST['message']) && $_POST['message'] == 'on') ? 1 : 0;

$allow_videos 				= (isset($_POST['allow_videos']) && $_POST['allow_videos'] == 'on') ? 1 : 0;

$vuploadpicture 		= (isset($_POST['uploadpicture']) && $_POST['uploadpicture'] == 'on') ? 1 : 0;

$vallowalbum 		= (isset($_POST['allowalbum']) && $_POST['allowalbum'] == 'on') ? 1 : 0;

$vallowim = (isset($_POST['allowim']) && $_POST['allowim'] == 'on') ? 1 : 0;

$vuploadpicturecnt = (isset($_POST['uploadpicturecnt']) && $_POST['uploadpicturecnt']>0)?$_POST['uploadpicturecnt']:0;

$vprofilepicscnt = (isset($_POST['profilepicscnt']) && $_POST['profilepicscnt']>0)?$_POST['profilepicscnt']:0;

$vmessage_keep_cnt = isset($_POST['message_keep_cnt'])?$_POST['message_keep_cnt']:'0';

$vmessage_keep_days = isset($_POST['message_keep_days'])?$_POST['message_keep_days']:'0';

$messages_per_day = isset($_POST['messages_per_day'])?$_POST['messages_per_day']:'0';

$winks_per_day = isset($_POST['winks_per_day'])?$_POST['winks_per_day']:'0';

$videoscnt = isset($_POST['videoscnt'])?$_POST['videoscnt']:'0';

$vseepictureprofile 	= (isset($_POST['seepictureprofile']) && $_POST['seepictureprofile'] == 'on') ? 1 : 0;

$vfavouritelist 	= (isset($_POST['favouritelist']) && $_POST['favouritelist'] == 'on') ? 1 : 0;

$vsendwinks 	= (isset($_POST['sendwinks']) && $_POST['sendwinks'] == 'on') ? 1 : 0;

$vextsearch 	= (isset($_POST['extsearch']) && $_POST['extsearch'] == 'on') ? 1 : 0;

$vevent_mgt 	= (isset($_POST['event_mgt']) && $_POST['event_mgt'] == 'on' )? 1 : 0;

$vallow_comment_removal = (isset($_POST['allow_comment_removal']) && $_POST['allow_comment_removal'] == 'on') ? 1 : 0;

$vactivedays = isset($_POST['activedays']) ? $_POST['activedays']:'0';

$vprice					= isset($_POST['txtprice'])?trim( $_POST['txtprice'] ):'0';

$vcurrency				= isset($_POST['txtcurrency'])?trim( $_POST['txtcurrency'] ):'';

$vname					= isset($_POST['txtname'])?trim( $_POST['txtname'] ):'';

$saveprofiles 		= (isset($_POST['saveprofiles']) && $_POST['saveprofiles'] == 'on') ? 1: 0;

$saveprofilescnt = isset($_POST['saveprofilescnt'])?$_POST['saveprofilescnt']:0;

$allow_mysettings = (isset($_POST['allow_mysettings']) && $_POST['allow_mysettings'] == 'on' )? 1: 0;

if( $vname == '' ) {

	$err = NO_NAME;

} elseif( $vprice == '' ) {

	$err = NO_PRICE;

} elseif( $vcurrency == '' ) {

	$err = NO_CURRENCY;

}

if ( isset($err) && $err != 0 ) {

	header( 'location: addmship.php?errid=' . $err );
	exit;

}


$osDB->query( 'INSERT INTO ! ' .
" ( name, chat, forum, blog, includeinsearch, message, message_keep_cnt, message_keep_days, allowim, favouritelist, sendwinks, extsearch, event_mgt,  uploadpicture, seepictureprofile, uploadpicturecnt, allowalbum, fullsignup, price,currency, activedays , messages_per_day, winks_per_day, allow_videos, videoscnt,poll, saveprofiles,saveprofilescnt, allow_mysettings, allow_comment_removal, hide, profilepicscnt)
 VALUES (  '$vname', '$vchat', '$vforum', '$vblog', '$vincludeinsearch', '$vmessage', '$vmessage_keep_cnt', '$vmessage_keep_days', '$vallowim', '$vfavouritelist', '$vsendwinks', '$vextsearch', '$vevent_mgt',  '$vuploadpicture', '$vseepictureprofile', '$vuploadpicturecnt', '$vallowalbum', 'y', '$vprice', '$vcurrency', '$vactivedays', '$messages_per_day', '$winks_per_day', '$allow_videos','$videoscnt' ,'$vpoll','$saveprofiles','$saveprofilescnt','$allow_mysettings','$vallow_comment_removal','$vhide','$vprofilepicscnt')", array( MEMBERSHIP_TABLE ) );

$id = $osDB->getOne('select id from ! where name = ?', array( MEMBERSHIP_TABLE, $vname) );

$osDB->query( 'UPDATE ! SET roleid = ? WHERE id = ?', array( MEMBERSHIP_TABLE, $id, $id ) );

header( 'location: membership.php' );
?>