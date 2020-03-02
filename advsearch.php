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

@ini_set('memory_limit','40M');

include( 'sessioninc.php' );

$data = array();

if (isset($_REQUEST['sectionid']) ) {
	$sectionid = $_REQUEST[ 'sectionid' ];
}

$sections = array_merge(array('0'=>'Signup Information'),$sections);

$t->assign('sections', $sections);

if ((isset($_REQUEST['search_new']) && $_REQUEST['search_new'] == 1) or (!isset($sectionid) && !isset($_REQUEST['advsearch']) && !isset($_SESSION['advsearch']) ) ) {

    $sectionid = 0;

    $_SESSION['advsearch'] = array();

    $_SESSION['advsearch']['srchlookagestart'] = $config['end_year']*-1;

    $_SESSION['advsearch']['srchlookageend'] = $config['start_year']*-1;

    $_SESSION['advsearch']['srchlookcountry'] = DEFAULT_COUNTRY;

}

/*  Query to reterive records from osdate_questions table
 sorted descending on mandatory -  mandatory fields should be displayed first
*/

if ((!isset($get_search) || $get_search == '') || isset($_REQUEST['advsearch'])) {
	if (isset($_REQUEST['cursectionid'] ) ) {

		if ($_REQUEST['cursectionid'] == '0' ) {
			/* Save data from section 0 - signup data */

			$_SESSION['advsearch']['srchusername'] = isset($_REQUEST['srchusername']) ? $_REQUEST['srchusername']:'';


			if (isset($_REQUEST['srchgender']) ) {

				$_SESSION['advsearch']['srchgender'] = $_REQUEST['srchgender'];

			} else {
				unset($_SESSION['advsearch']['srchgender']);
			}

			if (isset($_REQUEST['srchlookgender']) ) {

				$_SESSION['advsearch']['srchlookgender'] = $_REQUEST['srchlookgender'];

			} else {
				unset($_SESSION['advsearch']['srchlookgender']);
			}

			$_SESSION['advsearch']['srchlookagestart'] = $_REQUEST['srchlookagestart'];

			$_SESSION['advsearch']['srchlookageend'] = $_REQUEST['srchlookageend'];

			$_SESSION['advsearch']['srchradius'] = isset($_REQUEST['srchradius']) ? $_REQUEST['srchradius']:'';

			$_SESSION['advsearch']['radiustype'] = isset($_REQUEST['radiustype']) ? $_REQUEST['radiustype']:'';

			$_SESSION['advsearch']['with_photo'] = isset($_REQUEST['with_photo']) ? $_REQUEST['with_photo']:'';

			$_SESSION['advsearch']['with_video'] = isset($_REQUEST['with_video']) ? $_REQUEST['with_video']:'';
			$_SESSION['advsearch']['who_is_online'] = isset($_REQUEST['who_is_online']) ? $_REQUEST['who_is_online']:'';

			$_SESSION['advsearch']['srchlookcountry'] = $srchlookcountry = (isset($_REQUEST['srchlookcountry'])?(($_REQUEST['srchlookcountry']!='-1')?$_REQUEST['srchlookcountry']:'AA'):'AA');

			$_SESSION['advsearch']['srchlookcounty'] = $srchlookcounty = (isset($_REQUEST['srchlookcounty'])?(($_REQUEST['srchlookcounty']!='-1')?$_REQUEST['srchlookcounty']:'AA'):'AA');

			$_SESSION['advsearch']['srchlookcity'] = $srchlookcity = (isset($_REQUEST['srchlookcity'])?(($_REQUEST['srchlookcity']!='-1')?$_REQUEST['srchlookcity']:'AA'):'AA');

			$_SESSION['advsearch']['srchlookstate_province'] = $srchlookstate_province = (isset($_REQUEST['srchlookstate_province'])?(($_REQUEST['srchlookstate_province']!='-1')?$_REQUEST['srchlookstate_province']:'AA'):'AA');

			$_SESSION['advsearch']['srchlookzip'] = $srchlookzip = (isset($_REQUEST['srchlookzip'])?(($_REQUEST['srchlookzip']!='-1')?$_REQUEST['srchlookzip']:'AA'):'AA');

		} elseif ($_REQUEST['cursectionid'] > 0 && ( isset($_REQUEST['question']) || isset($_REQUEST['selected_questions']) ) ) {
		/* Check already selected options and if they are unchecked, remove session settings */
			if (isset($_REQUEST['selected_questions'])) {
				foreach ($_REQUEST['selected_questions'] as $k => $q) {
					unset($_SESSION['advsearch']['question'][$q]);
				}
			}

			if (isset($_REQUEST['question']) ) {
				foreach ($_REQUEST['question'] as $mkey => $val) {
					$_SESSION['advsearch']['question'][$mkey] = $val;
				}
			}

		}
	}
}


