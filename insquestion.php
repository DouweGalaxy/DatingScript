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

$id 			= 	trim( $_POST['txtid'] );

$question 		= 	trim( $_POST['txtquestion'] );

$description 	= 	trim( $_POST['txtdescription'] );

$guideline 		= 	trim( $_POST['txtguideline'] );

$controltype 	= 	$_POST['txtcontroltype'];

$maxlength 		= 	trim( $_POST['txtmaxlength'] );

$mandatory 		= 	$_POST['txtmandatory'];

$section 		= 	$_POST['txtsection'];

$extsearchable 	= 	$_POST['txtextsearch'];

$extsearchhead 	= 	$_POST['txextsearchhead'];

$enabled 		= 	$_POST['txtenabled'];

$gender = $_POST['txtgender'];


//get maximum order of sections
$maxorder = $osDB->getOne( 'SELECT MAX(displayorder) as maxorder FROM ! WHERE section = ?', array( QUESTIONS_TABLE, $_POST['txtsection'] ) );

$osDB->query( "INSERT INTO ! ( question, description, guideline, control_type, maxlength, mandatory, section, displayorder, extsearchable, extsearchhead, enabled, gender ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)", array( QUESTIONS_TABLE, $question, $description, $guideline, $controltype, $maxlength, $mandatory, $section, ($maxorder+1), $extsearchable, $extsearchhead, $enabled, $gender ) );

header ( 'location: section.php');
?>