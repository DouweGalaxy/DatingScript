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

$row['firstname'] = stripslashes($row['firstname']);
$row['lastname'] = stripslashes($row['lastname']);
$row['state_province'] = stripslashes($row['state_province']);
$row['county'] = stripslashes($row['county']);
$row['city'] = stripslashes($row['city']);
$row['zip'] = stripslashes($row['zip']);
$row['address_line1'] = stripslashes($row['address_line1']);
$row['address_line2'] = stripslashes($row['address_line2']);
$row['lookstate_province'] = stripslashes($row['lookstate_province']);
$row['lookcounty'] = stripslashes($row['lookcounty']);
$row['lookcity'] = stripslashes($row['lookcity']);
$row['lookzip'] = stripslashes($row['lookzip']);

$_SESSION['lookradius'] = $row['lookradius'];

$states_cnt = $counties_cnt = $cities_cnt = $zipcodes_cnt = $lookstates_cnt = $lookcounties_cnt = $lookcities_cnt = $lookzipcodes_cnt = 0;

if ($config['accept_country'] == 'Y' || $config['accept_country'] == '1') {

	$row['country'] = $countrycode = isset($row['country']) ? $row['country'] : 'AA';

} else{
	$row['country'] = $countrycode = 'AA';
}

if ( ($config['accept_state'] == 'Y' || $config['accept_state'] == '1') && $row['country'] != 'AA') {

	$lang['states'] = getStates($countrycode,'N');

	$states_cnt = count($lang['states']);

	if (isset($states_cnt) && $states_cnt > 0) {
		if ($states_cnt  ==  1 &&  !isset($row['state_province']) ) {
			foreach ($lang['states'] as $key => $val) {
				$row['state_province'] = $key;
			}
		} elseif (!isset($row['state_province']) ) {
			$row['state_province'] =  'AA';
		}
	} elseif (!isset($row['state_province'])) {
		$row['state_province'] = 'AA';		
	}
} elseif (!isset($row['state_province']) || $row['state_province'] == '' ) {
	$row['state_province'] = 'AA';		
}

if (($config['accept_county'] == '1' || $config['accept_county'] == 'Y') && ($states_cnt <= 1 || ($states_cnt > 1 && $row['state_province'] != 'AA') ) )   { 

	$lang['counties'] = getCounties($countrycode,$row['state_province'],'N');

	$counties_cnt = count($lang['counties']);
	
	if (isset($counties_cnt) && $counties_cnt > 0  ) {

		if ($counties_cnt  ==  1 &&  !isset($row['county']) ) {
			foreach ($lang['counties'] as $key => $val) {
				$row['county'] = $key;
			}
		} elseif (!isset($row['county'])) {
			$row['county'] = 'AA';		
		}
	} elseif (!isset($row['county']) ) {
		$row['county'] = 'AA';		
	}
} elseif (!isset($row['county']) || $row['county'] == '' ) {
	$row['county'] = 'AA';
}

if ( ($config['accept_city'] == '1' || $config['accept_city'] == 'Y') && ($states_cnt == 1 || ($states_cnt > 1 && $row['state_province'] != 'AA') ) && ($counties_cnt <= 1 || ($counties_cnt > 1 && $row['county'] != 'AA') ) )  { 

	$lang['cities'] = getCities($countrycode, $row['state_province'], $row['county'], 'N');

	$cities_cnt = count($lang['cities']);
	
	if (isset($cities_cnt) && $cities_cnt > 0  ) {

		if ($cities_cnt  ==  1 &&  !isset($row['city']) ) {
			foreach ($lang['cities'] as $key => $val) {
				$row['city'] = $key;
			}
		} elseif (!isset($row['city'])) {
			$row['city'] = 'AA';		
		}
	} elseif (!isset($row['city']) ) {
		$row['city'] = 'AA';		
	}
} elseif (!isset($row['city']) || $row['city'] == '') {
	$row['city'] = 'AA';
}