// edit by Adam
// make dropdowns propogate across sections
// pulled this code out of the above cursectionid==0 if block
// and moved down here so assignments were made and relations were kept outside of that section.

/* Added to avoid issue with !, ? and & as part of the field */
if (isset($_SESSION['advsearch']['sql']) ) $_SESSION['advsearch']['sql']=stripslashes($_SESSION['advsearch']['sql']);

foreach ($_SESSION['advsearch'] as $fld => $fldval) {
	$_SESSION['advsearch'][$fld] = str_replace(array("?","!","&"),array("\?","\!","\&"), $fldval);
}


$srchlookcountry = isset($_SESSION['advsearch']['srchlookcountry'])? $_SESSION['advsearch']['srchlookcountry']:'AA' ;

$srchlookcounty = isset($_SESSION['advsearch']['srchlookcounty']) ? $_SESSION['advsearch']['srchlookcounty']:'AA';

$srchlookstate_province = isset($_SESSION['advsearch']['srchlookstate_province']) ? $_SESSION['advsearch']['srchlookstate_province']:'AA';

$srchlookcity = isset($_SESSION['advsearch']['srchlookcity']) ? $_SESSION['advsearch']['srchlookcity']:'AA';

$srchlookzip = isset($_SESSION['advsearch']['srchlookzip'])? $_SESSION['advsearch']['srchlookzip']:'AA';

$srchradius = isset($_SESSION['advsearch']['srchradius']) ? $_SESSION['advsearch']['srchradius']:'';

$with_photo = isset($_SESSION['advsearch']['with_photo']) ? $_SESSION['advsearch']['with_photo']:'';

$with_video = isset($_SESSION['advsearch']['with_video']) ? $_SESSION['advsearch']['with_video']:'';
$who_is_online = isset($_SESSION['advsearch']['who_is_online']) ? $_SESSION['advsearch']['who_is_online']:'';

