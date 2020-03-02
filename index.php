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

if (isset($_SESSION['AdminId']) && $_SESSION['AdminId'] > 0) {

	header('location: '.ADMIN_DIR.'index.php');
	exit;
}


if ((isset($_SESSION['UserId']) && $_SESSION['UserId'] <= 0)  && ((isset($_GET['page']) && $_GET['page'] == 'login') || !isset($_GET) ) &&  isset($_COOKIE[$config['cookie_prefix'].'osdate_info']) ||!isset($_SESSION['UserId']) ) {

	$cookie = $_COOKIE[$config['cookie_prefix'].'osdate_info'];

	$_SESSION['txtusername'] = $cookie['username'];

	$_SESSION['txtpassword'] = $cookie['dir'] ;

	$_SESSION['rememberme'] = true;

	if ($cookie['username'] != "") {

		if ( !$_GET['errid'] ) {
			header("location: midlogin.php");
			exit;
		}
	}
}

if ( isset( $_GET['affid'] ) ) {

	$_SESSION['ReferalId'] = $_GET['affid'];

	if ( getenv( 'HTTP_CLIENT_IP' ) ){
		$userip = getenv( 'HTTP_CLIENT_IP' );
	}
	else if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )	{
		$userip = getenv( 'HTTP_X_FORWARDED_FOR' );
	}
	else {
		$userip = getenv( 'REMOTE_ADDR' );
	}

}

$affid = isset($_GET['affid'])?$_GET['affid']:'';
$errid = isset($_GET['errid'])?$_GET['errid']:'';
$getlang = isset($_GET['lang'])?$_GET['lang']:'';

if ((isset($_GET['page']) && $_GET['page'] == 'login') && (isset($_GET['errid']) &&  $_GET['errid'] != '' ) ) {

	$t->assign ( 'login_error', get_lang('errormsgs',$_GET['errid']) );
}


