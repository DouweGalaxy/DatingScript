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
	include_once( 'init.php' );
}

$email = isset($_POST['txtemail'])?trim( $_POST['txtemail'] ):'';

if ( $email == '' ) {

	header( 'location: forgotpass.php?errid=1' );
	exit;
}

$row = $osDB->getRow( 'SELECT id, username, firstname, lastname, password FROM ! WHERE email = ?', array( USER_TABLE, $email ) );

if ( $row && $row['id'] > 0 ) {

	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';

	$pwd = '';

	for( $i = 0; $i < 8; $i++ ) {

		$rand = (rand( 0, strlen( $chars ) )-1);
		$pwd .= $chars{$rand};
	}

	$osDB->query( 'UPDATE ! SET password = ? WHERE id=?', array( USER_TABLE, md5( $pwd ), $row['id'] ) );

	if ($config['forum_installed'] != '' && $config['forum_installed'] != 'None') {

		include_once(FORUM_DIR.$config['forum_installed'] . '_forum.php');
		
		forum_modifympass($pwd, $row['username']);

		$osDB = new osDateDB;

	}

	$subject = get_lang('forgot_password_sub');

	$body = get_lang('forgot_password', MAIL_FORMAT);

	$name = $row['firstname'] ;

	$body = str_replace( '#Name#', $name , $body );

	$body = str_replace( '#ID#',  $row['username'] , $body );

	$body = str_replace( '#Password#', $pwd, $body );

	$body = str_replace( '#LoginLink#',  HTTP_METHOD . $_SERVER['SERVER_NAME']  . DOC_ROOT.'login.php' , $body );

	$body = str_replace( '#SiteTitle#',  $config['site_name'] , $body );

	$From    = $config['admin_email'] ;
	$To     = $name . ' <' . $email . '>';


	$success=mailSender($From, $To, $email, $subject, $body);
	unset($body, $subject, $row);
	if( $success ) {
		header( 'location: forgotpass.php?errid='.PASSWORD_MAIL_SENT );
		exit;
	}
	else {
		header( 'location: forgotpass.php?errid='.MAIL_ERROR );
		exit;
	}
} else {
		header( 'location: forgotpass.php?errid='.NOT_REGISTERED );
		exit;
}
?>