if ($config['accept_country'] == 'Y' || $config['accept_country'] =='1') {
	if ($srchlookcountry != '' && $srchlookcountry != 'AA' && ($config['accept_state'] =='1' || $config['accept_state'] =='Y') ) {

		$lang['lookstates'] = getStates($srchlookcountry,'Y');

		$zipsavailable = $osDB->getOne('select 1 from ! where countrycode = ? ', array(ZIPCODES_TABLE, $srchlookcountry) );

		$t->assign('zipsavailable', $zipsavailable);

		if (count($lang['lookstates']) == 1) {
			foreach ($lang['lookstates'] as $key => $val) {
				$_SESSION['advsearch']['srchlookstate_province'] = $srchlookstate_province = $key;
			}
		}
		$_SESSION['advsearch']['srchlookstate_province'] = $srchlookstate_province;
		
	}
	if ($config['accept_state'] != '1' && $config['accept_state'] != 'Y') {
		$srchlookstate_province = $_SESSION['advsearch']['srchlookstate_province'] = 'AA';
	}
	if ($config['accept_county'] != '1' && $config['accept_county'] != 'Y') {
		$srchlookcounty = $_SESSION['advsearch']['srchlookcounty'] = 'AA';
	}
	if ($config['accept_city'] != '1' && $config['accept_city'] != 'Y') {
		$srchlookcity = $_SESSION['advsearch']['srchlookcity'] = 'AA';
	}
	if ($config['accept_zipcode'] != '1' && $config['accept_zipcode'] != 'Y') {
		$srchlookzipcode = $_SESSION['advsearch']['srchlookzipcode'] = 'AA';
	}
	if ($srchlookstate_province != '' && $srchlookstate_province != 'AA' && $srchlookcountry != 'AA' && ($config['accept_county']=='1' || $config['accept_county'] =='Y') ) {

		$lang['lookcounties'] = getCounties($srchlookcountry, $srchlookstate_province, 'Y');

		if (count($lang['lookcounties']) == 1) {
			foreach ($lang['lookcounties'] as $key => $val) {
				$_SESSION['advsearch']['srchlookcounty'] = $srchlookcounty = $key;
			}
		}
		$_SESSION['advsearch']['srchlookcounty'] = $srchlookcounty;
		
	}
	if ($srchlookcountry != 'AA' && ($config['accept_city'] == '1' || $config['accept_city'] == 'Y') && ((($config['accept_county'] == '1' || $config['accept_county'] =='Y') && $srchlookcounty != '' && $srchlookcounty != 'AA') || ($config['accept_county'] != '1' && $config['accept_county'] != 'Y' && $srchlookstate_province != '' && $srchlookstate_province != 'AA')  )  ){

		$lang['lookcities'] = getCities($srchlookcountry, $srchlookstate_province, $srchlookcounty, 'Y');

		if (count($lang['lookcities']) == 1) {
			foreach($lang['lookcities'] as $key => $val) {
				$_SESSION['advsearch']['srchlookcity'] = $srchlookcity = $key;
			}
		}
		$_SESSION['advsearch']['srchlookcity'] = $srchlookcity;
	}
	if (($config['accept_zipcode']=='1' || $config['accept_zipcode'] =='Y') && $srchlookcountry != 'AA' && (($config['accept_city'] == '1' || $config['accept_city'] == 'Y') && $srchlookcity != '' && $srchlookcity != 'AA') && (($config['accept_county'] == '1' || $config['accept_county'] == 'Y') && $srchlookcounty != '' && $srchlookcounty != 'AA') && (($config['accept_state'] == '1' || $config['accept_state'] == 'Y') && $srchlookstate_province != '' && $srchlookstate_province != 'AA')){

		$lang['lookzipcodes'] = getZipcodes($srchlookcountry, $srchlookstate_province, $srchlookcounty, $srchlookcity, 'Y');
		$_SESSION['advsearch']['srchlookzip'] = $srchlookzip;
	}
}


$psize = getPageSize();

$t->assign ( 'psize',  $psize );


