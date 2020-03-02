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
	include_once( dirname(__FILE__).'/../init.php' );
}


if( isset($_GET['username']) && $_GET['username'] != '') {
	$userid = $osDB->getOne( 'SELECT id FROM ! WHERE username = ? ', array( USER_TABLE, $_GET['username'] ) );
	$_REQUEST['id'] = $userid;
}


if( isset($_REQUEST['id']) & $_REQUEST['id'] != '' && (int)$_REQUEST['id'] != 0 ){

	$user=$osDB->getRow('SELECT id, username , level, country , firstname , lastname, gender , lookgender, state_province , lastvisit, about_me, couple_usernames,
		picture , city , floor((to_days(curdate())-to_days(birth_date))/365.25)   as age
		FROM ! WHERE id = ?' ,array( USER_TABLE, $_REQUEST['id'] ));

	if (!$user || $user['id'] != $_REQUEST['id']) {
		/* No such user. Display error message and exit */
		$t->assign('error_message', get_lang('no_such_user'));
		$t->assign('rendered_page', $t->fetch('nickpage.tpl'));
		$t->display('admin/index.tpl');
		exit;
	}

	/* Get countryname and statename */

	$user['countryname'] = getCountryName($user['country']);

	$user['statename'] = getStateName($user['country'], $user['state_province']);

	$user['m_status'] = checkOnlineStats( $user['id']  );

	$user['pub_pics'] = $osDB->getAll('select picno from ! where userid=? and (album_id is null or album_id = 0) order by picno', array(USER_SNAP_TABLE, $user['id']) );

	$dataSections = $osDB->getAll( 'SELECT sect.* FROM ! sect WHERE sect.enabled = ? and sect.id in (select distinct section from ! where gender in (?,?)) ORDER BY displayorder', array( SECTIONS_TABLE, 'Y', QUESTIONS_TABLE,$user['gender'],'A'   ) );


	$found = false;

	foreach( $dataSections as $section ){

		$prefs = array();

		$rsPref = $osDB->getAll( 'SELECT DISTINCT q.id, q.question, q.extsearchhead,
			q.control_type as type FROM ! pref INNER JOIN ! q ON pref.questionid = q.id WHERE pref.userid = ? AND q.section = ?  and q.gender in (?,?) and q.enabled = ? ORDER BY q.displayorder ',array( USER_PREFERENCE_TABLE, QUESTIONS_TABLE, $_REQUEST['id'], $section['id'],$user['gender'],'A','Y') );

		foreach( $rsPref as $row ){
			if ($_SESSION['opt_lang'] != 'english') {
			/* THis is made to adjust for multi-language */
				$lang_question = $_SESSION['profile_questions'][$row['id']]['question'];
				$lang_extsearchhead = $_SESSION['profile_questions'][$row['id']]['extsearchhead'];
				if ($lang_question != '') {
					$row['question'] = $lang_question;
					$row['extsearchhead'] = $lang_extsearchhead;
				}
			}


			if ($row['type'] != 'textarea') {

                $rsOptions = $osDB->getAll( 'SELECT distinct pref.answer as answer, opt.answer as anstxt from ! pref left join ! opt on pref.questionid = opt.questionid and opt.id = pref.answer where pref.userid = ? and opt.questionid = ? order by opt.questionid, opt.displayorder', array( USER_PREFERENCE_TABLE, OPTIONS_TABLE, $_REQUEST['id'], $row['id'] ) );

			} else {

                $rsOptions = $osDB->getAll( 'select distinct pref.answer as answer, pref.answer as anstxt from ! pref where pref.userid = ? and pref.questionid = ?', array( USER_PREFERENCE_TABLE, $_REQUEST['id'], $row['id'] ) );
			}

			$opts = array();

			foreach( $rsOptions as $key=>$opt ){
				if ($_SESSION['opt_lang'] != 'english') {
				/* THis is made to adjust for multi-language */
					$lang_ansopt = $_SESSION['profile_questions'][$row['id']][$opt['answer']];
					if ($lang_ansopt != '') {$opts[] = $lang_ansopt;
					}else{ $opts[] = $opt['anstxt'];}
				} else {
					$opts[] = $opt['anstxt'];
				}
			}

			unset($rsOptions);

			if (count($opts)>0) {
				$optsPhr = implode( ', ', $opts);
			} else {
				$optsPhr = "";
			}

			$row['options'] = $optsPhr;

			$prefs[] = $row;

			unset($optsPhr, $opts);

			$found = true;
		}

		if( count($prefs) > 0 ){

			$pref[] = array( 'SectionName' => $lang['sections'][$section['id']], 'preferences' => $prefs, 'SectionId'=>$section['id'] );
		}
	}

	unset($dataSections, $prefs);


	hasRight('');
	$cplusers = array();

	if ($user['couple_usernames'] != '' && $user['gender'] == 'C') {

		foreach (explode(',',$user['couple_usernames']) as $cpl) {
			$refuid = $osDB->getOne('select id from ! where username = ?', array(USER_TABLE, trim($cpl)));

			$cplusers[]=array('username' => trim($cpl),
								'uid' => $refuid) ;
		}

		$user['cplusers'] = $cplusers;
	}

	$t->assign( 'user', $user );

	$t->assign('title',str_replace('USERNAME', $user['username'], get_lang('profile_s')));

	$arr = array();

	for( $i=-5; $i<=5; $i++ ) {
		$arr[$i] = $i;
	}

	$t->assign ( 'rate_values', $arr );

	/* MOD START */

	// remove comment //

	if (isset($_GET["action"]) && $_GET["action"] == "removecomment") {

		$osDB->query( 'UPDATE ! SET reply = ? WHERE id = ?', array( USER_RATING_TABLE, '', substr($_GET["commentid"],0,250) ) );


	}
	// get ratings //

     $t->assign( 'profileid', $_REQUEST['id'] );

	if (isset($_GET['ratingid'])) {
		$t->assign( 'ratingid', $_GET['ratingid'] );
	}
	$data = $osDB->getAll( 'SELECT id, rating, displayorder, enabled, description from ! where enabled = ? order by displayorder asc ', array(RATINGS_TABLE, 'Y') );

	$newdata = array();

	$total_ratingscnt = 0;

	foreach ($data as $item) {

		// comment count //

		$futuredate1 = date("Y/m/d", mktime(0,0,0,date("m"),(date("d") - $config['mod_rating_rem_com']),date("Y")));

		$comments = $osDB->getAll('SELECT distinct rat.id, rat.comment, rat.reply, rat.userid, usr.username FROM ! as rat, ! as usr WHERE rat.profileid = ? and rat.ratingid = ? and rat.comment <> ? and rat.comment_date >= ? and usr.id = rat.userid', array( USER_RATING_TABLE, USER_TABLE, $_REQUEST['id'], $item['id'], '', $futuredate1 ) );

		$item["commentcount"] = count($comments);

		$item['comments'] = $comments;

		unset($comments);

		// rating count //

		$futuredate2 = date("Y/m/d", mktime(0,0,0,date("m"),(date("d") - $config['mod_rating_rem_rat']),date("Y")));

          $ratingcount = $osDB->getOne('SELECT count(id) as ratingcount FROM ! WHERE profileid = ? and ratingid = ? and rating > ? and rating_date >= ?', array( USER_RATING_TABLE, $_REQUEST['id'], $item["id"], '0', $futuredate2 ) );

		$item["ratingcount"] = $ratingcount;

		$total_ratingscnt += $ratingcount;


		// rating value //

          $rowrate = $osDB->getRow('SELECT count(rating) as tv , sum(rating) as sm FROM ! WHERE profileid = ? and ratingid = ? and rating > ? and rating_date >= ?', array( USER_RATING_TABLE, $_REQUEST['id'], $item["id"], '0', $futuredate2 ) );

		$tv = $rowrate['tv'];

		$sm = $rowrate['sm'];

		if ( $tv == 0 ) {

			$ratingvalue = 0;

		} else {

			$tv = ($tv == 0) ? 1 : $tv;

			$ratingvalue = round( $sm / $tv );

		}

		$item["ratingvalue"] = $ratingvalue;

		// check user has already rated //

		$has_rated = 1;

			$rowcrate = $osDB->getOne(  'SELECT count(*) as c  FROM !  WHERE  profileid = ? and ratingid = ? and rating > ?', array( USER_RATING_TABLE, $_REQUEST['id'], $item['id'], '0' ));

			if ( $rowcrate == 0 ) {
				$has_rated = 0;
			}else {
				$has_rated = 1;
			}


		$item["has_rated"] = $has_rated;

		// check if user has already commented //

		$has_commented = 1;

			$rowcrate = $osDB->getOne(  'SELECT count(*) as c  FROM !  WHERE  profileid = ? and ratingid = ? and comment is not null', array( USER_RATING_TABLE, $_GET['id'], $item["id"] ));

			if ( $rowcrate == 0 ) {
				$has_commented = 0;
			}else {
				$has_commented = 1;
			}


		$item["has_commented"] = $has_commented;

		array_push($newdata, $item);

	}

	$t->assign('total_ratingscnt', $total_ratingscnt);

	$t->assign( 'ratings', $newdata );

	// get options //

	$optionlist = array();
	$optionlist_note = array();

	$div = $config['mod_rating_inc'] - 1;

	for($i=$config['mod_rating_min']; $i<=$config['mod_rating_max']; $i++) {

		$div++;

		if ($i == $config['mod_rating_min']) {

			$thename = get_lang('worst1');

		} else if ($i == $config['mod_rating_max']) {

			$thename = get_lang('best1');

		} else {

			$thename = "";

		}

		if ($div == $config['mod_rating_inc']) {

		$temparray = array();

		$temparray["name"] = $i . $thename;
		$temparray["value"] = $i;

		array_push($optionlist, $temparray);

		$div = 0;

		}

	}

	if ($config['mod_rating_inc_order'] == "High to Low") {

		$optionlist = array_reverse($optionlist);

	}

	$t->assign( 'ratingoptions', $optionlist );

	// get comments //

	if (isset($_GET['ratingid']) ) {
	     $t->assign( 'comments', $osDB->getAll('SELECT distinct rat.id, rat.comment, rat.reply, rat.userid, usr.username FROM ! as rat, ! as usr WHERE rat.profileid = ? and rat.ratingid = ? and rat.comment <> ? and rat.comment_date >= ? and usr.id = rat.userid', array( USER_RATING_TABLE, USER_TABLE, $_REQUEST['id'], $_GET['ratingid'], '', $futuredate1 ) ) );
	}
	/* MOD END */

	if( $found ){

		$t->assign ( 'found', 1);

		$t->assign( 'pref', $pref);

	}

     $t->assign('profile_views', $osDB->getOne('select count(*) from ! where userid = ? and act = ?', array( VIEWS_WINKS_TABLE, $_REQUEST['id'], 'V' ) ) );

	/* Now add this view to profile_views table, if no user logged, make it -1  */

	if (isset($_GET['errid'])) {
		$t->assign('errid', $_GET['errid']);

		$t->assign("error_message",get_lang('errormsgs', $_GET['errid']) );
	}

	$t->assign('lang',$lang);


	$t->assign('lang',$lang);

	$config['use_profilepopups'] = "Y";
	$t->assign('config',$config);

	$t->display( 'admin/nickpage.tpl' );
}
?>
