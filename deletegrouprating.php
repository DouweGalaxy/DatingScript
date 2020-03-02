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

define( 'PAGE_ID', 'profile_ratings' );
if ( !checkAdminPermission( PAGE_ID ) ) {
	header( 'location: not_authorize.php' );
	exit;
}


if ($_REQUEST['action'] == get_lang('delete') ) {
	foreach( $_POST['txtid'] as $arr )
	{	$id = $arr;
		$displayorder = $osDB->getOne( 'SELECT displayorder FROM ! WHERE id = ?', array( RATINGS_TABLE, $id ) );
		$maxorder = $osDB->getOne( 'select max(displayorder) as maxorder from !', array( RATINGS_TABLE ) );
		//move the records below this record up
		$i = $displayorder + 1;
		while ( $i <= $maxorder['maxorder'] ){
			$osDB->query( 'UPDATE ! SET displayorder = ? WHERE displayorder = ?', array( RATINGS_TABLE, $i - 1, $i ) );
			$i++;
		}
		// Delete ratings
		$osDB->query ( 'DELETE FROM ! WHERE id = ?', array( RATINGS_TABLE, $id ) );
	}
}

header ( 'location: manageratings.php' );

?>