if( isset( $_GET['page'] ) ) {
	$sql = '';
	$siteurl = HTTP_METHOD . $_SERVER['SERVER_NAME'] . DOC_ROOT ;

	$psize = getPageSize();

	$t->assign ( 'psize',  $psize );

	$pageno = (int)isset($_REQUEST['pageno'])?$_REQUEST['pageno']:0;

	if( $pageno == 0 ) $pageno = 1;

	$upr = ($pageno * $psize )- $psize;

	$cpage = $pageno;

	$data = array();

	switch ($_GET['page']) {

		case 'stories':

			$temp = $osDB->getAll( 'SELECT * FROM ! order by date desc', array( STORIES_TABLE ) );

			$reccnt = count($temp);

			$pages = ceil( $reccnt / $psize );

			if( $pages > 1 ) {
				$sql .= ' limit '.$upr.','.$psize;
				if ( $cpage > 1 ) {

					$prev = $cpage - 1;

					$t->assign( 'prev', $prev );

				}

				if ( $cpage < $pages ) {

					$next = $cpage + 1;

					$t->assign ( 'next', $next );

				}
				$temp = array_slice($temp,$upr,$psize);

			}

			$t->assign ( 'cpage', $cpage );

			$t->assign ( 'pages',  $pages );

			$t->assign ( 'reccount',  $reccnt );

			foreach( $temp as $index => $row ) {

				$row['username'] = $osDB->getOne( 'SELECT username FROM ! where id = ?', array( USER_TABLE, $row['sender'] ) );
				$row['text'] = stripslashes($row['text']);
				$arrtext = explode( ' ', $row['text'], $config['length_story'] + 1 );
				$arrtext[ $config['length_story'] ] = '';
				$row['text'] = trim( implode( ' ', $arrtext ) ) . '...';
				$row['date'] = date( get_lang('DISPLAY_DATE_FORMAT'), $row['date'] );

				$data []= $row;
			}
			$t->assign( 'lang', $lang );
			$t->assign ( 'data', $data );
			unset($data, $temp, $row);
			$t->assign('rendered_page', $t->fetch('allstories.tpl') );
			break;

		case 'allnews':

			$temp = $osDB->getAll( 'SELECT * FROM ! order by date desc', array( NEWS_TABLE ) );

			$reccnt = count($temp);

			$pages = ceil( $reccnt / $psize );

			if( $pages > 1 ) {
				$sql .= ' limit '.$upr.','.$psize;
				if ( $cpage > 1 ) {

					$prev = $cpage - 1;

					$t->assign( 'prev', $prev );

				}

				if ( $cpage < $pages ) {

					$next = $cpage + 1;

					$t->assign ( 'next', $next );

				}
				$temp = array_slice($temp,$upr, $psize);
			}

			$t->assign ( 'cpage', $cpage );

			$t->assign ( 'pages',  $pages );

			$t->assign ( 'reccount',  $reccnt );

			foreach( $temp as $index => $row ) {
				$row['date'] = date( get_lang('DISPLAY_DATE_FORMAT'), $row['date'] );
				$arrtext = explode( ' ', stripslashes($row['text']), $config['length_story'] + 1);
				$arrtext[ $config['length_story'] ] = '';
				$row['text'] = trim(implode( ' ', $arrtext)) . '...';

				$data []= $row;
			}
			$t->assign( 'lang', $lang );

			$t->assign ( 'data', $data );
			unset($data, $temp, $row);
			$t->assign('rendered_page', $t->fetch('allnews.tpl') );
			break;

		case 'articles':

			$temp = $osDB->getAll( 'SELECT * FROM ! order by dat desc', array( ARTICLES_TABLE ) );

			$reccnt = count($temp);
			$pages = ceil( $reccnt / $psize );
			if( $pages > 1 ) {
				if ( $cpage > 1 ) {

					$prev = $cpage - 1;

					$t->assign( 'prev', $prev );

				}

				if ( $cpage < $pages ) {

					$next = $cpage + 1;

					$t->assign ( 'next', $next );

				}
				$temp = array_slice($temp,$upr,$psize);
			}

			$t->assign ( 'cpage', $cpage );

			$t->assign ( 'pages',  $pages );

			$t->assign ( 'reccount',  $reccnt );

			foreach( $temp as $index => $row ) {

				$row['dat'] = date( get_lang('DISPLAY_DATE_FORMAT'), $row['dat'] );
				$arrtext = explode( ' ', stripslashes($row['text']), $config['length_story'] + 1 );
				$arrtext[$config['length_story']] = '';
				$row['text'] = trim(implode( ' ', $arrtext)) . '...';

				$data []= $row;
			}
			$t->assign( 'lang', $lang );

			$t->assign ( 'data', $data );

			unset ($temp, $data, $row);

			$t->assign('rendered_page', $t->fetch('allarticles.tpl') );
			break;

		case 'showstory':

			$temp = $osDB->getAll( 'SELECT * FROM ! where storyid = ?', array( STORIES_TABLE, $_GET['storyid'] ) );

			foreach( $temp as $index => $row ) {
				$row['username'] = $osDB->getOne( 'SELECT username FROM ! where id = ?', array( USER_TABLE, $row['sender'] ) );

				$row['date'] = date( get_lang('DISPLAY_DATE_FORMAT'), $row['date'] );
				$row['text'] = stripslashes($row['text']);

				$data []= $row;
			}
			$t->assign( 'lang', $lang );

			$t->assign ( 'data', $data );
			unset($data, $temp, $row);
			$t->assign('rendered_page', $t->fetch('fullstory.tpl') );
			break;

		case 'shownews':

			$temp = $osDB->getAll( 'SELECT * FROM ! where newsid = ?', array( NEWS_TABLE, $_GET['newsid'] ) );

			foreach( $temp as $index => $row ) {
				$row['date'] = date(get_lang('DISPLAY_DATE_FORMAT'), $row['date'] );
				$row['text'] = stripslashes($row['text']);
				$data []= $row;
			}
			$t->assign( 'lang', $lang );

			$t->assign ( 'data', $data );
			unset($data, $temp, $row);
			$t->assign('rendered_page', $t->fetch('fullnews.tpl') );
			break;

		case 'showarticle':
			$temp = $osDB->getAll( 'SELECT * FROM ! where articleid = ?', array( ARTICLES_TABLE, $_GET['articleid'] ) );

			foreach( $temp as $index => $row ) {
				$row['dat'] = date( get_lang('DISPLAY_DATE_FORMAT'), $row['dat'] );
				$row['text'] = stripslashes($row['text']);
				$data []= $row;
			}
			$t->assign( 'lang', $lang );

			$t->assign ( 'data', $data );
			unset($data, $temp, $row);
			$t->assign('rendered_page', $t->fetch('fullarticle.tpl') );
			break;

		case 'login':

			$t->assign('rendered_page', $t->fetch('login.tpl') );
			break;

		default:

			$row = $osDB->getRow( 'SELECT * FROM ! where pagekey = ?', array( PAGES_TABLE, $_GET['page'] ) );

			if ( $row ) {
				$row['pagetext'] = str_replace('[Your Company]', $config['site_title'],stripslashes(stripslashes($row['pagetext'])));
				$index++;
			}
			$row['pagetext'] = str_replace("#CONTACTUS#",$siteurl.'feedback.php',$row['pagetext']);

			$row['pagetext'] = str_replace("#CANCEL#",$siteurl.'cancel.php',$row['pagetext']);
			$t->assign( 'lang', $lang );

			$t->assign ( 'data', $row );
			unset($row);
			$t->assign('rendered_page', $t->fetch('page.tpl') );
	}
}

