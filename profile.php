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

define( 'PAGE_ID', 'profile_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$psize = getPageSize();

if (isset($_GET['status']) && $_GET['status'] != '') {
	$_SESSION['query_for_status'] = $_GET['status'];
}

//Default Sorting
$sort = findSortBy();

$data=array();

//For Editing Sections
if ( isset($_GET['edit']) ) {


	if (isset($_SESSION['modifiedrow']) && $_SESSION['modifiedrow'] != '') {

		$data = $_SESSION['modifiedrow'];

		$data['level'] = $data['mlevel'];

		$data['id'] = $_GET['edit'];

		$_SESSION['modifiedrow'] = '';

	} else {

		$data = $osDB->getRow( 'SELECT * from ! Where id = ?', array( USER_TABLE, $_GET['edit'] ) );

	}

	if ($config['accept_state'] =='1' || $config['accept_state'] =='Y') {

		$lang['states'] = getStates($data['country'],'N');
		
		$statecode = (isset($data['state_province']) && $data['state_province'] != '')?$data['state_province']:'AA';

	} else {
		$statecode = 'AA';
	}

	if ($config['accept_county'] =='1' || $config['accept_county'] =='Y') {

		$lang['counties'] = getCounties($data['country'], $statecode, 'N');

		if (count($lang['counties']) == 1) {
			foreach ($lang['counties'] as $key => $val) {
				$data['county'] = $key;
			}
		}

		$countycode = (isset($data['county']) && $data['county'] != '')?$data['county']:'AA';
	} else {
		$countycode = 'AA';
	}

	if ($config['accept_city'] =='1' || $config['accept_city'] =='Y') {

		$lang['cities'] = getCities($data['country'], $statecode, $countycode, 'N');

		if (count($lang['cities']) == 1) {
			foreach($lang['cities'] as $key => $val) {
				$data['city'] = $key;
			}
		}
		$citycode = (isset($data['city']) && $data['city'] != '')?$data['city']:'AA';
	} else {
		$citycode = 'AA';
	}

	if ($config['accept_zipcode'] =='1' || $config['accept_zipcode'] =='Y') {


		$lang['zipcodes'] = getZipcodes($data['country'], $statecode, $countycode, $citycode, 'N');
	}

	$lang['tz'] = get_lang_values('tz');

	$t->assign( 'lang', $lang );

	if (isset($_GET['errid']) ) {
		$t->assign( 'error_message', get_lang('errormsgs', $_GET['errid'] ) );
	}

	$t->assign( 'user', $data );

	unset($data);

	$_SESSION['UserId'] = $_GET['edit'];

	$t->assign( 'mships', getMembershipsInfo() );

	$t->assign('rendered_page', $t->fetch('admin/profileedit.tpl'));

	$t->display( 'admin/index.tpl' );

	exit;
}

//For Deletion of profiles
if ( isset($_GET['txtdelete']) ) {

	deleteUser($_GET['txtdelete']);

	$t->assign('errmsg', PROFILE_DELETED);

	$t->assign('error_message', get_lang('errormsgs', PROFILE_DELETED) );

	if (isset($_SESSION['UserId'])) unset($_SESSION['UserId']);

	if (isset($_GET['returnto']) && $_GET['returnto'] != '' ) {
		header("Location: ".$_GET['returnto']."?error_message=".urlencode(get_lang('errormsgs', PROFILE_DELETED)) );
		exit();
	}
} elseif (isset($_POST['delete_selected']) && $_POST['delete_selected'] == get_lang('delete_selected')) {

	@set_time_limit(1200);

	$arr = $_POST['txtchk'];

	if (count($arr) > 0) {
		/* Delete profile routine */
		foreach ($arr as $userId) {
			deleteUser($userId);
		}
		unset($arr);

		$t->assign('errmsg', PROFILES_DELETED);
		$t->assign('error_message', get_lang('errormsgs', PROFILE_DELETED) );
	}
}


$mships = $osDB->getAll('select roleid, activedays, name from ! order by roleid',array( MEMBERSHIP_TABLE) );

$memberships = array();

$membership_names = array();

foreach ($mships as  $val) {

	$memberships[$val['roleid']] = $val['activedays'];

	$membership_names[$val['roleid']] = $val['name'];
}

unset($mships);

if ( isset($_POST['groupaction']) ) {

	$arr = $_POST['txtchk'];

	$status = 	$_POST['groupaction'];

	foreach ($lang['status_act'] as $key=>$val) {

		if ($val == $_POST['groupaction']) {

			$status = $key;
		}
	}

	if ( $status == get_lang('changeto') and count($arr) > 0 ) {

		$level =  $_POST['txtmlevel'];

		foreach( $arr as $val ) {
			if ($val != '' ) {
				$userlevel = $osDB->getRow('select * from ! where id = ?', array( USER_TABLE, $val) );

				if ($userlevel['levelend'] == '' || $level != $userlevel['level'] || $userlevel['levelend'] < time() ) {

					$userlevel['levelend'] = time();

				}

				$add_days = $memberships[$level];

				$levelend = $userlevel['levelend'];

				$levelend = strtotime("+$add_days day",$levelend);

				$osDB->query( 'UPDATE !  SET level = ?, levelend = ?  WHERE id = ?', array( USER_TABLE, $level,  $levelend, $val ) );

				/* Now send email to member about this change */

				$message = get_lang('profile_membership_changed', MAIL_FORMAT);

				$Subject =  get_lang('profile_membership_changed_sub') ;

				$From = $config['admin_email'];

				$To = $userlevel['email'];

				$message = str_replace('#FirstName#', $userlevel['firstname'],$message);

				$message = str_replace('#ValidDate#',date(get_lang('DISPLAY_DATE_FORMAT'), $levelend), $message);

				$message = str_replace('#CurrentLevel#', $membership_names[$userlevel['level']], $message);

				$message = str_replace('#NewLevel#', $membership_names[$level], $message);

				$success = mailSender($From, $To, $userlevel['email'], $Subject, $message);
				unset($message, $Subject, $From, $To);
			}
		}
		unset($arr);
	} elseif ( count($arr) > 0 ) {

		foreach( $arr as $val ) {

			if ($val != '') {
				$usr = $osDB->getRow('select * from ! where id = ?', array( USER_TABLE, $val) );

				if ($usr['levelend'] == '' || $usr['levelend'] < time() ) {

					$usr['levelend'] = time();

				}

				$levelend = $usr['levelend'];

				$add_days = $memberships[$usr['level']];

				$osDB->query( "UPDATE ".USER_TABLE." SET status = '".$status."' WHERE id = '". $val ."'" ) ;

				if ($status == 'active' or $status == get_lang('status_act','active') ) {

					$osDB->query( "UPDATE ".USER_TABLE." SET active = 1, actkey='Confirmed'  WHERE id = '". $val ."'" ) ;

					/* Send activation email to the member */

					$message = get_lang('profile_activated', MAIL_FORMAT);

					$Subject =  get_lang('profile_activated_sub') ;

					$From = $config['admin_email'];

					$To = $usr['email'];

					$message = str_replace('#FirstName#', $usr['firstname'],$message);

					$message = str_replace('#AdminName#', $config['admin_name'],$message);

					$message = str_replace('#ValidDate#',date(get_lang('DISPLAY_DATE_FORMAT'), $usr['levelend']), $message);

					$message = str_replace('#MembershipLevel#', $membership_names[$usr['level']], $message);

					$success = mailSender($From, $To, $usr['email'], $Subject, $message);
					unset($message, $Subject, $From, $To);
				} elseif ( $status == 'cancel' || $status == 'suspended') {
					$osDB->query( "UPDATE ".USER_TABLE." SET active = 0  WHERE id = '". $val ."'" ) ;
				}
			}
		}
		unset($arr);
	}
}

$t->assign("page_hdr01_text",get_lang('profile_title') );

$t->assign ( 'psize',  $psize );

$page_size = $psize;

$page = (int)(isset($_GET['offset'])?$_GET['offset']:1);

if( $page == 0 ) $page = 1;

$upr = ($page)*$page_size - $page_size;

/* added in 2.5 - detailed members list linked from panel.tpl */
if (isset($_GET['statfor']) ) {
	$time = $_GET['time'];
	$sql = 'select SQL_CALC_FOUND_ROWS * from ! where ';
	switch ($_GET['statfor']) {
		case 'usersinpastminute':
			$time = $time - 60;
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpastminute') );
			break;
		case 'usersinpasthour':
			$time = $time - (60*60);
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpasthour') );
			break;
		case 'usersinpastday':
			$time = $time - (24*60*60);
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpastday') );
			break;
		case 'usersinpastweek':
			$time = strtotime("-1 week",$time );
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpastweek') );
			break;
		case 'usersinpastmonth':
			$time = strtotime("-1 month",$time );
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpastmonth') );
			break;
		case 'usersinpastyear':
			$time = strtotime("-1 year",$time );
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpastyear') );
			break;
		case 'usersinpast2years':
			$time = strtotime("-2 year",$time );
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpast2years') );
			break;
		case 'usersinpast5years':
			$time = strtotime("-5 year",$time );
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpast5years') );
			break;
		case 'usersinpast10years':
			$time = strtotime("-10 year",$time );
			$sql .= ' lastvisit > '.$time;
			$t->assign("page_hdr01_text",get_lang('usersinpast10years') );
			break;
		case 'allusers':
			$sql .= ' id > 0 ';
			break;
		case 'activeusers':
			$sql .= " status in ('active','". get_lang('status_enum','active')."') ";
			$t->assign("page_hdr01_text",get_lang('totalactiveusers') );
			break;
		case 'pendingusers':
			$sql .= " status in ('approval','". get_lang('status_enum','approval')."') ";
			$t->assign("page_hdr01_text",get_lang('totalpendingusers') );
			break;
		case 'suspendedusers':
			$sql .= " status in ('suspend','". get_lang('status_enum','suspend')."') ";
			$t->assign("page_hdr01_text",get_lang('totalsuspendedusers') );
			break;
		case 'pictureusers':
			$sql .= " pictures_cnt > 0 ";
			$t->assign("page_hdr01_text",get_lang('totalpictureusers') );
			break;
		case 'membershiplevel':
			$sql .= " level =  ".$_GET['level'] ;
			$t->assign("page_hdr01_text",$membership_names[$_GET['level']].' '.get_lang('members'));
			break;
		case 'gender':
			$sql .= " gender =  '".$_GET['gender']."' " ;
			$t->assign("page_hdr01_text",get_lang('stats_gender_values',$_GET['gender']) );
			break;

		default:
	}

	$sql .= ' ORDER BY ' . $sort . " LIMIT $upr,$page_size ";

}elseif ( isset($_POST['filter']) && $_POST['filter'] == 1 ) {

	$searchat = $_POST['txtsrchat'];

	$search = $_POST['txtsearch'];
	if ($searchat == 'status') $search = strtolower($search);

	if ($search == 'pending') $search='approval';

	$sql = "SELECT SQL_CALC_FOUND_ROWS  * FROM ! WHERE $searchat LIKE '%$search%'";

} else {

	$sql = 'SELECT SQL_CALC_FOUND_ROWS  * FROM ! ';
	if (isset($_SESSION['query_for_status']) && $_SESSION['query_for_status'] != '') {
		$sql .= " where status = '".strtolower($_SESSION['query_for_status'])."' ";
	}
	$sql .= ' ORDER BY ' . $sort . " LIMIT $upr,$page_size ";

}



$data = $osDB->getAll( $sql, array( USER_TABLE ) );
$reccount = $osDB->getOne('select FOUND_ROWS()');

$t->assign('filter_options', get_lang_values('filter_options'));

$t->assign ( 'total_recs',  $reccount );

$total_pages = ceil( $reccount / $page_size );

$pages_vals = array();

for( $i=1; $i<=$total_pages; $i++ ) { $pages_vals[$i] = $i; }

$t->assign ( 'total_pages',  $pages_vals );

$page_limit = 5;
$j = 1;
if ( $page > 2 ) { $page = $page - 2; } else { $page = 1; }
for( $i=$page; $i<=$total_pages; $i++ ) {
	$pages_show[$i] = $i;

	$j++;
	if ( $j > $page_limit )	break;
}

$t->assign ( 'pages_show',  (isset($pages_show)?$pages_show:1) );

$t->assign ( 'reccount',  $reccount );

$t->assign( 'lang', $lang );

$t->assign( 'sort_type', ((isset($_GET['type']) && $_GET['type'] =='asc' )?'desc':'asc' ) );

/* if ($_GET['type'] == 'desc') {
	$srttype='asc';
} else {
	$srttype='desc';
}
*/
$srt='';
if (isset($_GET['sort']) ) {
	$srt = 'sort='.$_GET['sort'];
	if (isset($_GET['type'])) {
		$srt.='&amp;type='.$_GET['type'];
	}
}
if (isset($_GET['statfor']) ){
	$srt.='&amp;time='.$_GET['time'].'&amp;statfor='.$_GET['statfor'];
	if (isset($_GET['level']) ) $srt.='&amp;level='.$_GET['level'];
	if (isset($_GET['gender']) ) $srt.='&amp;gender='.$_GET['gender'];
}

$t->assign( 'querystring', $srt);

$t->assign( 'upr', $upr );

$usersinfo = array();

foreach ($data as $rec) {

	$feat = $osDB->getOne('select userid from ! where userid = ?',array(FEATURED_PROFILES_TABLE, $rec['id']) );
	if ( $feat ==$rec['id'] ) {
		$rec['featured'] = '1';
	}

	$rec['picscnt'] = $osDB->getOne('select count(*) from ! where userid = ?', array(USER_SNAP_TABLE, $rec['id']) );

	$rec['videoscnt'] = $osDB->getOne('select count(*) from ! where userid = ?', array(USER_VIDEOS_TABLE, $rec['id']) );

/* Modify the status flag if the profile is not activated by the user. */
	if ($rec['active'] != '1' && $rec['active'] != 'Y'  && $rec['status'] != 'cancel'  && $rec['status'] != 'suspended') {
		$rec['status'] = 'approval';
	}

	$usersinfo[]=$rec;

}

$t->assign( 'data', $usersinfo );

unset($userinfo);

$t->assign('nowdate', time());

$t->assign( 'mships', getMembershipsInfo() );

$t->assign('rendered_page', $t->fetch('admin/profile.tpl'));

$t->display( 'admin/index.tpl' );

exit;
?>