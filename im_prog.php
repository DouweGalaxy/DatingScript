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


require_once(dirname(__FILE__).'/init.php');

if (!isset($_REQUEST['a']) || empty($_REQUEST['a']) ) return '';

function is_in_mylist($userid) {
	global $osDB;
	$bdy = $osDB->getAll('select act from ! where userid = ? and ref_userid = ? ', array(BUDDY_BAN_TABLE, $_SESSION['UserId'], $userid ) );
	return $bdy;
}

/* First update the online users table for this user being online */

$ping_time = time();

/* not needed as separate ajax update is occuring
$osDB->query("update ! set is_online=1, last_ping=? WHERE userid=?", array(ONLINE_USERS_TABLE, $ping_time, $_SESSION['UserId'] ) );
*/
$msg_sent = '';

switch (trim($_REQUEST['a'])) {

	case 'sendMsg':
		/* Update message table */
		$osDB->query('insert into ! (senderid, recipientid, message, sendtime) values (?, ?, ?, ?)', array(INSTANT_MESSAGE_TABLE, $_SESSION['UserId'], $_REQUEST['refuid'], str_replace('|amp|','&amp;',$_REQUEST['msg']), $ping_time) );
		$msg = $osDB->getAll('select * from ! where recipientid = ? order by sendtime desc', array(INSTANT_MESSAGE_TABLE, $_SESSION['UserId']));
		$max_cnt = ($config['im_totmsg_count']>0)?$config['im_totmsg_count']:100;

		if (count($msg) >= $max_cnt) {
			for($ix=$max_cnt-1; $ix < count($msg); $ix++) {
				$osDB->query('delete from ! where id = ?', array(INSTANT_MESSAGE_TABLE, $msg[$ix]['id']) );
			}
		}

		$msg_sent = '|||newMsg|:|<textarea onFocus="javascript:clearInput(this);" style="height:100%;width:100%;overflow:auto;border:0;" name="message" id="im_msg" name="im_msg" onkeypress="keyHandler(event);" >Message sent</textarea>';
		print ($msg_sent);
		unset($msg_sent);
		break;

	case 'ping':
		print '|||userList|:|' .
				getUserList() .
				'|||msgArea|:|'.getMsg();
		break;
	default : return ''; break;
}

function getUserList() {

	global $osDB;

	$data = $osDB->getAll('select usr.id, usr.username from ! as onl, ! as usr, ! as mem where usr.id=onl.userid and onl.is_online=1 and onl.userid <> ! and mem.roleid = usr.level and mem.allowim = ? and usr.allow_viewonline = ? order by usr.username', array(ONLINE_USERS_TABLE, USER_TABLE, MEMBERSHIP_TABLE, $_SESSION['UserId'], '1', '1' ) );

	if (count($data) <= 0) {
		return get_lang('noone_online').
			'<script type="text/javascript">selectedUser(" "," ");</script>';
	}
	$dataok=0;
	$ret='<table border=0 width="100%" cellspacing="0" cellpadding="0">';
	foreach ($data as $dta) {
		$lst = '';
		$bdy = is_in_mylist(trim($dta['id']));

		if (count($bdy)> 0) {
			foreach ($bdy as $ac) {
				if ($ac['act'] == 'H') {
					$lst.='&nbsp;<img src="'.DOC_ROOT.'images/hot_list.gif" height="10" width="10" alt="" align="baseline" title="User is in Hot List" />';
				} elseif ($ac['act'] == 'F') {
					$lst.='&nbsp;<img src="'.DOC_ROOT.'images/buddy_list.gif" height="10" width="10" alt="" align="baseline" title="User is in Buddy List" />';
				} elseif ($ac['act'] == 'B') {
					$lst = 'B';
				}
			}
		}
		if ($lst != 'B') {
			$dataok++;
			$ret.= '<tr><td width="75%" height="6"><a onClick="selectedUser('."'".$dta['username']."','".$dta['id']."'); im_refuid='".$dta['id'] . "';" . '">'.$dta['username'].'</a></td><td width="25%" height="6">'.$lst.'</td></tr>';
		}
	}
	unset($data);
	if ($dataok == 0) return get_lang('noone_online').
	'<script type="text/javascript">selectedUser(""); document.getElementById("im_refuid").value="";</script>';

	$ret .= '</table>';
	return $ret;
}

function getMsg() {
	/* Get Messages for this user */
	global $osDB, $config;
	$ret = '';

	$msg_cnt = ($config['im_dispmsg_count']>0)?$config['im_dispmsg_count']:20;

	$messages = $osDB->getAll('select msg.id as msgid, msg.senderid, msg.message, msg.sendtime, msg.pingflag, usr.username as sendername from ! as msg, ! as usr where usr.id = msg.senderid and msg.recipientid = ! order by sendtime desc ', array(INSTANT_MESSAGE_TABLE, USER_TABLE, $_SESSION['UserId']) );

	if (count($messages) <= 0) return $ret.=get_lang('no_im_msgs');

	$cnt = 0;

	foreach ($messages as $msg) {
		$cnt++;


		if ($cnt <= $msg_cnt) {

			$ret.= '<font class=im_msg><b><a onClick="selectedUser('."'".$msg['sendername']."','".$msg['senderid']."'); im_refuid='".$msg['senderid'] . "';" . '">'.$msg['sendername'].'</a></b>:&nbsp;'.stripslashes($msg['message']).'</font><br />';

		}

	}
	unset($messages);
	return $ret;
}
?>
