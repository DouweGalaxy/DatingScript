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


require_once(dirname(__FILE__).'/../minimum_init.php');
$ret='';
if (!isset($_GET['a']) || empty($_GET['a']) || !isset($_GET['v']) || empty($_GET['v'])) { 
	if ($config['accept_state'] =='1' or $config['accept_state'] =='Y') {
		$ret .= '|||srchlookstate_province|:|' .'<input name="srchlookstate_province" type="text" size="30" maxlength="100" />';
	}
	if ($config['accept_county'] == 'Y' ||$config['accept_county'] == '1') { 
		$ret .=  '|||srchlookcounty|:|' . '<input name="srchlookcounty" type="text" class="textinput" size="30" maxlength="100" />';
	}
	if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') { 
		$ret .= '|||srchlookcity|:|' . '<input name="srchlookcity" type="text" class="textinput" size="30" maxlength="100" />';
	}
	if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
		$ret .= '|||srchlookzip|:|' . '<input name="srchlookzip" type="text" class="textinput" size="30" maxlength="100" />';
	}

	$osDB->disconnect(); 
	return ''; 
}

if (trim($_GET['a']) == 'country') {
	$cntry = $_GET['v'];
} else {
	$cntry = $_GET['v1'];
}

if ($cntry == 'US') $_SESSION['radiustype'] = 'miles';

$zipsAvailable = zipsAvailable($cntry);

$zipsDisp =	'<table border=0 cellspacing="'.$config['cellspacing'].'" cellpadding="'.$config['cellpadding'].'" width="100%"><tr >'.
	'<td width="25%" valign="middle">'.get_lang('search_within').':</td><td valign="top"  width="75%"><table width="100%" cellpadding=0 border=0 cellspacing=0><tr>
	<td valign="middle" width="15">'.
	'<input name="lookradius" value="'.$_SESSION['lookradius'].'" type="text" class="textinput" size="5" maxlength="10" /></td>'.
	'<td valign="middle" width="6">'.
	'<input type=radio name="radiustype" value="miles"';
$zipsDisp .= ($_SESSION['radiustype'] == 'miles')? 'checked':'';
$zipsDisp .= '/></td><td width="15" valign="middle">'.get_lang('miles').
	'</td><td width="6" valign="middle"><input type=radio name="radiustype" value="kms"';
$zipsDisp .= ($_SESSION['radiustype'] == 'kms')?' checked ':'';
$zipsDisp .='/></td><td valign="middle" width="20">'.get_lang('kms').'</td><td  valign="middle">&nbsp;'.get_lang('of_zip_code').'</td></tr></table></td></tr></table>';


switch (trim($_GET['a'])) {

	case 'country':
		$countrycode = isset($_GET['v'])?$_GET['v']:DEFAULT_COUNTRY;
		if ($config['accept_state'] == 'Y' or $config['accept_state'] =='1') {
			$ret .= '|||srchlookstate_province|:|' . stateOptions($countrycode);
			if ($config['accept_county'] == 'Y' ||$config['accept_county'] == '1') { 
				$ret .=  '|||srchlookcounty|:|' . '<input name="srchlookcounty" type="text" class="textinput" size="30" maxlength="100" />';
			}
			if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') { 
				$ret .= '|||srchlookcity|:|' . '<input name="srchlookcity" type="text" class="textinput" size="30" maxlength="100" />';
			}
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||srchlookzip|:|' . '<input name="srchlookzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_county'] == 'Y' || $config['accept_county'] =='1') {
			$ret .= '|||srchlookcounty|:|' . countyOptions($countrycode, 'AA');
			if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') { 
				$ret .= '|||srchlookcity|:|' . '<input name="srchlookcity" type="text" class="textinput" size="30" maxlength="100" 	/>';
			}
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||srchlookzip|:|' . '<input name="srchlookzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') {
			$ret .=  '|||srchlookcity|:|' . cityOptions($countrycode, 'AA', 'AA');
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||srchlookzip|:|' . '<input name="srchlookzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_zipcode'] =='Y' || $config['accept_zipcode'] == '1') {
			$ret.= '|||srchlookzip|:|' . zipOptions($countrycode, 'AA', 'AA', 'AA');
		}
		break;

	case 'state':
		$statecode = $_GET['v'];
		$countrycode = $_GET['v1'];
		if ($config['accept_county'] == 'Y' || $config['accept_county'] =='1') {
			$ret .= '|||srchlookcounty|:|' . countyOptions($countrycode, $statecode);
			if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') { 
				$ret .= '|||srchlookcity|:|' . '<input name="srchlookcity" type="text" class="textinput" size="30" maxlength="100" 	/>';
			}
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||srchlookzip|:|' . '<input name="srchlookzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') {
			$ret .=  '|||srchlookcity|:|' . cityOptions($countrycode, $statecode, 'AA');
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||srchlookzip|:|' . '<input name="srchlookzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_zipcode'] =='Y' || $config['accept_zipcode'] == '1') {
			$ret.= '|||srchlookzip|:|' . zipOptions($countrycode, $statecode, 'AA', 'AA');
		}		
		break;

	case 'county':
		$countycode = $_GET['v'];
		$statecode = isset($_GET['v2'])?$_GET['v2']:'AA';
		$countrycode = $_GET['v1'];
		if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') {
			$ret .=  '|||srchlookcity|:|' . cityOptions($countrycode, $statecode, $countycode);
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||srchlookzip|:|' . '<input name="srchlookzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_zipcode'] =='Y' || $config['accept_zipcode'] == '1') {
			$ret.= '|||srchlookzip|:|' . zipOptions($countrycode, $statecode, $countycode, 'AA');
		}				
		break;

	case 'city':
		$citycode = $_GET['v'];
		$statecode = isset($_GET['v2'])?$_GET['v2']:'AA';
		$countrycode = $_GET['v1'];
		$countycode = isset($_GET['v3'])?$_GET['v3']:'AA';
		if ($config['accept_zipcode'] =='Y' || $config['accept_zipcode'] == '1') {
			$ret.= '|||srchlookzip|:|' . zipOptions($countrycode, $statecode, $countycode, $citycode);
		}				
		break;

	default : 
		$ret .= ''; 
		break;
}

