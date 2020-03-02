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

define( 'PAGE_ID', 'poll_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$id = trim( $_POST['txtid'] );

$question = trim( $_POST['txtquestion'] );

$err = 0;
if ( $question == '' ) {

	$err = OPTION_BLANK;
}

if ( $err != 0 ) {

	header ( 'location: managepoll.php?edit=' . $_POST['txtid'] . '&errid=' . $err );
	exit;

}

$dat = strtotime( $_POST['txtdateYear'].'-'.$_POST['txtdateMonth'].'-'.$_POST['txtdateDay'] );

$question = eregi_replace('</?[a-z][a-z0-9]*[^<>]*>', '', $question );

$osDB->query( 'UPDATE ! SET question = ?, date = ?  WHERE pollid = ?', array( POLLS_TABLE, $question, $dat, $id ) );

header ( 'location: managepoll.php' );
?>