if (isset($_REQUEST['advsearch'])  ){
/* Search is requested. Now let us select data and display. Output in sqlselect. */

    if (!isset($_REQUEST['sort_by'])) {
    /* First time search actiated.. Prepare query */

 	       /* if not a blog search, do it like this */
 	       $sort_by = $config['search_sort_by'];

 	       $sort_order = ' asc ';

		   if ($sort_by == 'logintime') $sort_order = 'desc';

 	       if ($_SESSION['advsearch']['srchradius'] != '') {
 		       /* zipcode proximity search */
 	           /* First get the latitude and longitude of the zip code entered */
 	           $cntrycode=($_SESSION['advsearch']['srchlookcountry']!='AA')?$_SESSION['advsearch']['srchlookcountry']:$config['default_country'];

 	           $srchzip = $_SESSION['advsearch']['srchlookzip'];

 	           if ($cntrycode == 'GB') {
 	               $ukzip = explode(' ',$_SESSION['advsearch']['srchlookzip']);
 	               $srchzip = $ukzip[0];
 	           }

 	           $row = $osDB->getRow('select * from ! where code=?  and countrycode=?',array(ZIPCODES_TABLE, $srchzip, $cntrycode ) );

			   if (isset($row['latitude'])) {
				   $lat = $row['latitude'];
			   } else {
				   $lat='';
			   }
			   if (isset($row['langitude'])) {
	 	           $lng = $row['longitude'];
			   } else {
					$lng='';
			   }
			   unset($row);
 	           $zipcodes_in = "";

 	           if ($lng!='' && $lat!='') {

 	               $radius = $_SESSION['advsearch']['srchradius'];
 	               $radiustype = $_SESSION['advsearch']['radiustype'];

 	               if ($radiustype == 'kms') {
                    /* Kilometers calculation */
 	                   $_SESSION['advsearch']['zipcodes_in'] = " ( sqrt(power(69.1*(user.zip_latitude - $lat),2)+power(69.1*(user.zip_longitude-$lng)*cos(user.zip_latitude/57.3),2)) < " . $radius ." ) ";
 	               } else {
                    /* Miles  */
 	                   $_SESSION['advsearch']['zipcodes_in'] = " (  (3958* 3.1415926 * sqrt((user.zip_latitude - $lat) * (user.zip_latitude- $lat) + cos(user.zip_latitude / 57.29578) * cos($lat/57.29578)*(user.zip_longitude - $lng) * (user.zip_longitude - $lng))/180) < " . $radius ." ) ";
 	               }
				}
			}

	        $prefsel = "";

	        $questionmatch='';

	        $questionusers = array();

	        $_SESSION['advsearch']['questionusers']=array();
	        $_SESSION['advsearch']['questionmatch']='';
	        if (isset($_SESSION['advsearch']['question']) && count($_SESSION['advsearch']['question']) > 0){

				$match_needed = 0;

		        /* Let us make the question query */

	            foreach($_SESSION['advsearch']['question'] as $questionid => $options) {

	                $opts = '';
					if (count($options) <= 0) {
						$_SESSION['advsearch']['question'][$questionid] = '';
					} else {
						if ($questionid != 5 && $questionid != 27) {
			                foreach ($options as $k => $val) {

			                    if ($val != '') {

			                        if ($opts != '' ) $opts.=', ';

			                        $opts .= "'".$val."'";
			                    }
							}
		                } else {
							if (count($options) > 0) {
								if ($options[0] != '' && $options[1] != '') {
									$opts = ' between '.$options[0].' and '.$options[1];
								}
							}
						}
					}
	                if ($opts != '') {
						$match_needed++;

						if ($questionid != 5 && $questionid != 27) {
		                    $prefsel = "pref.answer in ( ".$opts." )  ";
						} else {
		                    $prefsel = "pref.answer ".$opts;

						}
		                $questionusers = $osDB->getAll("select distinct userid from ! as pref where ".$prefsel." group by  userid",array(USER_PREFERENCE_TABLE) );
						foreach($questionusers as $k=>$us){
							if (!in_array($matchedusers[$us['userid']])){
								$matchedusers[$us['userid']]='';
							}
						}
	                }
	            }

	            if (count($matchedusers) > 0) {
	            	foreach ($matchedusers as $ur => $v) {
                        if ($questionmatch != '') $questionmatch .= ", ";
                        $questionmatch.= "'".$ur."'";
                    }
                    $questionmatch = ' user.id in ('.$questionmatch.') ';
                    $_SESSION['advsearch']['questionmatch']=$questionmatch;

                    $_SESSION['advsearch']['questionusers']=$matchedusers;
					unset($matchedusers, $questionmatch);
	            } else {
                    $_SESSION['advsearch']['questionusers']=array();
                    $_SESSION['advsearch']['questionmatch']='';
				}
				unset($matchedusers, $questionmatch);
	        }

	        $actflag = get_lang('active');

			$photoqry='';
			if ($with_photo) {
				$photoqry = ' and user.id = ANY (select snp.userid from '.USER_SNAP_TABLE. ' as snp where snp.userid = user.id  ) ';
			}
			$videoqry='';
			if ($with_video) {
				$videoqry = ' and user.id = ANY (select snp.userid from '.USER_VIDEOS_TABLE. ' as vd where vd.userid = user.id  ) ';
			}

			if ($who_is_online) {
				$sqlselect = "SELECT SQL_CALC_FOUND_ROWS DISTINCT user.id, user.username, user.gender, user.lastvisit, user.country, user.about_me, user.state_province, user.county, user.city, user.lookgender, user.zip, onl.is_online, floor((to_days(curdate())-to_days(birth_date))/365.25)  as age FROM ! mem, ! user, ! as onl where onl.userid=user.id and user.level=mem.roleid and mem.includeinsearch='1' and user.active=1 and user.status in ('active', '".$actflag."')  ". $photoqry . $videoqry;
			} else {
				$sqlselect = "SELECT SQL_CALC_FOUND_ROWS DISTINCT user.id, user.username, user.gender, user.lastvisit, user.country, user.state_province,  user.about_me, user.county, user.city, user.lookgender, user.zip, onl.is_online, floor((to_days(curdate())-to_days(birth_date))/365.25)  as age FROM ! mem, ! user left join ! as onl on onl.userid=user.id where  user.level=mem.roleid and mem.includeinsearch='1' and user.active=1 and user.status in ('active', '".$actflag."')  " . $photoqry . $videoqry;
			}

	        if (isset($_SESSION['advsearch']['questionmatch']) && $_SESSION['advsearch']['questionmatch'] != '') {
	            $sqlselect .= ' and '.$_SESSION['advsearch']['questionmatch'];
	        }

			if (isset($_SESSION['advsearch']['zipcodes_in']) && $_SESSION['advsearch']['zipcodes_in']!='') {
				$sqlselect .= ' and '.$_SESSION['advsearch']['zipcodes_in'];
			}

			if (isset($_SESSION['advsearch']['srchusername'] ) && $_SESSION['advsearch']['srchusername'] != '') {
				$sqlselect .= "and upper(user.username) like upper('%".$_SESSION['advsearch']['srchusername']."%') ";
			}

			if (isset($_SESSION['advsearch']['srchlookgender']) && count($_SESSION['advsearch']['srchlookgender']) > 0) {
				$lookgender='';
				foreach($_SESSION['advsearch']['srchlookgender'] as $lg) {
					if ($lookgender != '') $lookgender.=", ";
					$lookgender .= "'".$lg."'";
				}
				$sqlselect .= " and user.gender in (".$lookgender.") ";
			}

			/* Bypass cross matching in search if set in global settings or the lookgender is not accepted */

			if ( ($config['bypass_search_lookgender'] == 'N' or $config['bypass_search_lookgender'] == '0' ) and ( $config['accept_lookgender'] == 'Y' or $config['accept_lookgender'] == '1') && $_SESSION['advsearch']['srchgender'] != '' ) {

				$txtgender_search = "and (user.lookgender = 'A' or (user.lookgender = 'B' and '".$_SESSION['advsearch']['srchgender']."' in ('M','F') ) or user.lookgender = '".$_SESSION['advsearch']['srchgender']."') ";

				$sqlselect .= $txtgender_search;
			}

			$yearstart  = $osDB->getOne('select date_sub(curdate(),interval '.($_SESSION['advsearch']['srchlookageend'] - 1) .' year)');

			$yearend  = $osDB->getOne('select date_sub(curdate(),interval '.($_SESSION['advsearch']['srchlookagestart']) .' year)');


			$sqlselect .= " and ( birth_date  between '".$yearstart."' and '". $yearend."' ) ";

	        if ($_SESSION['advsearch']['srchlookcountry']!='' and $_SESSION['advsearch']['srchlookcountry']!= 'AA' and $_SESSION['advsearch']['srchlookcountry']!= '-1') {
            $sqlselect .= " and user.country = '".$_SESSION['advsearch']['srchlookcountry']."' ";
	        }

			if (isset($_SESSION['advsearch']['zipcodes_in']) && $_SESSION['advsearch']['zipcodes_in']!='') {
				$sqlselect .= ' and '.$_SESSION['advsearch']['zipcodes_in'];
			} else {

				if ($_SESSION['advsearch']['srchlookcounty']!='' and $_SESSION['advsearch']['srchlookcounty']!= 'AA' and $_SESSION['advsearch']['srchlookcounty']!= '-1') {
				$sqlselect .= " and user.county = '".$_SESSION['advsearch']['srchlookcounty']."' ";
				}
				if ($_SESSION['advsearch']['srchlookstate_province']!='' and $_SESSION['advsearch']['srchlookstate_province']!= 'AA' and $_SESSION['advsearch']['srchlookstate_province']!= '-1') {
					$sqlselect .= " and user.state_province = '".$_SESSION['advsearch']['srchlookstate_province']."' ";
				}
				if ($_SESSION['advsearch']['srchlookcity']!='' and $_SESSION['advsearch']['srchlookcity']!= 'AA' and $_SESSION['advsearch']['srchlookcity']!= '-1') {
					$sqlselect .= " and user.city = '".$_SESSION['advsearch']['srchlookcity']."' ";
				}
				if (($_SESSION['advsearch']['srchlookzip']!='' and $_SESSION['advsearch']['srchlookzip']!= 'AA' and $_SESSION['advsearch']['srchlookzip']!= '-1') and $_SESSION['advsearch']['srchradius']=='') {
					$sqlselect .= " and user.zip = '".$_SESSION['advsearch']['srchlookzip']."' ";
				}
			}
	        $_SESSION['advsearch']['sql'] = $sqlselect;

	        $sqlselect .= ' '.sort_by_validate($sort_by, $sort_order);

	} elseif ($_REQUEST['sort_by'] != '' or $_REQUEST['page'] != '') {

		if ( (!isset($_REQUEST['page']) || (isset($_REQUEST['page']) && $_REQUEST['page']!= '')) && (!isset( $_REQUEST['sort_by']) || (isset( $_REQUEST['sort_by']) &&  $_REQUEST['sort_by'] == '') ) ) { $_REQUEST['sort_by'] = $_SESSION['sort_by']; }

		$_SESSION['sort_by'] = $_REQUEST['sort_by'];

		if ((isset( $_REQUEST['sort_by']) &&  $_REQUEST['sort_by'] == '')||!isset( $_REQUEST['sort_by'])) {

			$sort_by='username';

		} else {

			$sort_by=$_REQUEST['sort_by'];
		}

		if ((isset($_REQUEST['sort_order'] ) && $_REQUEST['sort_order'] == '')|| !isset($_REQUEST['sort_order']) )  {

			if ($_SESSION['sort_order'] == '') {
				$sort_order='asc';
			} else {
				$sort_order=$_SESSION['sort_order'];
			}

		} else {

			$sort_order=$_REQUEST['sort_order'];

		}

		$_SESSION['sort_order'] = $sort_order;

		$sqlselect = stripslashes($_SESSION['advsearch']['sql'])." ".sort_by_validate($sort_by, $sort_order);
	}

    /* If  not blog search, do it this way.  We already have the results for the blog
      search */
    if ( (isset($_REQUEST['cursectionid']) && $_REQUEST['cursectionid'] != 99) || !isset($_REQUEST['cursectionid'])  ) {

	    $t->assign('sort_by',$sort_by);

	    $t->assign('sort_order',$sort_order);

	    $cpage = isset($_REQUEST['page']) ? $_REQUEST['page']:1;

	    if( $cpage == '' ) $cpage = 1;

		$start = ( $cpage - 1 ) * $psize;

		$t->assign ( 'start', $start );

            /* Actually perform the search query to see what info we will get */
	    $rs = $osDB->getAll(stripslashes($sqlselect)." limit $start,$psize ", array(MEMBERSHIP_TABLE, USER_TABLE, ONLINE_USERS_TABLE));

		$rcount = $osDB->getOne('select FOUND_ROWS()');

		unset($sqlselect);

	    $lang['sort_types'] = get_lang_values('sort_types');

	    $lang['search_results_per_page'] = get_lang_values('search_results_per_page');

		if( $rcount > 0 ) {

			$t->assign( 'totalrecs', $rcount );

			$pages = ceil( $rcount / $psize );

			if( $pages > 1 ) {

				if ( $cpage > 1 ) {

					$prev = $cpage - 1;

					$t->assign( 'prev', $prev );

				}

				$t->assign ( 'cpage', $cpage );

				$t->assign ( 'pages', $pages );

				if ( $cpage < $pages ) {

					$next = $cpage + 1;

					$t->assign ( 'next', $next );

				}

			}
			$data = array();

			foreach( $rs as $row) {

				$row['countryname'] = getCountryName($row['country'] );

				$row['statename'] = getStateName(  $row['country'], $row['state_province'] );

				if (count($_SESSION['advsearch']['questionusers']) > 0 ) {
					foreach ($_SESSION['advsearch']['questionusers'] as $usr) {
						if ($usr['userid'] == $row['id']) {
							$row['matchcnt'] = $usr['match_cnt'];
						}
					}
				}

						  /* Save the search results into data */
				$data[] = $row;
			}
		} else {
			$t->assign( 'error', 1);
		}

	}

    $lang['sort_types'] = get_lang_values('sort_types');

	$t->assign('advsearch', $_SESSION['advsearch']);

	$t->assign ( 'data', $data );

	unset ($data, $rs);

	$t->assign ( 'lang', $lang );

	$t->assign('rendered_page', $t->fetch('advmatch.tpl') );

    $t->display ( 'admin/index.tpl' );

    exit;

}