function stateOptions($countrycode) { 
	global $config;
	$ret = '';
	$data = getStates(trim($countrycode),'N');
	if (count($data) < 1) return '<input name="srchlookstate_province" type="text" class="textinput" size="30" maxlength="100" />';
	if ($config['accept_county'] == 'Y' || $config['accept_county'] == '1') { 	
		$ret .= '	<select class="select" style="width: 175px" name="srchlookstate_province" onchange="javascript: cascadeState(this.value,this.form.srchlookcountry.value);" >';
	} elseif ($config['accept_city'] == '1' || $config['accept_city'] == 'Y')  {
		$ret .= '	<select class="select" style="width: 175px" name="srchlookstate_province" onchange="javascript:  cascadeCounty(\'AA\',this.form.srchlookcountry.value,this.value);" >';
	} elseif ( $config['accept_zipcode'] =='1' || $config['accpet_zipcode'] =='Y') {
		$ret .= '	<select class="select" style="width: 175px" name="srchlookstate_province" onchange="javascript:  cascadeCity(\'AA\',this.form.srchlookcountry.value,this.value,\'AA\');" >';
	}
	$ret .= '<option value="-1">'.get_lang('select_state').'</option>';

	foreach ($data as $k => $y){
		if ($k != 'AA') {
			$ret .= "<option value='$k'>$y</option>";
		}
	}
	unset ($data);

	return $ret .= '</select>';

}

function countyOptions($countrycode, $statecode) {
	global $config;
	$ret='';
	$data = getCounties(trim($countrycode),trim($statecode),'N');

	if (count($data) < 1) return '<input name="srchlookcounty" type="text" class="textinput" size="30" maxlength="100" />';

	if ($config['accept_city'] == '1' || $config['accept_city'] == 'Y')  {
		$ret .= '	<select class="select" style="width: 175px" name="srchlookcounty" onchange="javascript:  cascadeCounty(this.value,this.form.srchlookcountry.value,this.form.srchlookstate_province.value);" >';
	} elseif ( $config['accept_zipcode'] =='1' || $config['accpet_zipcode'] =='Y') {
		$ret .= '	<select class="select" style="width: 175px" name="srchlookcounty" onchange="javascript:  cascadeCity(\'AA\',this.form.srchlookcountry.value, this.form.srchlookstate_province.value, this.value);" >';
	}

	$ret .= '<option value="-1">'.get_lang('select_county').'</option>';
	foreach ($data as $k => $y) {
		if ($k != 'AA') {
			$ret .= "<option value='$k'>$y</option>";
		}
	}
	unset ($data);

	return $ret .= '</select>';
}

function cityOptions($countrycode, $statecode, $countycode) {
	global $config;
	$ret='';
	$data = getCities(trim($countrycode),trim($statecode),trim($countycode),'N');

	if (count($data) < 1) return '<input name="srchlookcity" type="text" size="30" maxlength="100" />';

	if ($config['accept_zipcode'] =='1' || $config['accept_zipcode'] =='Y') {
		if ($config.accept_county == 'Y' || $config.accept_county == '1') {
			$ret .= '	<select class="select" style="width: 175px" name="srchlookcity" onchange="javascript: cascadeCity(this.value,this.form.srchlookcountry.value,this.form.srchlookstate_province.value,this.form.srchlookcounty.value);" >';
		}else{
			$ret .= '	<select class="select" style="width: 175px" name="srchlookcity" onchange="javascript: cascadeCity(this.value,this.form.srchlookcountry.value,this.form.srchlookstate_province.value,\'AA\');" >';
		}
	} else {
		$ret .= '	<select class="select" style="width: 175px" name="srchlookcity" >'; 		
	}	

	$ret .= '<option value="-1">'.get_lang('select_city').'</option>';

	foreach ($data as $k => $y) $ret .= "<option value='$k'>$y</option>";

	unset($data);
	return $ret .= '</select>';
}


function zipOptions($countrycode, $statecode, $countycode, $citycode) {
	global $config;
	$ret='';
	$data = getZipcodes(trim($_GET['v1']),trim($_GET['v2']),trim($_GET['v3']),trim($_GET['v']),'N');

	if (count($data) < 1) return '<input name="srchlookzip" type="text" size="30" maxlength="100" />';

	$ret = '	<select class="select"  name="srchlookzip" >';

	foreach ($data as $k => $y) $ret .= "<option value='$k'>$y</option>";

	unset($data);
	return $ret .= '</select>';
}

if ($zipsAvailable == 1) {
	$ret .='|||zipsavailable|:|' .$zipsDisp;
} else {
	$ret .='|||zipsavailable|:|' .'<td></td>';
}
echo($ret);

$osDB->disconnect();
?>
