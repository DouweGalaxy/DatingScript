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

define( 'PAGE_ID', 'profile_mgt' );

if ( !checkAdminPermission( PAGE_ID ) ) {

	header( 'location: not_authorize.php' );
	exit;
}

$userid = $_POST[ 'txtuserid' ];

$modified['username'] = $username = trim($_POST[ 'txtusername' ]);

$modified['password'] = $password = trim($_POST[ 'txtpassword' ]);

$modified['confpassword'] = $confpassword = trim($_POST[ 'txtpassword2' ]);

$modified['firstname'] = $firstname = trim($_POST[ 'txtfirstname' ]);

$modified['lastname'] = $lastname = trim($_POST[ 'txtlastname' ]);

$modified['about_me'] = $about_me = addslashes(strip_tags(trim($_POST[ 'about_me' ])));

$modified['couple_usernames'] = $couple_usernames = strip_tags(trim($_POST[ 'couple_usernames' ]));

$modified['email'] = $email = trim($_POST[ 'txtemail' ]);

$modified['gender'] = $gender = $_POST[ 'txtgender' ];

$modified['birthmonth'] = $birthmonth = $_POST[ 'txtbirthMonth' ];

$modified['birthday'] = $birthday = $_POST[ 'txtbirthDay' ];

$modified['birthyear'] = $birthyear = $_POST[ 'txtbirthYear' ];

$modified['birth_date'] = strtotime($birthyear.'-'.$birthmonth.'-'.$birthday);

$modified['country'] = $from = $_POST[ 'txtfrom' ];

$modified['zip'] = $zip = trim($_POST[ 'txtzip' ]);

$modified['timezone'] = $timezone = $_POST['txttimezone'];

$modified['city'] = $city = trim($_POST[ 'txtcity' ]);

$modified['county'] = $county = isset($_POST[ 'txtcounty' ])?trim($_POST[ 'txtcounty' ]):'AA';

$modified['state_province'] = $state_province = trim($_POST[ 'txtstateprovince' ]);

$modified['address1'] = $address1 = trim($_POST['txtaddress1' ]);

$modified['address2'] = $address2 = trim($_POST['txtaddress2' ]);

$modified['mlevel'] = $mlevel = $_POST['txtmship'];

$_SESSION['modifiedrow'] = $modified;

//Check for duplicate user
$row_cnt = $osDB->getOne( 'SELECT count(*) as aacount from !  where username = ? and id <>?', array( USER_TABLE, $username, $userid ) );

$rowd_cnt = $osDB->getOne ( "SELECT count(*) as aacount from ! where username = ?", array( ADMIN_TABLE, $username) );

$rowe_cnt = $osDB->getOne ( "SELECT count(*) as aacount from ! where email = ? and id <>?", array( USER_TABLE, $email, $userid) );

$err =0;

if ( $config['accept_firstname'] == 'Y' or $config['accept_firstname'] =='1' ) {
	if ($config['firstname_mandatory'] == 'Y' && $firstname == '' ) {

		$err = FIRSTNAME_REQUIRED;
	}
	if (strlen( $firstname ) > 50 ) {

		$err = FIRSTNAME_LENGTH;
	}
	if ( strpos( $firstname, '@' ) > 0 ) {

		$err = FIRSTNAME_REQUIRED;
	}

} elseif ( $config['accept_lastname'] == 'Y' or $config['accept_lastname'] =='1' ) {
	if ($config['lastname_mandatory'] == 'Y' && $lastname == '' ) {

		$err = LASTNAME_REQUIRED;
	}
	if (strlen( $lastname ) > 50 ) {

		$err = LASTNAME_LENGTH;
	}
	if ( strpos( $lastname, '@' ) > 0 ) {

		$err = LASTNAME_REQUIRED;
	}

} elseif ( $email == '' ) {

	$err = EMAIL_REQUIRED;

} elseif ( strlen( $email ) > 255 ) {

	$err = EMAIL_LENGTH;

} elseif ( preg_replace( "/[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}/i", "", $email ) != "" ) {

	$err = EMAIL_REQUIRED;

} elseif ( ! checkdate( $birthmonth, $birthday, $birthyear ) ) {

	$err = INVALID_BIRTHDATE;

} elseif (($config['accept_about_me'] == 'Y' or $config['accept_about_me'] =='1') &&  $config['about_me_mandatory'] == 'Y' && $about_me == '') {

	$err = ABOUT_ME_MANDATORY;

} elseif ($config['accept_country'] == 'Y' or $config['accept_country'] == '1') {

	if ($config['accept_state'] == 'Y' or $config['accept_state'] == "1") {

		if ( $stateprovince == '' && $config['state_mandatory'] == 'Y' ) {

			$err = STATEPROVINCE_NEEDED;

		} elseif ( $county == ''  && $config['county_mandatory'] == 'Y' && ($config['accept_county'] == 'Y' or $config['accept_county'] == "1")) {

			$err = COUNTY_REQUIRED;

		} elseif ($config['accept_city'] == 'Y' or $config['accept_city'] == "1") {

			if ($city == ''  && $config['city_mandatory'] == 'Y') {

				$err = CITY_REQUIRED;

			} elseif ( strlen( $city ) > 255 ) {

				$err = CITY_LENGTH;
			}

		} elseif ( $zip == ''  && $config['zipcode_mandatory'] == 'Y' && ($config['accept_zipcode'] == 'Y' or $config['accept_zipcode'] == "1")) {

			$err = ZIP_REQUIRED;
		}
	}

} elseif ( $lookageend < $lookagestart && ($config['accept_lookage'] == 'Y' or $config['accept_lookage'] == "1") ) {

	$err = BIGGER_STARTAGE;

} elseif ($timezone == '-25' && $config['timezone_mandatory'] == 'Y' && ($config['accept_timezone'] == 'Y' or $config['accept_timezone'] == "1" ) ) {

	$err = INVALID_TIMEZONE;

} elseif (checkDuplicateEmail($email, $userid) > 0) {
	$err = EMAIL_EXISTS;
}