if ($sectionid > 0 ) {
	$currdisplayorder = $osDB->getOne('select displayorder from ! where id=?', array(SECTIONS_TABLE, $sectionid) );

	$nextsectionid = $osDB->getOne('select id from ! where displayorder > ? and enabled = ? order by displayorder asc',array(SECTIONS_TABLE, $currdisplayorder, 'Y') );

	if (!isset($nextsectionid)) $nextsectionid = 0;

    /* reterive record from osdate_questions and osdate_questionoptions table   */

    $temp = $osDB->getAll( 'select id, question, mandatory, description, guideline, maxlength, control_type, extsearchhead from ! where enabled = ? and section = ? and question <> ? and extsearchable = ?  order by mandatory desc, displayorder', array( QUESTIONS_TABLE, 'Y', $sectionid , '', 'Y') );

    $data = array();

    foreach( $temp as $index => $row ) {
        if (($config['use_extsearchhead'] == '1' or $config['use_extsearchhead'] == 'Y') && $row['extsearchhead'] != '') {
            $row['question'] = $row['extsearchhead'];
        }

		/* THis is made to adjust for multi-language */
		if ($_SESSION['opt_lang'] != 'english') {
	        if (($config['use_extsearchhead'] == '1' or $config['use_extsearchhead'] == 'Y') && $row['extsearchhead'] != '') {
	            $lang_question = $_SESSION['profile_questions'][$row['id']]['extsearchhead'];
	        } else {
				$lang_question = $_SESSION['profile_questions'][$row['id']]['question'];
			}
			$lang_descr = 	$_SESSION['profile_questions'][$row['id']]['description'];
			$lang_guide = 	$_SESSION['profile_questions'][$row['id']]['guideline'];
			if ($lang_question != '') {
				$row['question'] = $lang_question;
			}
			if ($lang_descr != '') {
				$row['description'] = $lang_descr;
			}
			if ($lang_guide != '') {
				$row['guideline'] = $lang_guide;
			}
		}

        $options = $osDB->getAll( 'select * from ! where enabled = ? and questionid = ? order by displayorder', array( OPTIONS_TABLE, 'Y', $row['id'] ) ) ;

		/* THis is made to adjust for multi-language */
		$optsrs = array();
		if ($_SESSION['opt_lang'] != 'english') {
			foreach($options as $kx => $opt) {
				$lang_ansopt = $_SESSION['profile_questions'][$row['id']][$opt['id']];
				if ($lang_ansopt != '') {
					$opt['answer'] = $lang_ansopt;
				}
				$optsrs[] = $opt;
			}
		} else {$optsrs = $options; }

		unset($options);

        $row['options'] = makeOptions ( $optsrs);

        $endoptions = makeOptions ($optsrs);

		unset($optsrs);

        krsort($endoptions);

        reset($endoptions);

        $row['endoptions'] = $endoptions;

        $data [] = $row;
    }
	unset($row, $temp);
        /* Get a default date for the blog search */
	if ((isset( $_SESSION['advsearch']['date_posted'] ) &&  $_SESSION['advsearch']['date_posted'] == '') || !isset( $_SESSION['advsearch']['date_posted'] )  ) {

		$_SESSION['advsearch']['date_posted'] = date('Y-m-d');
	}
} else {

	$nextsectionid = $osDB->getOne('select id from ! where enabled = ? order by displayorder asc',array(SECTIONS_TABLE,'Y') );
}


