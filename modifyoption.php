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

if ((isset($_POST['frm'] ) && $_POST['frm'] !='frmQuestionDetail') || !isset($_POST['frm']) ){

	$id 	= 	trim( $_POST['txtid'] );

	$answer	= 	$_POST['txtanswer'];

	$enable	= 	$_POST['txtenabled'];


	$err = 0;

	if ( $answer == '' ) {

		$err = FIELDS_BLANK;

	}

	if ( $err != 0 ) {

		header ( 'location: sectionquestiondetail.php?sectionid=' . $_POST['txtsectionid'] . '&edit=' . $_POST['txtid'] . '&errid=' . $err .'&questionid='.$_POST['questionid']);
		exit;

	}

 	$osDB->query( 'UPDATE ! SET answer = ?, enabled = ?  WHERE id = ?' , array( OPTIONS_TABLE, $answer, $enable, $id ));

	header ( 'location: sectionquestiondetail.php?sectionid=' . $_POST['txtsectionid'] . '&edit=' . $_POST['txtid'].'&questionid='.$_POST['questionid'] );

	exit;
} else {

	$arr = isset($_POST[ 'txtcheck' ])?$_POST[ 'txtcheck' ]:'';

	if ( isset($_POST['groupaction']) && $_POST['groupaction'] == get_lang('enable_selected') && count($arr) > 0) {

		foreach ( $arr as $id ) {

			$osDB->query( 'UPDATE ! SET enabled = ? WHERE id = ?', array( OPTIONS_TABLE , 'Y', $id ) );

		}

	} elseif (isset($_POST['groupaction']) && $_POST['groupaction'] == get_lang('disable_selected') && count($arr) > 0) {

		foreach ( $arr as $id ) {

			$osDB->query( 'UPDATE ! SET enabled = ? WHERE id = ?', array( OPTIONS_TABLE , 'N', $id ) );

		}

	}

	unset($arr);


	header ( "location: sectionquestiondetail.php?sectionid=" . $_POST['txtsectionid'] . "&questionid=" .(isset( $_POST['txtquestionid'])?$_POST['txtquestionid']:''));
	exit;
}
?>