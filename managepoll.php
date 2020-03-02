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

//Include init.php
if ( !defined( 'SMARTY_DIR' ) ) {
		include_once( '../init.php' );
}

include ( 'sessioninc.php' );

define( 'PAGE_ID', 'poll_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

//Default Sorting
if (isset($_GET['sort'] )) {
	if( $_GET['sort'] == '' ) {

		$sort = ' pollid asc ';

	} else if ( $_GET['sort'] == 'end_date' ) {

		$sort = 'date '. checkSortType ( $_GET['type'] );

	} else if ( $_GET['sort'] == get_lang('active') ) {

		$sort = 'active '. checkSortType ( $_GET['type'].' , pollid asc' );
	} else {

		$sort = findSortBy();
	}
} else {
	$sort = ' pollid asc ';
}

//For Editing Polls
if ( isset($_GET['edit']) ) {

	$data = $osDB->getRow( 'SELECT * from ! Where pollid = ?', array( POLLS_TABLE, $_GET['edit'] ) );

	$data['question'] = stripslashes($data['question']);

	$t->assign( 'lang', $lang );

	if (isset($_GET['errid']) &&  $_GET['errid'] != '') {
		$t->assign( 'error', get_lang('poll_error', $_GET['errid'] ) );
	}

	$t->assign( 'data', $data );

	unset($data);

	$t->assign('rendered_page', $t->fetch('admin/polledit.tpl'));

	$t->display( 'admin/index.tpl' );
	exit;
}

//For Deletion of Polls
if ( isset($_POST['frm']) && $_POST['frm'] == 'frmDelPoll' && isset($_POST['delaction']) && $_POST['delaction'] == 'Yes') {

	$osDB->query( 'DELETE FROM ! WHERE pollid = ?', array( POLLOPTS_TABLE, $_POST['txtid'] ) );

	$osDB->query( 'DELETE FROM ! WHERE pollid = ?', array( POLLS_TABLE, $_POST['txtid'] ) );

	header('location: managepoll.php');

	exit;
}

//Insert Poll
if ( isset($_POST['frm']) && $_POST['frm'] == 'frmAddPoll') {

	$poll = $_POST['txtpoll'];

	$poll = eregi_replace('</?[a-z][a-z0-9]*[^<>]*>', '', $poll );

	$osDB->query( 'INSERT INTO ! (question, date , active) VALUES (?, ?, ?)', array( POLLS_TABLE, $poll, time(), '0' ) );

	header('location: managepoll.php');

	exit;
}//End of if

$polls = isset($_POST['txtcheck'])?$_POST['txtcheck']:'';

//Delete Group Poll
if (isset($_POST['groupaction'])) {
	if ( $_POST['groupaction'] == get_lang('delete_selected') ) {

		foreach( $polls as $val ) {

			$osDB->query( 'DELETE FROM ! WHERE pollid = ?', array( POLLOPTS_TABLE, $val ) );

			$osDB->query( 'DELETE FROM ! WHERE pollid = ?', array( POLLS_TABLE, $val ) );

		}

		unset($polls);

		header('location: managepoll.php');

		exit;
	// Enable polls
	} elseif ( $_POST['groupaction'] == get_lang('activate') ) {

		foreach( $polls as $val ) {

			$osDB->query( 'update ! set enabled = ? WHERE pollid = ?', array( POLLS_TABLE, 'Y', $val ) );

			$osDB->query( 'update ! set enabled = ? WHERE pollid = ?', array( POLLOPTS_TABLE, 'Y', $val ) );

		}

		unset($polls);

		header('location: managepoll.php');

		exit;
	// Disable selected polls
	} elseif ( $_POST['groupaction'] == get_lang('deactivate') ) {

		foreach( $polls as $val ) {

			$osDB->query( 'update ! set enabled = ? , active= ? WHERE pollid = ?', array( POLLS_TABLE, 'N','0',  $val ) );

			$osDB->query( 'update ! set enabled = ? WHERE pollid = ?', array( POLLOPTS_TABLE, 'N', $val ) );

		}

		unset($polls);

		header('location: managepoll.php');

		exit;

	// Activate selected polls
	} elseif ( $_POST['groupaction'] == get_lang('activate') ) {

		foreach( $polls as $val ) {

			$osDB->query( 'update ! set active = ?, enabled=? WHERE pollid = ?', array( POLLS_TABLE, '1','Y',  $val ) );

		}

		unset($polls);

		header('location: managepoll.php');

		exit;

	} elseif ( $_POST['groupaction'] == get_lang('deactivate') ) {

		foreach( $polls as $val ) {

			$osDB->query( 'update ! set active = ?, enabled=? WHERE pollid = ?', array( POLLS_TABLE, '0', 'N',  $val ) );

		}

		unset($polls);

		header('location: managepoll.php');

		exit;
	}
}

$id = isset($_REQUEST['pollid'])?$_REQUEST['pollid']:'';


if ( isset($_REQUEST['active']) && $_REQUEST['active'] == 'poll' ) {

	$osDB->query( 'UPDATE ! SET active = ?, enabled=? WHERE pollid = ?', array( POLLS_TABLE, '1', 'Y', $id ) );

	header('location: managepoll.php');

	exit;
} elseif ( isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'poll' ) {

	$osDB->query( 'UPDATE ! SET active = ?, enabled=? WHERE pollid = ?', array( POLLS_TABLE, '0', 'N', $id ) );

	header('location: managepoll.php');

	exit;
}

$sectionrs = $osDB->getAll('SELECT * from ! order by ' . $sort, array( POLLS_TABLE ) );

$data = array();

foreach ( $sectionrs as $row ) {
	$row['question'] = stripslashes($row['question']);

	if ($row['suggested_by'] > 0) {

		$row['suggested_by_username'] = $osDB->getOne('select username from ! where id = ?', array( USER_TABLE, $row['suggested_by'] ) );
	}
	$data[] = $row;
}
$t->assign( 'data', $data );
unset($data, $sectionrs);
$t->assign( 'lang', $lang );

$t->assign( 'sort_type', (isset( $_GET['type'] )?checkSortType( $_GET['type'] ):'asc') );

$t->assign('rendered_page', $t->fetch('admin/managepolls.tpl'));

$t->display( 'admin/index.tpl' );

?>