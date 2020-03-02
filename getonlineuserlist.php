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
	include_once( 'minimum_init.php' );
}

$sql = 'SELECT distinct u.username, u.id, u.gender FROM ! u, ! ou WHERE u.allow_viewonline=? AND u.status in (?,?) AND u.id = ou.userid and u.id <> ? and ou.lastactivitytime > ? order by u.username';

$data = $osDB->getAll( $sql, array( USER_TABLE, ONLINE_USERS_TABLE, '1', get_lang('status_enum','active'), 'active', $_SESSION['UserId'], time()-300) );

$rcount = count($data);

$ret='';

if ( $rcount > 0 ) {
	foreach ($data as $usr) {
		$ret.='<a href="javascript:popUpScrollWindow2(\''.DOC_ROOT;
		if ($config['enable_mod_rewrite'] == 'Y') {
			if ($config['seo_username'] == 'Y') {
				$ret.= $usr['username'];
			} else {
				$ret.= $usr['id'].'.htm';
			}
		}else{
			$ret.='showprofile.php?';
			if ($config['seo_username'] == 'Y') {
				$ret.='username='.$usr['username'];
			}else{
				$ret.='id='.$usr['id'];
			}
		}
		$ret.='\',\'top\',650,600)">';
		$ret.=$usr['username'].' ('.get_lang('signup_gender_values',$usr['gender']).')</a><br />';
	}
} else {
	$ret.=get_lang('noone_online');
}
echo '|||onlineUserList|:|'.$ret;
?>