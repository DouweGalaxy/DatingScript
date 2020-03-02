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
**********************************************/


require_once(dirname(__FILE__).'/../init.php');

$editcode = $editedcode='';
if (isset($_REQUEST['edit']) && $_REQUEST['edit'] !='') $code = $editcode=$_REQUEST['edit'];

if (isset($_REQUEST['edited']) && $_REQUEST['edited'] != '') $code = $editedcode  =$_REQUEST['edited'];

$ret = '';

if (isset($_SESSION['now_editing']) && $_SESSION['now_editing'] != $editedcode && $_SESSION['now_editing'] != '' && $_SESSION['now_editing'] != $editcode) {

	/* There was an editing field open. Reverse it */
	$glblrow = $osDB->getRow("select * from ! where config_variable = ?",array(CONFIG_TABLE,$_SESSION['now_editing']) );

	if ($glblrow['config_value'] =='') {
		$ret .= '|||row_'.$_SESSION['now_editing'].'_col2|:|&nbsp;';
	} else {
		$ret .= '|||row_'.$_SESSION['now_editing'].'_col2|:|'.(($glblrow['config_variable'] == 'SMTP_PASS')?'*******':$glblrow['config_value']);
	}
	$ret .= '|||row_'.$_SESSION['now_editing'].'_col3|:|';
	$ret.= '<a href="#" onClick="getglblsettings('."'".$glblrow['groupid']."','".$_SESSION['now_editing']."'".');"><img src="images/button_edit.png" border="0" alt="" />';
	$_SESSION['now_editing'] = '';
}

if ($editedcode != '' ) {

	$newval = $_REQUEST['val'];

	if ($code == 'SMTP_AUTH') {
		if ($newval == 'Y') {
			$newval = '1';
		} else {
			$newval = '0';
		}
	}

	if ($code == 'forum_path' && $newval == '') $newval='None';

	$osDB->query( 'UPDATE ! SET config_value = ?, update_time=? WHERE config_variable = ?', array( CONFIG_TABLE, trim($newval),time(), $code ) );

	if ($code == 'skin_name' || $code == 'site_name') {

		/* Change in template. Copy the files to the curent directory */

		/* Remove files from templates_c directory */
		if ( $handle = opendir( TEMPLATE_C_DIR ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file != '.' && $file != '..' && $file != 'index.html' && $file != 'index.htm') {
					unlink( TEMPLATE_C_DIR . $file );
				}
			}
			closedir($handle);

		}
		/* Remove cache files */
		if ( $handle = opendir( CACHE_DIR ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file != '.' && $file != '..' && $file != 'index.html' && $file != 'index.htm') {
					unlink( CACHE_DIR . $file );
				}
			}
			closedir($handle);
		}
		/* Now see the case of watermark changes */
	}
/*
	if ($code == 'watermark_snaps' || $code == 'watermark_image' || $code == 'watermark_image_intensity' || $code == 'watermark_position_h' || $code == 'watermark_position_v' ||$code == 'watermark_margin' || $code == 'watermark_text_shadow' || $code == 'watermark_text_color')
	{
		if ( $handle = opendir( USER_IMAGE_CACHE_DIR ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file != '.' && $file != '..' && $file != 'index.html' && $file != 'index.htm' ) {
					unlink( USER_IMAGE_CACHE_DIR . $file );
				}
			}
			closedir($handle);
		}
	}
*/
	if ($code == 'forum_installed') {
		$osDB->query( 'UPDATE '.CONFIG_TABLE." SET config_value = 'None', update_time = '".time()."' WHERE config_variable = 'forum_path'");

	}
}

