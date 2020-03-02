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

define( 'PAGE_ID', 'section_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}


if( isset($_POST['frm']) && $_POST['frm'] == 'frmGroupDelete' ){

	$section = $_POST['sectionid'];

	foreach( $_POST['txtid'] as $arr ) {

		$id = $arr;

		$result = $osDB->query( 'DELETE FROM ! WHERE id = ? and section = ?', array( QUESTIONS_TABLE, $id, $section ) );
	}

	$questions = $osDB->getAll( 'SELECT id FROM ! WHERE section = ? order by displayorder', array( QUESTIONS_TABLE, $section ) );

	$i=1;

	foreach ($questions as $qid ){

		$osDB->query( 'UPDATE ! SET displayorder = ? WHERE id = ?
			AND section = ?', array( QUESTIONS_TABLE, $i , $qid['id'], $section ));

		$i++;
	}
	unset($questions);
	header ( "location: sectionquestions.php?sectionid=" . $section );
	exit;
}

$arr = $_POST[ 'txtcheck' ];

if ( count( $arr ) == 0 ) {

	header( 'location: sectionquestions.php?sectionid=' . $_POST['sectionid'] . '&msg=' . urlencode(get_lang('no_select_msg')) );
	exit;

}
if ( isset($_POST['groupaction']) && $_POST['groupaction'] == get_lang('enable_selected') ) {

	foreach ( $arr as $id ) {

		$result = $osDB->query( 'UPDATE ! SET enabled = ? WHERE id = ?', array( QUESTIONS_TABLE, 'Y', $id ) );

	}

	unset($arr);
	header ( "location: sectionquestions.php?sectionid=" . $_POST['sectionid'] );
	exit;
} elseif (isset($_POST['groupaction']) && $_POST['groupaction'] == get_lang('disable_selected') ) {

	foreach ( $arr as $id ) {

		$result = $osDB->query( 'UPDATE ! SET enabled = ? WHERE id = ?', array( QUESTIONS_TABLE, 'N', $id ) );

	}

	unset($arr);
	header ( "location: sectionquestions.php?sectionid=" . $_POST['sectionid'] );
	exit;

} elseif (isset($_POST['groupaction']) && $_POST['groupaction'] == get_lang('delete_selected') ) {

	$sql = 'SELECT * from ! Where 0 ';

	foreach ( $arr as $questionid ) {
		$sql .= " or id=" . $questionid;
	}
	$rsQuestions = $osDB->getAll( $sql, array( QUESTIONS_TABLE ) );
	$data = array();
	foreach ( $rsQuestions as $row ) {
		$row['question'] = stripslashes($row['question']);
		$row['description'] = stripslashes($row['description']);
		$row['guideline'] = stripslashes($row['guideline']);
		$row['extsearchhead'] = stripslashes($row['extsearchhead']);
		$data[] = $row;
	}

	$t->assign( 'data', $data );

	unset($data, $rsQuestions, $arr);

	$t->assign( 'lang', $lang );

	$t->assign('sectionid', $_POST['sectionid']);

	$t->assign('rendered_page', $t->fetch('admin/groupquestiondel.tpl'));

	$t->display( 'admin/index.tpl' );
	exit;
}
?>