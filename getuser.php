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

$usertbl = USER_TABLE;
$onlinetbl = ONLINE_USERS_TABLE;

// get active users from past 60 seconds

$data = $osDB->getAll( "SELECT id, username
		FROM $usertbl
			INNER JOIN $onlinetbl ON $usertbl.id = $onlinetbl.userid
				WHERE 	$usertbl.allow_viewonline = ?  AND
						( $usertbl.status = ? or $usertbl.status = ? ) AND
						$usertbl.id <> ? AND
						unix_timestamp() - $onlinetbl.lastactivitytime < 60 ", array( '1', 'active', get_lang('status_enum','active'), $_SESSION['UserId'] ) );

$xml = '<?xml version="1.0"?>';

$xml .= "<users>";

if( strlen($data) > 0 ){

	foreach ( $data as $user ) {
		$xml .= '<user userid="'.$user['id'].'" username="'.$user['username'].'" />';
	}
}

$xml .= "</users>";

print( $xml );
unset($xml);
?>