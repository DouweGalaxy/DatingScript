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

define( 'PAGE_ID', 'change_pwd' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$id = $_POST['txtid'];

$old = $_POST['txtoldpwd'];

$new = $_POST['txtnewpwd'];

$con = $_POST['txtconpwd'];


$err = 0;

if ( $old == '' ) {

	$err = OLDPWD_BLANK;
} elseif ( $new == '' ) {

	$err = NEWPWD_BLANK;

} elseif ( $con == '' ) {

	$err = CONFPWD_BLANK;

}

if ( strcmp( $new, $con ) != 0 ) {

	$err = DIFF_PASSWORDS;
}

if ( $err > 0 ) {

	header( 'location: changepwd.php?errid=' . $err );
	exit;

}

$usrname = $osDB->getOne( 'SELECT username FROM ! WHERE id = ? AND password = ?', array( ADMIN_TABLE, $id, md5( $old ) ) );

if ( $usrname != '' ) {

	$osDB->query( 'UPDATE ! SET password = ? WHERE id = ?', array(ADMIN_TABLE, md5( $con ), $id ) );

	if ($config['forum_installed'] != '' && $config['forum_installed'] != 'None') {
	    include_once(FORUM_DIR.$config['forum_installed'] . '_forum.php');
		forum_modifypwd($usrname,$con);
	}

} else {

	header( 'location: changepwd.php?errid='.WRONG_PASSWORD );
	exit;

}

header( 'location: pwdchanged.php' );

exit;

?>