$t->assign('nextsectionid',$nextsectionid);

if ( isset( $_GET['errid'] ) ) {

    $t->assign( 'mandatory_question_error', get_lang('errormsgs',$_GET['errid']) );

}

if ((isset($_SESSION['search_save_type']) && ($_SESSION['search_save_type'] == 'N' or $_SESSION['search_save_type'] == 'R' ) ) && (!isset($get_search) or $get_search == '')  && $_REQUEST['search_new'] != '1' ) {

    /* Save this search */
    if ($_SESSION['search_save_type'] == 'N') {
        $srch_name = $_SESSION['search_new_name'];
    } else {
        $srch_name = $_SESSION['search_name'];
    }

    $qry_txt = serialize($_SESSION['advsearch']);

    if ($srch_name != '') {

		save_search($qry_txt, $srch_name);

    }

	$_SESSION['search_name'] = $srch_name;

	unset($qry_txt, $srch_name);
	$_SESSION['search_new_name'] = '';

	$_SESSION['save_type'] = 'R';

}

$t->assign( 'data', $data );

$t->assign( 'head', $sections[ $sectionid ] );

unset($data, $sections);

$t->assign('lang', $lang);

$t->assign( 'sectionid', $sectionid );

$t->assign('frmname', 'frm' . $sectionid );