if ($password != $confpassword) {

	$err = '18';
}


if ($gender == 'C' ) {
	if (trim($couple_usernames) == '' or substr_count($couple_usernames,',') <= 0 or !isset($couple_usernames) ) {
		$err = COUPLE_USERNAMES_MISSING;
	} else {
		$userok = 0;
		$usrs = 0;
		foreach(explode(',',$couple_usernames) as $k => $uname) {
			if (trim($uname) != '') {
				$user = $osDB->getOne('select username from ! where username = ?', array(USER_TABLE, trim($uname)) );
				$usrs++;
				if ($user != trim($uname)) {$userok++;}
			}
		}
		if ($userok > 0 ) {$err = 129; }
		if ($usrs < 2) {$err = COUPLE_USERNAMES_MISSING;}
	}
}


if (  $err != 0 ) {

	header ( "location: profile.php?edit=$userid&errid=$err" );
	exit();

}

$active = $rank = 1;

$birthdate = $birthyear . '-' . $birthmonth . '-' . $birthday;

$act_days = $osDB->getOne('select activedays from ! where roleid = ?', array( MEMBERSHIP_TABLE, $mlevel) );

$curlevel = $osDB->getRow('select level, levelend from ! where id = ?', array( USER_TABLE, $userid ) );

if ($curlevel['level'] != $mlevel) {

	$levelend = ($curlevel['levelend'] != '') ? $curlevel['levelend']: time();
	if ($levelend < time() ) $levelend = time();
	$levelend = strtotime("+$act_days day", $levelend);
} else {
	$levelend = $curlevel['levelend'];
}
// Get orginal username so forum username can be changed also
$org_username = $osDB->getOne("SELECT username FROM ".USER_TABLE." WHERE id = '$userid'");

unset($_SESSION['modifiedrow']);

/* now get the latitude and longitude for zip and lookzip */
if ($from == 'GB') {
   $ukzip = explode(' ',$zip);
   $zipcd = $ukzip[0];
} else {
	$zipcd = $zip;
}

$ziprec = $osDB->getRow("select latitude, longitude from ".ZIPCODES_TABLE." where countrycode = '".$from."'  and code = '".$zipcd."'" );

if (isset($ziprec) && (isset($ziprec['latitude']) && $ziprec['latitude'] != '')  && (isset($ziprec['longitude']) && $ziprec['longitude'] != '' ) ) {
	$osDB->query( 'update ! set username=?, active=?,  email = ?, country=?, firstname=?, lastname=?, gender=?, timezone=?, address_line1=?, address_line2=?, state_province=?, city=?, zip=?, county=?, birth_date=?, levelend=?, about_me=?, couple_usernames=?,  level=?, zip_latitude=?, zip_longitude=? where id = ?', array( USER_TABLE, $username, $active, $email, $from, $firstname, $lastname, $gender, $timezone, $address1, $address2, $state_province, $city, $zip, $county, $birthdate, $levelend, $about_me, $couple_usernames,  $mlevel, $ziprec['latitude'], $ziprec['longitude'], $userid ) );
} else {
	$osDB->query( 'update ! set username=?, active=?,  email = ?, country=?, firstname=?, lastname=?, gender=?, timezone=?, address_line1=?, address_line2=?, state_province=?, city=?, zip=?, county=?, birth_date=?, levelend=?, about_me=?, couple_usernames=?,  level=? where id = ?', array( USER_TABLE, $username, $active, $email, $from, $firstname, $lastname, $gender, $timezone, $address1, $address2, $state_province, $city, $zip, $county, $birthdate, $levelend, $about_me, $couple_usernames,  $mlevel, $userid ) );
}

if ($password != '') {
	$osDB->query('update ! set password=? where id=?', array(USER_TABLE, md5($password), $userid) );
}

$nextsectionid = $osDB->getOne('select id from ! where enabled = ? order by displayorder asc ',array(SECTIONS_TABLE, 'Y') );

header( 'location: editprofilequestions.php?sectionid='.$nextsectionid.'&edit=' . $userid );

?>