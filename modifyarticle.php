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

define( 'PAGE_ID', 'article_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$modified['articleid'] = $id = $_POST['id'];

$day = $_POST['txtDay'];

$mmm = $_POST['txtMonth'];

$yyy = $_POST['txtYear'];

$modified['dat'] = $dat = strtotime( $day . ' ' . $mmm . ' ' . $yyy );

$modified['title'] = $title = $_POST['txttitle'];

$modified['text'] = $text = ltrim(rtrim($_POST['txttext']));

$err = 0 ;

if ( $title == '' ) {

	$err = NO_HDR;

} elseif ( $text == '' || strlen($text) <= 0 ) {

	$err = NO_TEXT;
}

if ( $err != 0 ) {

	$_SESSION['modified'] = array('dat'=>$dat, 'title'=>$title, 'text'=>$text);

	header( 'location: managearticle.php?edit=' . $id . '&errid=' . $err );

	exit;

}

$title = eregi_replace('</?[a-z][a-z0-9]*[^<>]*>', '', $title );

$osDB->query('UPDATE ! SET title = ?, dat = ?, text = ? WHERE articleid = ?', array( ARTICLES_TABLE, $title, $dat, $text, $id ) );

header( 'location: managearticle.php' );

exit;

?>