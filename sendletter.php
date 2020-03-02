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
include (PEAR_DIR.'Mail/mime.php');

$mail_sending_count = isset($config['mail_count'])?$config['mail_count']:10;
$attach_files='';
if ($mail_sending_count == '' || $mail_sending_count == 0) $mail_sending_count = 10;

define( 'PAGE_ID', 'send_letter' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$cmd = isset($_POST['cmd'])?$_POST['cmd']:'';


if(isset($cmd) && $cmd=='imgposted'){

	$delimg = isset($_POST['delimg'])?$_POST['delimg']:'';

	if($delimg !=''){

		unlink( EMAILIMAGES_DIR.$delimg );

	} else{


		$picfile_name = $_FILES['picfile']['name'];

		$tmpfile = $_FILES['picfile']['tmp_name'];

		$real_path = realpath( EMAILIMAGES_DIR );

		if(	$HTTP_ENV_VARS["OS"] == 'Windows_NT'){

			$real_path= str_replace("\\","\\\\",$real_path);

			$file = $real_path."\\\\".$picfile_name;

		} else {

			$file = $real_path."/".$picfile_name;
		}

		copy( $tmpfile, $file );
	}

	header( 'location: ?' );
	exit;
}

if( isset($_POST['frm']) && $_POST['frm'] == 'frmSend' ){

	unset($_SESSION['attach_files']);
	$_SESSION['letterid'] = $letterid = (isset($_POST['txttitle'])?$_POST['txttitle']:'');

	$_SESSION['fromname'] = $fromname = (isset($_POST['txtsendname'])?$_POST['txtsendname']:'');

	$_SESSION['from'] = $from = $osDB->getOne( 'select email from ! where id = ?', array(  ADMIN_EMAILS_TABLE , $_POST['txtfrom'] ) );

	$_SESSION['subject'] = $subject = (isset($_POST['txtsubject'])?$_POST['txtsubject']:'');

	$_SESSION['message'] = $message = (isset($_POST['txtmessage'])?$_POST['txtmessage']:'');

	$_SESSION['save'] = $save = (isset($_POST['txtsave'])?$_POST['txtsave']:'');

	$_SESSION['name'] = $name = (isset($_POST['txtname'])?$_POST['txtname']:'');

	$_SESSION['txtselected'] = $txtselected = (isset($_POST['txtselected'])? $_POST['txtselected'] :'');

	$usernames = array();

	if ($txtselected != '') {

		$txtselected = str_replace('\\n',',',$txtselected);

		$usernames = explode(',', $txtselected);

		$users_select = '';

		foreach ($usernames as $user) {

			if (trim($user) != '') {

				if ($users_select != '') {$users_select .= ','; }

				$users_select .= "'".strtoupper(trim($user))."'";
			}
		}

		unset($usernames);

		$users_select = ' upper(username) in ('.$users_select.')';
	}

	$sql  = "SELECT id, firstname, lastname, gender, email, username FROM !"; // Specific fields to minimize memory use
	$sql .= " WHERE status not in ('Cancel', 'cancel','".get_lang('cancel')."')";

	if( isset($_POST['userrange'])) {
		if ( $_POST['userrange'] == 'selected' && $users_select != ''){
			$sql .= ' AND '.$users_select;
		} elseif ( $_POST['userrange'] == 'level' ){
			$sql .= ' AND level = ' . $_POST['txtlevel'];
		}
	}

	unset($users_select);

	if (isset($_POST['txtfilteruser']) && count($_POST['txtfilteruser']) > 0) {

		foreach( $_POST['txtfilteruser'] as $filter ){

			if( $filter == 'gender' && isset($_POST['txtgender']) ) {

				$gndrsql = " and gender in (";
				$gndrs = '';
				foreach ($_POST['txtgender'] as $k => $gndr ) {

					if ($gndrs != '') $gndrs .= ",";

					if ($gndr == 'A' ) {
						$gndrsql = "";

					} elseif ($gndr == 'B') {
						$gndrs .= "'M','F'";

					} else {
						$gndrs .= "'".$gndr."'";
					}
				}
				if ($gndrsql != '') {
					$gndrsql .= $gndrs.') ';
				}
				$sql .= $gndrsql;

			}

			if( $filter == 'location' && isset($_POST['txtcountry']) ) {

				if ($_POST['txtcountry'] != 'AA' && $_POST['txtcountry'] != '') {

					$sql .= " and country= '" . $_POST['txtcountry'] . "'";

				}
			}

			if( $filter == 'age' && isset($_POST['txtagestart']) && isset($_POST['txtageend']) ) {

				$sql .= ' and floor(period_diff(extract(year_month from NOW()),extract(year_month from birth_date))/12)  between '
				. $_POST['txtagestart'] . ' and ' . $_POST['txtageend'];

			}


		}
	}


	$_SESSION['sendletter_query'] = $sql;

	if( isset($_POST['txtfilteruser']['filter']) && $_POST['txtfilteruser']['filter'] == 'userid' && isset($_POST['txtuserid']) && intval($_POST['txtuserid']) > 0 ) {
		$sql .= ' AND id > ' . intval($_POST['txtuserid']);
	}

	// Check for uploaded file (assuming it will stay where it is for a while
	// If it doesnt we will need to use SESSION or a TMP file (TODO).
	if( is_uploaded_file( $_FILES['files_to_attach']['tmp_name'] ) )
	{
		$_SESSION['attach_files'] = array($_FILES['files_to_attach']['name'] => $_FILES['files_to_attach']['tmp_name']);
	}
}
$msg = get_lang('adminltr', MAIL_FORMAT);

$errcnt = 0;
$MAIL_OVER=0;

if( isset($_POST['txtsave']) && $_POST['txtsave'] == 'yes' ){

	$rid = $osDB->getOne('select id from ! where title = ?', array( ADMIN_LETTER_TABLE, $name) );

	if ($rid > 0) {

		$osDB->query('update ! set subject = ?, modify = ?, bodytext = ? where id = ?', array(ADMIN_LETTER_TABLE, $subject, '133', $message, $rid) );

	} else {

		$osDB->query( 'INSERT INTO ! ( title, subject, modify, bodytext) VALUES( ?, ?, ?, ? )', array( ADMIN_LETTER_TABLE, $name, $subject, '133', $message ) );
	}
}

if (isset($_GET['userid']) && $_GET['userid'] != '') {

	$sql =  $_SESSION['sendletter_query'].' AND id > '.$_GET['userid'];
}

if( (isset($_POST['frm']) && $_POST['frm'] == 'frmSend') || (isset( $_GET['userid']) && $_GET['userid'] != '') ){

	$sql .= " ORDER BY id limit 0," . $mail_sending_count;

	$rs = $osDB->getAll ( $sql, array( USER_TABLE ) );

	unset($sql);

	$sent_cnt = 0;

	$sent_list='';

	if( isset($_SESSION['attach_files']) )
	{
		$attach_files = $_SESSION['attach_files'];
	}

	foreach( $rs as $user){

		$from = '"' . $_SESSION['fromname'] . '" <'. $_SESSION['from'] .'>';

		$subject = $_SESSION['subject'] ;

/*
	#Link#, 		#SiteTitle#, 	#SiteName#, #NickName#,
	#RealName#, 	#Sex#, 			#Email#, 	#UserId#,
	#UserPicture#, 	#UserAge#, 		#UserDOB#, #Domain#
*/

		$siteurl = $link = HTTP_METHOD . $_SERVER['SERVER_NAME'] . DOC_ROOT;
		$message1=str_replace('#Link#', $link, $_SESSION['message']);
		$message1=str_replace('#SiteName#', $config['site_name'], $message1);
		$message1=str_replace('#NickName#', $user['firstname'], $message1);
		$message1=str_replace('#RealName#', $user['firstname'].' '.$user['lastname'], $message1);
		$message1=str_replace('#Sex#', get_lang('signup_gender_values',$user['gender']), $message1);
		$message1=str_replace('#Domain#', $link, $message1);
		$message1=str_replace('#Email#', $user['email'], $message1);
		$message1=str_replace('#UserId#', $user['username'], $message1);
		$message1 = str_replace('#FromName#', $_SESSION['fromname'], $message1);
		$message1 = str_replace('#SiteTitle#', $config['site_title'], $message1);

		$msg1 = str_replace('#LetterContent#',$message1,$msg);
		$msg1 = str_replace('#Subject#',$subject,nl2br($msg1));
		@set_time_limit(1200);
		$sendok = mailSender($from, $user['email'], $user['email'], $subject, $msg1, $attach_files);
		/* Wait for some time before sending another email */
		$sent_cnt++;
		if ($sendok === false) {
			/* issue in sending email */
			$errcnt++;
			$sent_list .=  get_lang('letter_not_sent').'&nbsp;'.get_lang('for').'&nbsp;<b>'.$user['id'].' - '. $user['username']. '</b><br />';
		} else {
			$sent_list .=  get_lang('mail_sent_for_user').'&nbsp;<b>'.$user['id'].' - '. $user['username']. '</b><br />';
		}
		sleep (1);
		$last_userid = $user['id'];
	}
	if ($sent_cnt < $mail_sending_count) {
		$MAIL_OVER=1;
		if ($errcnt > 0) {
			$sent_list .="<br /><b>Issues with mail sending</b><br />";
		} else {
			$sent_list .="<br /><b>Mail sending completed</b><br />";
		}
	}
	$MAIL_STARTED=1;
	unset($msg1, $attach_files, $subject, $from, $user);
}


if( isset($_POST['frm'] ) && $_POST['frm'] == 'frmDelete' && isset($_POST['letterid']) && (int)$_POST['letterid'] ){

	$osDB->query( 'DELETE FROM ! WHERE id = ?', array( ADMIN_LETTER_TABLE, $_POST['letterid'] ) );

	header('location: sendletter.php');

	exit;
}

if( isset($_GET['txttitle']) && $_GET['txttitle'] != ''){

	$row = $osDB->getRow( 'select * from ! where id = ?', array( ADMIN_LETTER_TABLE, $_GET['txttitle']  ) );

	$t->assign ( 'message' , $row );

}

if ((isset($_GET['userid']) && $_GET['userid'] == '') || !isset($_GET['userid']) ) {
	$rs = $osDB->getAll( 'select * from ! ', array( ADMIN_LETTER_TABLE ) );

	$data = array();

	foreach( $rs as $row ) {
		$data[] = $row;
	}

	$t->assign ( 'letters', $data );

	$rs = $osDB->getAll( 'select id,name from ! ', array( MEMBERSHIP_TABLE ) );

	$data = array();

	foreach( $rs as $row ) {
		$data[$row['id']] = $row['name'];
	}

	$t->assign ( 'memberships', $data );

	unset($data);

	$rs = $osDB->getAll( 'select * from ! ORDER BY ' . findSortBy(), array( ADMIN_EMAILS_TABLE ) );

	$emails = array();

	foreach ( $rs as $row ) {
		$emails[$row['id']] = $row['email'];
	}

	$t->assign ( 'adminemails', $emails );

	unset($emails, $rs);

	if ($handle = opendir(EMAILIMAGES_DIR)) {

		while (false !== ($file = readdir($handle))) {

			if ($file != "." && $file != ".." && !is_dir($file) ) {

				$ext = substr( $file, -3, 3 );

				if ( $ext == 'gif' || $ext == 'jpg'|| $ext == 'bmp' || $ext == 'png' || $ext == 'swf' ){

					$imgs[] = $file;

				}

			}
		}

		closedir($handle);

	}
}

if (isset($sent_list)){
	$t->assign('sent_list',$sent_list);
}

$base_url = HTTP_METHOD . $_SERVER['SERVER_NAME'] . DOC_ROOT ;
$t->assign('base_url',$base_url);

if (isset($imgs) ) {

	$t->assign ( 'images', $imgs );

}
if (!isset($sendok)) {
	$t->assign('error_message', get_lang('errormsgs','301') );
}
$t->assign ( 'lang', $lang );

$t->assign('rendered_page', $t->fetch('admin/sendletter.tpl'));


$t->display('admin/index.tpl');



if (isset($MAIL_STARTED) && $MAIL_STARTED == 1 && (!isset($MAIL_OVER) || $MAIL_OVER != '1' ) ) {
	echo('<meta http-equiv=refresh content=2;url='.DOC_ROOT.ADMIN_DIR.'sendletter.php?userid='.$last_userid.'>');
	flush();
}

?>