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


require_once(dirname(__FILE__).'/../init.php');

if (!isset($_REQUEST['a']) || empty($_REQUEST['a']) ) return '';

switch (trim($_REQUEST['a'])) {

	case 'getUsers':

		$text = str_replace('|amp|','&amp;',strip_tags($_REQUEST['msg']));

		$users = $osDB->getAll( 'select username from ! where username like ?', array( USER_TABLE, '%'.$text.'%' ) );

		$ret = '<select name="reqdusers" id="reqdusers"  multiple style="width: 90px;">';
		foreach ($users as $user) {
			$ret.='<option value="'.$user['username'].'">'.$user['username'].'</option>';
		}
		unset($users);

		$ret.='</select>&nbsp;';
		$ret.='<input type="button" value="'.get_lang('ok').'" class="formbutton" onclick="selectedUsers();" />';

		echo '|||usernameFind|:|'.$ret;
		unset($ret);
		break;

	default : return ''; break;
}

?>
