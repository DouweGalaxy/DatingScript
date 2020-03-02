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

include('sessioninc.php');

define( 'PAGE_ID', 'profile_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}


$userid = (int)$_REQUEST['edit'];

$sectionid = $_REQUEST['sectionid'];

$currdisplayorder = $osDB->getOne('select displayorder from ! where id=?', array(SECTIONS_TABLE, $sectionid) );

$nextsectionid = $osDB->getOne('select id from ! where displayorder > ? and enabled = ? order by displayorder asc',array(SECTIONS_TABLE, $currdisplayorder, 'Y') );

if (!isset($nextsectionid)) $nextsectionid = 0;

if ($_POST['reqsectionid']!= '') {
	$nextsectionid = $_POST['reqsectionid'];
}


foreach ($_POST as $questionid => $options) {

	$j = 0;

	if ($questionid == 'selected_questions') {
		foreach($options as $kx => $val) {

			$osDB->query ( 'DELETE FROM ! WHERE userid = ? AND questionid = ?', array( USER_PREFERENCE_TABLE, $userid, $val ) );

		}
	}

	if ( $questionid == 'sectionid' || $questionid == 'selected_questions' || $questionid == 'reqsectionid' || $questionid == 'edit') {


	} elseif ( !is_array( $options ) ) {

		$userpref[ $j ] = $userid;

		$j++;

		if ( substr( $questionid, -1 ) == 'Y' ) {

			if ( $options == NULL ) {

				header ( 'location: editprofilequestions.php?sectionid=' . $sectionid . '&edit='.$userid.'&errid='.MANDATORY_FIELDS );
				exit;

			}

		}

		$questionid = substr( $questionid, 0, strlen( $questionid) -1  );

		$userpref[ $j ] = $questionid;

		$j++;

		$userpref[ $j ] = $options;

		$osDB->query ( 'INSERT INTO ! ( userid, questionid, answer ) VALUES ( ?, ?, ? )', array( USER_PREFERENCE_TABLE, $userpref[0], $userpref[1], $userpref[2]) );

	} else {

		$executeflag = 0;

		foreach( $options as $option ) {

			$j = 0;

			$userpref[ $j ] = $userid;

			$j++;

			if ( substr( $questionid, -1 ) == 'Y' ) {

				if ( $options == NULL ) {

					header ( 'location: editprofilequestions.php?sectionid=' . $sectionid . '&edit='.$userid.'&errid='.MANDATORY_FIELDS );
					exit;

				}

			}

			$qid = substr( $questionid, 0, strlen( $questionid) -1 );

			$userpref[ $j ] = $qid;

			$j++;

			$userpref[ $j ] = $option;

			if ( !$executeflag ) {

				$executeflag = 1;

			}

			$osDB->query ( 'INSERT INTO ! ( userid, questionid, answer ) VALUES (? , ?, ? )', array( USER_PREFERENCE_TABLE, $userpref[0],  $userpref[1], $userpref[2] ) );
		} //foreach

	} //else

} //foreach

unset($options);

if( $nextsectionid > 0 ) {

	header ( 'location: editprofilequestions.php?sectionid='.$nextsectionid.'&edit='.$userid);

} else {

	header ( 'location: profile.php?edit='.$userid);

}

?>