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

include( 'sessioninc.php' );

$userid = $_SESSION['UserId'];

$sectionid = isset($_GET[ 'sectionid' ])?$_GET[ 'sectionid' ]:'';

if ( $sectionid == '' ) {
	foreach ($sections as $k => $v) {
		$sectionid = $k;
		break;
	}
} 

// Query to reterive records from osdate_questions table
// sorted descending on mandatory: that is mandatory fields should be displayed first

$temp = $osDB->getAll( 'select id, question, mandatory, description, guideline, maxlength, control_type from ! where enabled = ? and section = ? and question <> ? and gender in (?,?) order by mandatory desc, displayorder', array( QUESTIONS_TABLE, 'Y', $sectionid , '',$_SESSION['gender'],'A') );


$data = array();

foreach( $temp as $index => $row ) {

	if ($_SESSION['opt_lang'] != 'english') {
	/* THis is made to adjust for multi-language */
		$lang_question = $_SESSION['profile_questions'][$row['id']]['question'];
		$lang_descr = 	$_SESSION['profile_questions'][$row['id']]['description'];
		$lang_guide = 	$_SESSION['profile_questions'][$row['id']]['guideline'];
		if ($lang_question != '') {
			$row['question'] = $lang_question;
		}
		if ($lang_descr != '') {
			$row['description'] = $lang_descr;
		}
		if ($lang_guide != '') {
			$row['guideline'] = $lang_guide;
		}
	}

	// reterive record from osdate_questionoptions table

	$options = $osDB->getAll( 'select * from ! where enabled = ? and questionid = ? order by displayorder', array( OPTIONS_TABLE, 'Y', $row['id'] ) ) ;

	$optsrs = array(); if ($_SESSION['opt_lang'] != 'english') { /* THis is made to adjust for multi-language */ foreach($options as $kx => $opt) { $lang_ansopt = $_SESSION['profile_questions'][$row['id']][$opt['id']]; if ($lang_ansopt != '') {$opt['answer'] = $lang_ansopt; } $optsrs[] = $opt; } } else {$optsrs = $options; }

	unset($options);

	$row['options'] = makeOptions ( $optsrs );

	unset($optsrs);

	$userprefrs = $osDB->getAll( 'select questionid, answer from ! where userid = ? and questionid = ?', array( USER_PREFERENCE_TABLE, $userid, $row['id'] ) ) ;

	$row['userpref'] = makeAnswers ( $userprefrs );

	unset($userprefrs);

	$data [] = $row;
}

if ( isset( $_GET['errid'] ) ) {

	$t->assign( 'mandatory_question_error', get_lang('errormsgs',$_GET['errid']) );

}

$t->assign( 'sectionid', $sectionid );

$t->assign('frmname', 'frm' . $sectionid );

$t->assign( 'head', get_lang('myprofilepreferences')." - ".$sections[ $sectionid ] );

$t->assign( 'data', $data );

$t->assign('lang', $lang);

$t->assign('rendered_page', $t->fetch('editquestions.tpl') );

$t->display('index.tpl');
unset($data, $temp, $sections);

?>