if ( ($config['accept_zipcode'] == '1' || $config['accept_zipcode'] == 'Y') && ($states_cnt == 1 || ($states_cnt > 1 && $row['state_province'] != 'AA') ) && ($counties_cnt <= 1 || ($counties_cnt > 1 && $row['county'] != 'AA') ) && ($cities_cnt <= 1 || ($cities_cnt > 1 && $row['city'] != 'AA' ) ) ) { 

	$lang['zipcodes'] = getZipcodes($countrycode,$row['state_province'], $row['county'], $row['city'], 'N');

	$zipcodes_cnt = count($lang['zipcodes']);
	
	if (isset($zipcodes_cnt) && $zipcodes_cnt > 0  ) {

		if ($zipcodes_cnt  ==  1 &&  !isset($row['zip']) ) {
			foreach ($lang['zipcodes'] as $key => $val) {
				$row['zip'] = $key;
			}
		} elseif (!isset($row['zip']) ) {
			$row['zip'] = 'AA';		
		}
	} elseif (!isset($row['zip']) ) {
		$row['zip'] = 'AA';		
	}
} elseif (!isset($row['zip']) || $row['zip'] == '') {
	$row['zip'] = 'AA';
}

if (($config['accept_country'] == 'Y' || $config['accept_country'] == '1') && ($config['accept_lookcountry'] == 'Y' || $config['accept_lookcountry'] == '1'))  {

	$row['lookcountry'] = $lookcountrycode = isset($row['lookcountry']) ? $row['lookcountry'] : $row['country'];

} else {
	$row['lookcountry'] = 'AA';
}

if (($config['accept_state'] == 'Y' || $config['accept_state'] == '1') && ($config['accept_lookstate'] == 'Y' || $config['accept_lookstate'] == '1') && $row['lookcountry'] != 'AA' )  {

	$lang['lookstates'] = getStates($lookcountrycode,'Y');

	$lookstates_cnt = count($lang['lookstates']);

	if (isset($lookstates_cnt) && $lookstates_cnt > 0) {
		if ($lookstates_cnt  ==  1 &&  !isset($row['lookstate_province']) ) {
			foreach ($lang['lookstates'] as $key => $val) {
				$row['lookstate_province'] = $key;
			}
		} elseif (!isset($row['lookstate_province']) ) {
			$row['lookstate_province'] =  'AA';
		}
	} elseif (!isset($row['lookstate_province'])) {
		$row['lookstate_province'] = 'AA';		
	}
} elseif (!isset($row['lookstate_province']) || $row['lookstate_province'] == '' ) {
	$row['lookstate_province'] = 'AA';		
}

if (($config['accept_county'] == '1' || $config['accept_county'] == 'Y') && ($config['accept_lookcounty'] == '1' || $config['accept_lookcounty'] == 'Y') && $row['lookcountry'] != 'AA' && ($lookstates_cnt <= 1 || ($lookstates_cnt > 1 && $row['lookstate_province'] != 'AA') ) )   { 

	$lang['lookcounties'] = getCounties($lookcountrycode, $row['lookstate_province'],'Y');

	$lookcounties_cnt = count($lang['lookcounties']);
	
	if (isset($lookcounties_cnt) && $lookcounties_cnt > 0  ) {

		if ($lookcounties_cnt  ==  1 &&  !isset($row['lookcounty']) ) {
			foreach ($lang['lookcounties'] as $key => $val) {
				$row['lookcounty'] = $key;
			}
		} elseif (!isset($row['lookcounty'])) {
			$row['lookcounty'] = 'AA';		
		}
	} elseif (!isset($row['lookcounty']) ) {
		$row['lookcounty'] = 'AA';		
	}
} elseif (!isset($row['lookcounty']) || $row['lookcounty'] == '' ) {
	$row['lookcounty'] = 'AA';
}