if ($code != '') {
	$glblrow = $osDB->getRow("select * from ! where config_variable = ?",array(CONFIG_TABLE,$code) );


	$group = $glblrow['groupid'];
	$ret .= '|||row_'.$code.'_col2|:|';

	switch (trim($code)) {
		case 'cntry_mgt':
		case 'snaps_require_approval':
		case 'images_in_db':
		case 'default_active_status':
		case 'feedback_info':
		case 'use_profilepopups':
		case 'drop_tn_also':
		case 'use_extsearchhead':
		case 'seo_username':
		case 'mod_rating_allow_com':
		case 'mod_rating_allow_rep':
		case 'bypass_regconfirm':
		case 'bypass_search_lookgender':
		case 'newuser_admin_info':
		case 'newpic_admin_info':
		case 'newvideo_admin_info':
		case 'display_all_menu_items':
		case 'enable_shoutbox':
		case 'disable_cache':
		case 'aff_default_active_status':
		case 'accept_timezone':
		case 'timezone_mandatory':
		case 'enable_mod_rewrite':
		case 'watermark_text_shadow':
		case 'banner_in_emails':
		case 'flashbb_installed':
		case 'forum_display_in_same_window':
		case 'luckyspin_genderwise':
		case 'SMTP_AUTH':
		case 'country_mandatory':
		case 'accept_country':
		case 'state_mandatory':
		case 'accept_state':
		case 'county_mandatory':
		case 'accept_county':
		case 'city_mandatory':
		case 'accept_city':
		case 'zipcode_mandatory':
		case 'accept_zipcode':
		case 'accept_allow_viewonline':
		case 'default_allow_viewonline':
		case 'allow_viewonline_mandatory':
		case 'accept_timezone':
		case 'accept_address_line1':
		case 'accept_address_line2':
		case 'accept_lookage':
		case 'accept_lookgender':
		case 'accept_lookcountry':
		case 'lookcountry_mandatory':
		case 'accept_address_line2':
		case 'accept_lookstate':
		case 'accept_lookcounty':
		case 'accept_lookcity':
		case 'accept_lookzipcode':
		case 'nomail_for_onlineuser':
		case 'accept_about_me':
		case 'about_me_mandatory':
		case 'about_me_in_smallprofile':
		case 'update_profile_inactive':
		case 'letter_featuredprofile':
		case 'letter_winkreceived':
		case 'letter_messagereceived':
		case 'letter_hotlist':
		case 'letter_buddylist':
		case 'letter_banlist':
		case 'letter_profilereactivated':
		case 'letter_blogcommentreceived':
		case 'accept_supreq':
		case 'accept_profpic_signup':
		case 'accept_profpic_signup_must':
		case 'allow_reply_by_all':
		case 'mail_queue':
		case 'newpic_admin_act_ltr':
		case 'newvideo_admin_act_ltr':
			if ($editcode != '') {
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				foreach ($lang['enabled_values'] as $cd=>$rowval) {
					$ret.='<option value="'.$cd.'"';
					if ($cd == $glblrow['config_value']) { $ret .= " SELECTED ";}
					$ret .= '>'.$rowval."</option>";
				}
				$ret .= '</select>';
			} else {
				$ret.=$glblrow['config_value'];
			}
			break;
		case 'lastnewusers_display':
		case 'featuredprofiles_display':
		case 'recentactiveprofiles_display':
		case 'randomprofiles_display':
		case 'advmatch_display':
		case 'searchmatch_display':
		case 'mymatches_display':
		case 'iplocation_prof_display':
		case 'newest_profpics_display':
			if ($editcode != '') {
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				$ret.='<option value="tiny"';
				if ('tiny' == $glblrow['config_value']) { $ret .= " SELECTED ";}
				$ret .= '>Tiny display</option>';
				$ret.='<option value="mini"';
				if ('mini' == $glblrow['config_value']) { $ret .= " SELECTED ";}
				$ret .= '>Mini display</option>';
				$ret .= '</select>';
			} else {
				$ret.=$glblrow['config_value'];
				$forumrow = $osDB->getRow("select * from ".CONFIG_TABLE." where config_variable = 'forum_path'" );
				$ret .= '|||row_forum_path_col2|:|'.$forumrow['config_value'] ;
			}
			break;

		case 'forum_installed':
			if ($editcode != '') {
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				foreach ($lang['forum_values'] as $cd=>$rowval) {
					$ret.='<option value="'.$cd.'"';
					if ($cd == $glblrow['config_value']) { $ret .= " SELECTED ";}
					$ret .= '>'.$rowval."</option>";
				}
				$ret .= '</select>';
			} else {
				$ret.=$glblrow['config_value'];
				$forumrow = $osDB->getRow("select * from ".CONFIG_TABLE." where config_variable = 'forum_path'" );
				$ret .= '|||row_forum_path_col2|:|'.$forumrow['config_value'] ;
			}
			break;

		case 'mod_rating_inc_order':
			if ($editcode != '') {
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				foreach ($lang['mod_lowtohigh'] as $cd=>$rowval) {
					$ret.='<option value="'.$cd.'"';
					if ($cd == $glblrow['config_value']) { $ret .= " SELECTED ";}
					$ret .= '>'.$rowval."</option>";
				}
				$ret .= '</select>';
			} else {
				$ret.=$glblrow['config_value'];
			}
			break;
		case 'default_country':
			if ($editcode != '') {
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				foreach ($lang['countries'] as $cd=>$rowval) {
					$ret.='<option value="'.$cd.'"';
					if ($cd == $glblrow['config_value']) { $ret .= " SELECTED ";}
					$ret .= '>'.$rowval."</option>";
				}
				$ret .= '</select>';
			} else {
				$ret.=$glblrow['config_value'];
			}
			break;
		case 'skin_name':
			if ( $handle = opendir( TEMPLATE_DIR ) ) {

				while (false !== ( $file = readdir( $handle ) ) ) {

					if ( $file != '.' && $file != '..'  && $file != 'pages' && $file != 'install') {

						if ( is_dir( TEMPLATE_DIR . $file ) ) {

							$temp_dirs[$file] = $file;

						}
					}
				}

				closedir($handle);
			}

			asort($temp_dirs);

			reset($temp_dirs);
			if ($_REQUEST['edit'] != '') {
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				foreach ($temp_dirs as $cd=>$rowval) {
					$ret.='<option value="'.$rowval.'"';
					if ($rowval == $glblrow['config_value']) { $ret .= " SELECTED ";}
					$ret .= '>'.$rowval."</option>";
				}
				$ret .= '</select>';
			} else {
				$ret.=$glblrow['config_value'];
			}
			break;
		case 'default_user_level':
		case 'expired_user_level':
			if ($editcode != '') {
				$mships = $osDB->getAll('select roleid, name from ! ', array(MEMBERSHIP_TABLE) );

				$memberships = array();

				foreach ($mships as $row ) {
					$memberships[$row['roleid']] = $row['name'];
				}
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				foreach ($memberships as $cd=>$rowval) {
					$ret.='<option value="'.$cd.'"';
					if ($cd == $glblrow['config_value']) { $ret .= " SELECTED ";}
					$ret .= '>'.$rowval."</option>";
				}
				$ret .= '</select>';
			} else {
				$mship_name = $osDB->getOne('select name from ! where roleid = ?', array(MEMBERSHIP_TABLE, $glblrow['config_value']) );
				$ret.=$mship_name;
			}
			break;
		case 'search_sort_by':
			if ($editcode != '') {
				$sort_by = $glblrow['config_value'];
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				$ret .= '<option value="username" ';
				if ($sort_by == 'username') $ret .= ' selected ';
				$ret.= '>'.get_lang('username_without_colon').'</option><option value="age" ';
				if ($sort_by == 'age') $ret.=' selected ';
				$ret .= '> '.get_lang('age').'</option><option value="logintime" ';
				if ($sort_by == 'logintime') $ret.=' selected ';
				$ret.='>'.get_lang('logintime').'</option><option value="online" ';
				if ($sort_by == 'online') $ret.=' selected ';
				$ret.='> '.get_lang('online').'</option><option value="level" ';
				if ($sort_by == 'level') $ret.=' selected ';
				$ret.='> '.get_lang('membership_hdr').'</option> ';
				$ret .= '</select>';
			} else {
				$ret.=	$glblrow['config_value'];
			}
			break;
		case 'admin_lang':

			if ($editcode != '') {

				$ss = 'select distinct lang from '.LANGUAGE_TABLE;
				$langs = $osDB->getAll($ss );
				$ret.='<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';

				foreach ($langs as $val) {
					$ret.="<option value='".$val['lang']."' ";
					if ($glblrow['config_value']==$val['lang']) {
						$ret.=" SELECTED ";
					}
					$ret.=">".$val['lang']."</option>";
				}
				$ret.="</select>";
			} else {
				$ret.=	$glblrow['config_value'];
			}
			break;
		
		case 'MAIL_FORMAT':

			if ($editcode != '') {

				$ret.='<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';

				$ret.="<option value='html' ";
				if ($glblrow['config_value']=='html') {
					$ret.=" SELECTED ";
				}
				$ret.=">".'Html'."</option>";
				$ret.="<option value='text' ";
				if ($glblrow['config_value']=='text') {
					$ret.=" SELECTED ";
				}
				$ret.=">".'Text'."</option>";
				$ret.="</select>";
			} else {
				$ret.=	$glblrow['config_value'];
			}
			break;
		case 'MAIL_TYPE':

			if ($editcode != '') {
				$ret.='<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';

				$ret.="<option value='mail' ";
				if ($glblrow['config_value']=='mail') {
					$ret.=" SELECTED ";
				}
				$ret.=">".'Standard Mail'."</option>";
				$ret.="<option value='smtp' ";
				if ($glblrow['config_value']=='smtp') {
					$ret.=" SELECTED ";
				}
				$ret.=">".'SMTP'."</option>";
				$ret.="<option value='sendmail' ";
				if ($glblrow['config_value']=='sendmail') {
					$ret.=" SELECTED ";
				}
				$ret.=">".'Sendmail'."</option>";
				$ret.="</select>";
			} else {
				$ret.=	$glblrow['config_value'];
			}
			break;
		case 'menutype':
		case 'adminmenutype':
			if ($editcode != '') {
				$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
				$ret.="<option value='sideF' ";
				if ($glblrow['config_value']=='sideF') {
					$ret.=" SELECTED ";
				}
				$ret.=">".'Side Full Menu'."</option>";
				$ret.="<option value='sideM' ";
				if ($glblrow['config_value']=='sideM') {
					$ret.=" SELECTED ";
				}
				$ret.=">".'Side Fold Menu'."</option>";
				$ret.="<option value='top' ";
				if ($glblrow['config_value']=='top') {
					$ret.=" SELECTED ";
				}
				$ret.=">".'Top Menu'."</option>";
			} else {
				$ret.=$glblrow['config_value'];
			}
			break;

		case 'mail_quecount':
		default:
			if ($editcode != '') {
				if ($group == '4' && $code != 'message_days_old' && $code != 'message_count' && $code != 'message_warn_days' && $code != 'SMTP_USER' && $code != 'SMTP_PASS' && $code != 'mail_count' && $code != 'SMTP_HOST' && $code != 'SMTP_PORT' && $code != 'SM_PATH' && $code != 'mail_queuecount'){
					$ret .= '<select name="txtconfigval_'.$code.'" id="txtconfigval_'.$code.'">';
					foreach (get_lang_values('enabled_values') as $cd=>$rowval) {
						$ret.='<option value="'.$cd.'"';
						if ($cd == $glblrow['config_value']) { $ret .= " SELECTED ";}
						$ret .= '>'.$rowval."</option>";
					}
					$ret .= '</select>';
				} else {
					if ($code == 'SMTP_PASS') { $typ = 'password'; 
					} else { $typ = 'text'; }
					if ($glblrow['config_value']=='') {
						$ret.='<input type="'.$typ.'" name="txtconfigval_'.$code.'"  id="txtconfigval_'.$code.'" value=" " size="25" /> ';
					} else {
						$ret.='<input type="'.$typ.'" name="txtconfigval_'.$code.'"  id="txtconfigval_'.$code.'" value="'.$glblrow['config_value'].'" size="25" /> ';
					}
				}
			} else {
				
				$ret.=	($code == 'SMTP_PASS')?'*********':$glblrow['config_value'];
			}

	}
	/* Now put save and other commands */
	if ($glblrow['config_value']=='') {
		$ret .= ' |||row_'.$code.'_col3|:|';
	} else {
		$ret .= '|||row_'.$code.'_col3|:|';
	}
	if ($editcode != '') {
		if ($code == 'watermark_text_color' || $code == 'bgcolor' || $code == 'textcolor' ){
			$ret.= '<a href="javascript:TCP.popup(document.forms[\'frm001\'].elements['."'txtconfigval_".$code."'".'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src = "images/sel.gif" /></a>&nbsp;';
		}
		$ret.= '<a href="#row_'.$code.'_col2" onClick="validate('."'".$group."','" . $code."',1".');"><img src="images/button_save.jpg" border="0" alt="" /></a>&nbsp;<a href="#" onClick="validate('."'".$group."','".$code."',".'2);"><img src="images/button_cancel1.jpg" border="0" alt="Cancel" /></a>';
		$_SESSION['now_editing'] = $code;
	} else {
		$ret.= '<a href="#" onClick="getglblsettings('."'".$group."','".$code."'".');"><img src="images/button_edit.png" border="0" alt="" />';
	}
}
echo ($ret);
?>