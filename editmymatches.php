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

if (isset($_SESSION['modifiedrow']) && $_SESSION['modifiedrow'] != '') {

	$row = $_SESSION['modifiedrow'] ;

	$row['id'] = $_SESSION['UserId'];

	$_SESSION['modifiedrow'] = '';

} else {
	$row = $osDB->getRow( 'SELECT * FROM ! where id = ?', array( USER_TABLE, $_SESSION['UserId'] ) );
}

if ( isset( $_GET['errid'] ) ) {
	$t->assign ( 'modify_error', get_lang('errormsgs',$_GET['errid']) );
}
if (isset($row['country']) && $row['country'] == '-1') $row['country'] = 'AA';
if (isset($row['state_province']) && $row['state_province'] == '-1') $row['state_province'] = 'AA';
if (isset($row['county']) && $row['county'] == '-1') $row['county'] = 'AA';
if (isset($row['city']) && $row['city'] == '-1') $row['city'] = 'AA';
if (isset($row['zip']) && $row['zip'] == '-1') $row['zip'] = 'AA';
if (isset($row['lookcountry']) && $row['lookcountry'] == '-1') $row['lookcountry'] = 'AA';
if (isset($row['lookstate_province']) && $row['lookstate_province'] == '-1') $row['lookstate_province'] = 'AA';
if (isset($row['lookcounty']) && $row['lookcounty'] == '-1') $row['lookcounty'] = 'AA';
if (isset($row['lookcity']) && $row['lookcity'] == '-1') $row['lookcity'] = 'AA';
if (isset($row['lookzip']) && $row['lookzip'] == '-1') $row['lookzip'] = 'AA';


$row['lookstate_province'] = stripslashes($row['lookstate_province']);
$row['lookcounty'] = stripslashes($row['lookcounty']);
$row['lookcity'] = stripslashes($row['lookcity']);
$row['lookzip'] = stripslashes($row['lookzip']);

$_SESSION['lookradius'] = $row['lookradius'];

$_SESSION['lookfrom'] = $lookcountrycode = $row['lookcountry'];

$lang['lookstates'] = getStates($lookcountrycode);

$lang['signup_gender_look'] = get_lang_values('signup_gender_look');

$zipsavailable = $osDB->getOne('select count(*) from ! where countrycode=?', array(ZIPCODES_TABLE, $lookcountrycode) );
if (!isset($row['radiustype']) or $row['radiustype'] == '') {
	if ($lookcountrycode == 'US') {
		$row['radiustype'] = 'miles';
	} else {
		$row['radiustype'] = 'kms';
	}
}

$_SESSION['radiustype'] = $row['radiustype'];

$t->assign('zipsavailable', $zipsavailable);
$t->assign('radiustype', $row['radiustype']);

if (($row['lookstate_province'] != '' && $row['lookstate_province'] != 'AA') && (($config['accept_county'] == 'Y' || $config['accept_county'] == '1') && ($config['accept_lookcounty'] == 'Y' || $config['accept_lookcounty'] == '1') ) ) {

	$lang['lookcounties'] = getCounties($lookcountrycode, (($row['lookstate_province']!='')?$row['lookstate_province']:'AA'), 'Y');

	if (count($lang['lookcounties']) == 1) {
		foreach ($lang['lookcounties'] as $key => $val) {
			$row['lookcounty'] = $key;
		}
	}
}

if (($row['lookcounty'] != '' && $row['lookcounty'] != 'AA') ||(($row['lookcountry'] != 'AA' &&  $row['lookstate_province'] != 'AA') && (($config['accept_city'] == 'Y' || $config['accept_city'] == '1') && ($config['accept_lookcity'] == 'Y' || $config['accept_lookcity'] == '1') ) ) ) {

	$lang['lookcities'] = getCities($lookcountrycode, (($row['lookstate_province']!='')?$row['lookstate_province']:'AA'), (($row['lookcounty']!='')?$row['lookcounty']:'AA'), 'Y');

	if (count($lang['lookcities']) == 1) {
		foreach($lang['lookcities'] as $key => $val) {
			$row['lookcity'] = $key;
		}
	}
}

if (($row['lookcity'] != '' && $row['lookcity'] != 'AA') ||( ( $row['lookcountry'] != 'AA' &&  $row['lookstate_province'] != 'AA') && (($config['accept_zipcode'] == 'Y' || $config['accept_zipcode'] == '1') && ($config['accept_lookzipcode'] == 'Y' || $config['accept_lookzipcode'] == '1') ) ) ) {
	$lang['lookzipcodes'] = getZipcodes($lookcountrycode, (($row['lookstate_province']!='')?$row['lookstate_province']:'AA'), (($row['lookcounty']!='')?$row['lookcounty']:'AA'), (($row['lookcity']!='')?$row['lookcity']:'AA'), 'Y');
}

$t->assign( 'user', $row );

$t->assign('lang', $lang);

$t->assign('rendered_page', $t->fetch('editmymatches.tpl') );

$t->display( 'index.tpl' );
unset($row);

exit;

?>