if ( ($config['accept_city'] == '1' || $config['accept_city'] == 'Y') && ($config['accept_lookcity'] == '1' || $config['accept_lookcity'] == 'Y') && $row['lookcountry'] != 'AA' && ($lookstates_cnt == 1 || ($lookstates_cnt > 1 && $row['lookstate_province'] != 'AA') ) && ($lookcounties_cnt <= 1 || ($lookcounties_cnt > 1 && $row['lookcounty'] != 'AA') ) )  { 

	$lang['lookcities'] = getCities($lookcountrycode,$row['lookstate_province'], $row['lookcounty'], 'Y');

	$lookcities_cnt = count($lang['lookcities']);
	
	if (isset($lookcities_cnt) && $lookcities_cnt > 0  ) {

		if ($lookcities_cnt  ==  1 &&  !isset($row['lookcity']) ) {
			foreach ($lang['lookcities'] as $key => $val) {
				$row['lookcity'] = $key;
			}
		} elseif (!isset($row['lookcity'])) {
			$row['lookcity'] = 'AA';		
		}
	} elseif (!isset($row['lookcity']) ) {
		$row['lookcity'] = 'AA';		
	}
} elseif (!isset($row['lookcity']) || $row['lookcity'] == '') {
	$row['lookcity'] = 'AA';
}

if ( ($config['accept_zipcode'] == '1' || $config['accept_zipcode'] == 'Y') && ($config['accept_lookzipcode'] == '1' || $config['accept_lookzipcode'] == 'Y') && $row['lookcountry'] != 'AA'  && ($lookstates_cnt == 1 || ($lookstates_cnt > 1 && $row['lookstate_province'] != 'AA') ) && ($lookcounties_cnt <= 1 || ($lookcounties_cnt > 1 && $row['lookcounty'] != 'AA') ) && ($lookcities_cnt <= 1 || ($lookcities_cnt > 1 && $row['lookcity'] != 'AA' ) ) ) { 

	$lang['lookzipcodes'] = getZipcodes($lookcountrycode,$row['lookstate_province'], $row['lookcounty'], $row['lookcity'], 'N');

	$lookzipcodes_cnt = count($lang['lookzipcodes']);
	
	if (isset($lookzipcodes_cnt) && $lookzipcodes_cnt > 0  ) {

		if ($lookzipcodes_cnt  ==  1 &&  !isset($row['lookzip']) ) {
			foreach ($lang['lookzipcodes'] as $key => $val) {
				$row['lookzip'] = $key;
			}
		} elseif (!isset($row['lookzip']) ) {
			$row['lookzip'] = 'AA';		
		}
	} elseif (!isset($row['lookzip']) ) {
		$row['lookzip'] = 'AA';		
	}
} elseif (!isset($row['lookzip']) || $row['lookzip'] == '' ) {
	$row['lookzip'] = 'AA';
}

$lang['signup_gender_values'] = get_lang_values('signup_gender_values');

$lang['signup_gender_look'] = get_lang_values('signup_gender_look');

$lang['tz'] = get_lang_values('tz');
if (isset($lookcountrycode) && $lookcountrycode != '') {
	$zipsavailable = $osDB->getOne('select count(*) from ! where countrycode=?', array(ZIPCODES_TABLE, $lookcountrycode) );
	$t->assign('zipsavailable', $zipsavailable);
}
if (!isset($row['radiustype']) or $row['radiustype'] == '') {
	if ($lookcountrycode == 'US') {
		$row['radiustype'] = 'miles';
	} else {
		$row['radiustype'] = 'kms';
	}
}
$_SESSION['radiustype'] = $row['radiustype'];
$t->assign('radiustype', $row['radiustype']);

if ( $row['birth_date'] == -1 && $row['birthyear'] < 1970 ) {

	if ( strlen( $row['birthday'] ) == 1 ) {
		$row['birthday'] = '0' . $row['birthday'];
	}

	$row['birth_date'] = $row['birthyear'] . '-' . $row['birthmonth'] . '-' . $row['birthday'];
}

$t->assign( 'user', $row );

$t->assign('lang', $lang);

$t->assign('rendered_page', $t->fetch('edituser.tpl') );

$t->display( 'index.tpl' );

unset($row, $lang);

exit;

?>