$t->assign('advsearch', $_SESSION['advsearch']);

$t->assign('rendered_page', $t->fetch('advsearch.tpl') );

$t->display('admin/index.tpl');

function save_search ($qry_txt, $srch_name){
	global $osDB;

	$rec_available = $osDB->getOne('select 2 from ! where userid=? and search_name=?', array(USER_SEARCH_TABLE, $_SESSION['UserId'], trim($srch_name)) );

	if (isset($rec_available) && $rec_available==2) {
		$osDB->query('update ! set query=? where userid=? and search_name=?', array(USER_SEARCH_TABLE, $qry_txt, $_SESSION['UserId'], trim($srch_name)) );
	} else {
		$osDB->query('insert into ! (userid, search_name, query) values (?, ?, ?)', array(USER_SEARCH_TABLE, $_SESSION['UserId'], trim($srch_name), $qry_txt) );
	}

	$_SESSION['search_name'] = $srch_name;

	$_SESSION['search_new_name'] = '';

	$_SESSION['save_type'] = 'R';
}
function sort_by_validate($sort_by, $sort_order=' asc '){

	$sortme = ' order by ';
	if ($sort_by == 'username') {

		$sortme .= 'user.username ';

	} elseif ( $sort_by == 'age' ) {

		$sortme .= ' age ';

	} elseif ( $sort_by == 'level' ) {

		$sortme .= ' user.level ';

	} elseif ( $sort_by == 'logintime' ) {

		$sortme .= 'user.lastvisit ';

		$sort_order = ' desc ';

	} elseif ( $sort_by == 'online' ) {

		$sortme .= ' onl.is_online '.$sort_order.', user.username ';
	}

	return $sortme.' '.$sort_order;

}

?>