if ( strlen( $_SERVER['QUERY_STRING'] ) <= 0 or $_SERVER['QUERY_STRING'] == 'affid='.$affid || $_SERVER['QUERY_STRING'] == 'lang='.$getlang or(( $errid == NOT_YET_APPROVED or $errid == NOT_ACTIVE ) && (isset($_SESSION['UserId']) && $_SESSION['UserId'] > 0) ) ){

	$last_users = $config['no_last_new_users'];

	$list_newmembers_since_days = $config['list_newmembers_since_days'];

	if ($list_newmembers_since_days == '') $list_newmembers_since_days=0;

	$list_newmembers_since = strtotime("-$list_newmembers_since_days day",time());

	/* Modify the newest profile condition to be from last visit time if user is logged in */
	
	include("featured_profiles_display.php");

	include("newest_profiles_display.php");

	include("newuserlist_display.php");

	include("random_profiles_display.php");

	include("recent_active_profiles_display.php");

	include("newest_profpics_display.php");

	include('luckySpin_gender.php');

	include('iplocation_profiles_display.php');

	if (isset($_SESSION['UserId']) && $_SESSION['UserId'] > 0 ) {

		/* Get some stats */

		include("birthday_profiles_display.php");
		$bdp = $osDB->getOne( "SELECT user.id FROM ! as user WHERE user.status in (?, ?)  and month(user.birth_date) = month(now()) and dayofmonth(user.birth_date) = dayofmonth(now()) and user.id = ? ", array( USER_TABLE , get_lang('status_enum','active'), 'active', $_SESSION['UserId'] ) );

		if (!isset($bdp)) $bdp = 0;

		$t->assign('bdp',$bdp);

		$viewswinks_since_days = ($config['last_viewswinks_since']=='')?0:$config['last_viewswinks_since'];

		$viewswinks_since = strtotime("-$viewswinks_since_days day",time());

		if (isset($_SESSION['lastvisit'])){
			if ($viewswinks_since > $_SESSION['lastvisit']) $viewswinks_since = $_SESSION['lastvisit'];
		}

		if ($viewswinks_since < $_SESSION['regdate']) $viewswinks_since=$_SESSION['regdate'];

		$sql = 'select count(*) from ! where userid = ? and act_time >= ? and act = ?';

		$t->assign('profile_views', $osDB->getOne($sql, array( VIEWS_WINKS_TABLE, $_SESSION['UserId'], $viewswinks_since, 'V' ) ) );

		$t->assign('winks', $osDB->getOne($sql, array( VIEWS_WINKS_TABLE, $_SESSION['UserId'], $viewswinks_since, 'W' ) ) );

		$t->assign('new_messages', $osDB->getOne('select count(*) from ! where owner=? and recipientid = ? and flagread = 0 and folder = ?', array( MAILBOX_TABLE, $_SESSION['UserId'], $_SESSION['UserId'], 'inbox' ) ) );


		$usr = $osDB->getRow('select usr.levelend, usr.pictures_cnt, usr.gender, mem.name from ! usr, ! mem  where usr.id = ? and mem.roleid = usr.level', array(USER_TABLE, MEMBERSHIP_TABLE, $_SESSION['UserId']) );

		$t->assign('profpicscnt', $osDB->getOne('select count(*)  from ! where userid = ? and (album_id is null or album_id = 0)', array(USER_SNAP_TABLE, $_SESSION['UserId'])) );

		$t->assign('albumpicscnt', $osDB->getOne('select count(*)  from ! where userid = ? and album_id > 0', array(USER_SNAP_TABLE, $_SESSION['UserId'])) );

		$levelend = $usr['levelend'];

		$end_date = strftime($lang['DATE_FORMAT'],$levelend);

		$t->assign('curlevel', $usr['name']);

		$diff=$levelend - (time()+0);

		$bal_days = round($diff/86400,0);

		if ($bal_days == -0) $bal_days=0;

		$t->assign('bal_days', $bal_days );

		$t->assign('end_date', $end_date );

		$t->assign('viewswinks_since', strftime($lang['DATE_FORMAT'],$viewswinks_since));

		/* Now see the profile questions this user has answered */
		$profquestions_must_cnt = $osDB->getOne('select count(*) from ! where enabled=? and mandatory = ? and ifnull(gender,?) in (?,?)', array(QUESTIONS_TABLE, 'Y', 'Y', 'A', 'A', $usr['gender']) );

		$profquestions_nonmust_cnt = $osDB->getOne('select count(*) from ! where enabled = ? and mandatory <> ? and ifnull(gender,?) in (?,?)', array(QUESTIONS_TABLE, 'Y', 'Y', 'A', 'A', $usr['gender']) );

		$profquestions_must_ans_cnt = $osDB->getOne('select count(distinct pref.questionid) from ! as pref, ! as qst where pref.questionid = qst.id and qst.enabled=? and qst.mandatory = ? and ifnull(qst.gender,?) in (?,?) and pref.userid = ? ', array(USER_PREFERENCE_TABLE, QUESTIONS_TABLE, 'Y', 'Y', 'A', 'A', $usr['gender'], $_SESSION['UserId'] ) );

		$profquestions_nonmust_ans_cnt = $osDB->getOne('select count(distinct pref.questionid) from ! as pref, ! as qst where pref.questionid = qst.id and qst.enabled=? and qst.mandatory <> ? and ifnull(qst.gender,?) in (?,?) and pref.userid = ? ', array(USER_PREFERENCE_TABLE, QUESTIONS_TABLE, 'Y', 'Y', 'A', 'A', $usr['gender'], $_SESSION['UserId']) );

		$totalQuestionsValue = ($profquestions_must_cnt * 3) + $profquestions_nonmust_cnt;
		$totalAnsweredValue = ($profquestions_must_ans_cnt * 3) + $profquestions_nonmust_ans_cnt;

		$t->assign('profquestions_must_cnt',$profquestions_must_cnt);
		$t->assign('profquestions_nonmust_cnt',$profquestions_nonmust_cnt);

		$t->assign('profquestions_must_ans_cnt',$profquestions_must_ans_cnt);
		$t->assign('profquestions_nonmust_ans_cnt',$profquestions_nonmust_ans_cnt);

	}
	$t->assign('lang', $lang);

	$t->assign('rendered_page', $t->fetch('homepage.tpl') );
}

if (isset($_GET['errid']) && $_GET['errid'] != '') {

	$t->assign('errid_message', get_lang('errormsgs',$_GET['errid']) );

	$_GET['errid_message'] = urlencode(get_lang('errormsgs',$_GET['errid']));
}

$t->assign('simplesearch', $_SESSION['simplesearch']);

$lang['DATE_FORMAT'] = get_lang('DATE_FORMAT');

$t->assign('lang', $lang);

$t->display( 'index.tpl' );

exit();
?>