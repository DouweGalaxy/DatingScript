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

if (!isset($_SERVER['HTTP_REFERER']) || strstr( $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false)
{
die("Hacker attempt. Aborted");
}

if ( !defined( 'SMARTY_DIR' ) ) {
	include_once( 'init.php' );
}

$cmd = isset($_POST['cmd'])?$_POST['cmd']:'';

if ( $cmd == 'posted' ){

	$txttitle = strip_tags(trim($_POST['txttitle']));
	$txtname = strip_tags(trim($_POST['txtname']));
	$txtemail = strip_tags(trim($_POST['txtemail']));
	$txtcountry = strip_tags(trim($_POST['txtcountry']));
	$txtcomments = strip_tags(trim($_POST['txtcomments']));

	$t->assign('txttitle', $txttitle);
	$t->assign('txtname', $txtname);
	$t->assign('txtemail', $txtemail);
	$t->assign('txtcountry', $txtcountry);
	$t->assign('txtcomments', $txtcomments);

	if ( (strtolower($_SESSION['spam_code']) != strtolower($_POST['spam_code']) || !isset($_SESSION['spam_code']) ) && $config['spam_code_length'] > 0)  {
		$t->assign('msg', get_lang('errormsgs','121') );
	} else {

		$From    = $config['admin_email'];
		$To      = $config['feedback_email'];
		$Subject = get_lang('email_feedback_subject');

		$message = get_lang('feedback_email_to_admin', MAIL_FORMAT);
		$message = str_replace('#txttitle#',$txttitle,$message);
		$message = str_replace('#txtname#', $txtname,$message);
		$message = str_replace('#txtemail#',$txtemail,$message);
		$message = str_replace('#txtcountry#', $lang['countries'][$txtcountry],$message);
		$message = str_replace('#txtcomments#', nl2br($txtcomments), $message);
		$success = mailSender($From, $To, $To, $Subject, $message);
		unset($message, $Subject);
		$t->assign( 'success', $success );
	}
} elseif (isset($_SESSION['UserId']) && $_SESSION['UserId'] > 0) {
	$t->assign('txtname', $_SESSION['FullName']);
	$t->assign('txtemail', $_SESSION['email']);
}

$t->assign('lang',$lang);

$t->assign('rendered_page', $t->fetch('feedback.tpl') );

$t->display( 'index.tpl' );
exit;
?>