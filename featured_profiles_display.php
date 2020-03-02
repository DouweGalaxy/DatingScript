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

/* THis program will populate the featured profile display portion. */
if ( !defined( 'SMARTY_DIR' ) ) {

	include_once(dirname(__FILE__).'/minimum_init.php');
}
if ($config['show_featured_profiles'] > 0 ) {
	$xid = (isset($_SESSION['UserId']) && $_SESSION['UserId'] > 0)?$_SESSION['UserId']:'0';


	$gender=(isset($_POST['gender']) && $_POST['gender']!='' )?$_POST['gender']:'A';


	if ($xid > 0 ){
		if (!isset($gender) || $gender=='') {
			if (!isset($_SESSION['featured_profiles_gender']) ) {
				$gender= $osDB->getOne('select lookgender from ! where id=?', array( USER_TABLE, $xid ));
			} else {
				$gender = $_SESSION['featured_profiles_gender'];
			}
		}
		$_SESSION['featured_profiles_gender'] = $gender;
	}

	if ($gender=='' || $gender==' ') $gender='A';
	/* Make a banned users list */
	$bannedlist = '';
	if (isset($_SESSION['UserId']) && $_SESSION['UserId'] > 0) {
		$bannedusers = $osDB->getAll('select bdy.ref_userid from ! as bdy where bdy.act=? and bdy.userid = ? union select bdy1.userid as ref_userid from ! as bdy1 where bdy1.act=? and bdy1.ref_userid = ?', array(BUDDY_BAN_TABLE,  'B', $_SESSION['UserId'], BUDDY_BAN_TABLE,  'B', $_SESSION['UserId'] ) );
		if (count($bannedusers) > 0) {
			$bannedlist=' and fp.userid not in (';
			$bdylst = '';
			foreach ($bannedusers as $busr) {
				if ($bdylst != '') $bdylst .= ',';
				$bdylst .= "'".$busr['ref_userid']."'";
			}
			$bannedlist .=$bdylst.') ';
		}
		unset($bannedusers);
	}

	if ($gender == 'A') {
		$list = $osDB->getAll('select fp.id, fp.userid from ! as fp where ? between fp.start_date and fp.end_date and fp.req_exposures > fp.exposures  and fp.userid <> ? '.$bannedlist.' order by rand() limit 0, ! ', array( FEATURED_PROFILES_TABLE, time(), $xid, $config['show_featured_profiles'] ) );
	} else {
		$list = $osDB->getAll('select fp.id, fp.userid from ! as fp, ! as usr where ? between fp.start_date and fp.end_date and fp.req_exposures > fp.exposures  and fp.userid <> ? and usr.id = fp.userid and usr.gender = ? '.$bannedlist.' order by rand() limit 0, ! ', array( FEATURED_PROFILES_TABLE, USER_TABLE, time(), $xid, $gender,  $config['show_featured_profiles'] ) );
	}

	$featured_profiles = array();

	foreach ($list as $usr) {

		$row = $osDB->getRow('select *, floor((to_days(curdate())-to_days(birth_date))/365.25)  as age from ! where id = ? and status=?', array( USER_TABLE, $usr['userid'],'active' ) );

		if ($row){
			/* Get countryname and statename */
			$row['statename'] = getStateName( $row['country'], $row['state_province'] ) ;
			$row['countryname'] = getCountryName($row['country'] ) ;
			$featured_profiles[] = $row;
			$osDB->query('update ! set exposures = exposures + 1 where id = ?', array( FEATURED_PROFILES_TABLE, $usr['id'] ) );
		}
	}
	$t->assign('featured_profiles', $featured_profiles);
	$genders = get_lang_values('search_genders');
	$genders_list="&nbsp;&nbsp;&nbsp;<select name='gender' onchange='javascript:featuredProfilesDisplay(this.value);'><option value='A' selected='selected' >".get_lang('signup_gender_look','A')."</option>";
	foreach($genders as $k=>$v) {
		$genders_list.="<option value='$k' ";
		if ($k==$gender) {$genders_list.=' selected="selected" ';}
		$genders_list.=">$v</option>";
	}
	$genders_list.="</select>";
	$t->assign('fphdr02',$genders_list);
	if (isset($_REQUEST['send']) ) {
		$disp=$t->fetch('featured_profiles_display.tpl');
		echo("|||featured_profiles_display|:|".$disp);
	}
	unset($list, $featured_profiles, $row);
}

?>