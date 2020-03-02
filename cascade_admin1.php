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
		$ret .= '|||txtstateprovince|:|' .'<input name="txtstateprovince" type="text" size="30" maxlength="100" />';
	}
	if ($config['accept_county'] == 'Y' ||$config['accept_county'] == '1') { 
		$ret .=  '|||txtcounty|:|' . '<input name="txtcounty" type="text" class="textinput" size="30" maxlength="100" />';
	}
	if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') { 
		$ret .= '|||txtcity|:|' . '<input name="txtcity" type="text" class="textinput" size="30" maxlength="100" />';
	}
	if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
		$ret .= '|||txtzip|:|' . '<input name="txtzip" type="text" class="textinput" size="30" maxlength="100" />';
	}
	echo($ret);
	$osDB->disconnect();
	exit;
}


switch (trim($_GET['a'])) {

	case 'country':
		$countrycode = isset($_GET['v'])?$_GET['v']:DEFAULT_COUNTRY;
		if ($config['accept_state'] == 'Y' or $config['accept_state'] =='1') {
			$ret .= '|||txtstateprovince|:|' . stateOptions($countrycode);
			if ($config['accept_county'] == 'Y' ||$config['accept_county'] == '1') { 
				$ret .=  '|||txtcounty|:|' . '<input name="txtcounty" type="text" class="textinput" size="30" maxlength="100" />';
			}
			if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') { 
				$ret .= '|||txtcity|:|' . '<input name="txtcity" type="text" class="textinput" size="30" maxlength="100" />';
			}
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||txtzip|:|' . '<input name="txtzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_county'] == 'Y' || $config['accept_county'] =='1') {
			$ret .= '|||txtcounty|:|' . countyOptions($countrycode, 'AA');
			if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') { 
				$ret .= '|||txtcity|:|' . '<input name="txtcity" type="text" class="textinput" size="30" maxlength="100" 	/>';
			}
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||txtzip|:|' . '<input name="txtzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') {
			$ret .=  '|||txtcity|:|' . cityOptions($countrycode, 'AA', 'AA');
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||txtzip|:|' . '<input name="txtzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_zipcode'] =='Y' || $config['accept_zipcode'] == '1') {
			$ret.= '|||txtzip|:|' . zipOptions($countrycode, 'AA', 'AA', 'AA');
		}
		echo($ret);
		break;

	case 'state':
		$statecode = $_GET['v'];
		$countrycode = $_GET['v1'];
		if ($config['accept_county'] == 'Y' || $config['accept_county'] =='1') {
			$ret .= '|||txtcounty|:|' . countyOptions($countrycode, $statecode);
			if ($config['accept_city'] == 'Y' || $config['accept_city'] == '1') { 
				$ret .= '|||txtcity|:|' . '<input name="txtcity" type="text" class="textinput" size="30" maxlength="100" 	/>';
			}
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||txtzip|:|' . '<input name="txtzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') {
			$ret .=  '|||txtcity|:|' . cityOptions($countrycode, $statecode, 'AA');
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||txtzip|:|' . '<input name="txtzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_zipcode'] =='Y' || $config['accept_zipcode'] == '1') {
			$ret.= '|||txtzip|:|' . zipOptions($countrycode, $statecode, 'AA', 'AA');
		}
		echo($ret);
		break;

	case 'county':
		$countycode = $_GET['v'];
		$statecode = isset($_GET['v2'])?$_GET['v2']:'AA';
		$countrycode = $_GET['v1'];
		if ($config['accept_city'] == 'Y' ||$config['accept_city'] == '1') {
			$ret .=  '|||txtcity|:|' . cityOptions($countrycode, $statecode, $countycode);
			if ($config['accept_zipcode'] == 'Y' ||$config['accept_zipcode'] == '1') { 
				$ret .= '|||txtzip|:|' . '<input name="txtzip" type="text" class="textinput" size="30" maxlength="100" />';
			}
		} elseif ($config['accept_zipcode'] =='Y' || $config['accept_zipcode'] == '1') {
			$ret.= '|||txtzip|:|' . zipOptions($countrycode, $statecode, $countycode, 'AA');
		}		
		echo($ret);
		break;

	case 'city':
		$citycode = $_GET['v'];
		$statecode = isset($_GET['v2'])?$_GET['v2']:'AA';
		$countrycode = $_GET['v1'];
		$countycode = isset($_GET['v3'])?$_GET['v3']:'AA';
		if ($config['accept_zipcode'] =='Y' || $config['accept_zipcode'] == '1') {
			$ret.= '|||txtzip|:|' . zipOptions($countrycode, $statecode, $countycode, $citycode);
		}		
		echo($ret);
		break;

	default : return ''; break;
}

function stateOptions($countrycode) { 
	global $config;
	$data = getStates(trim($countrycode),'N');
	if (count($data) < 1) return '<input name="txtstateprovince" type="text" class="textinput" size="30" maxlength="100" />';
	if ($config['accept_county'] == 'Y' || $config['accept_county'] == '1') { 	
		$ret .= '	<select class="select" style="width: 175px" name="txtstateprovince" onchange="javascript: cascadeState(this.value,this.form.txtfrom.value);" >';
	} elseif ($config['accept_city'] == '1' || $config['accept_city'] == 'Y')  {
		$ret .= '	<select class="select" style="width: 175px" name="txtstateprovince" onchange="javascript:  cascadeCounty(\'AA\',this.form.txtfrom.value,this.value);" >';
	} elseif ( $config['accept_zipcode'] =='1' || $config['accpet_zipcode'] =='Y') {
		$ret .= '	<select class="select" style="width: 175px" name="txtstateprovince" onchange="javascript:  cascadeCity(\'AA\',this.form.txtfrom.value,this.value,\'AA\');" >';
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
	$data = getCounties(trim($countrycode),trim($statecode),'N');

	if (count($data) < 1) return '<input name="txtcounty" type="text" class="textinput" size="30" maxlength="100" />';

	if ($config['accept_city'] == '1' || $config['accept_city'] == 'Y')  {
		$ret .= '	<select class="select" style="width: 175px" name="txtcounty" onchange="javascript:  cascadeCounty(this.value,this.form.txtfrom.value,this.form.txtstateprovince.value);" >';
	} elseif ( $config['accept_zipcode'] =='1' || $config['accpet_zipcode'] =='Y') {
		$ret .= '	<select class="select" style="width: 175px" name="txtcounty" onchange="javascript:  cascadeCity(\'AA\',this.form.txtfrom.value, this.form.txtstateprovince.value, this.value);" >';
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
	$data = getCities(trim($countrycode),trim($statecode),trim($countycode),'N');

	if (count($data) < 1) return '<input name="txtcity" type="text" class="textinput" size="30" maxlength="100" />';

	if ($config['accept_zipcode'] =='1' || $config['accept_zipcode'] =='Y') {
		if ($config.accept_county == 'Y' || $config.accept_county == '1') {
			$ret .= '	<select class="select" style="width: 175px" name="txtcity" onchange="javascript: cascadeCity(this.value,this.form.txtfrom.value,this.form.txtstateprovince.value,this.form.txtcounty.value);" >';
		}else{
			$ret .= '	<select class="select" style="width: 175px" name="txtcity" onchange="javascript: cascadeCity(this.value,this.form.txtfrom.value,this.form.txtstateprovince.value,\'AA\');" >';
		}
	} else {
		$ret .= '	<select class="select" style="width: 175px" name="txtcity" >'; 		
	}	
	$ret .= '<option value="-1">'.get_lang('select_city').'</option>';

	foreach ($data as $k => $y) {
		if ($k != 'AA') {

			$ret .= "<option value='$k'>$y</option>";
		}
	}
	unset ($data);

	return $ret .= '</select>';
}


function zipOptions($countrycode, $statecode, $countycode, $citycode) {

	$data = getZipcodes(trim($countrycode),trim($statecode),trim($countycode),trim($citycode),'N');

	if (count($data) < 1) return '<input name="txtzip" type="text" class="textinput" size="30" maxlength="100" />';

	$ret = '	<select class="select" style="width: 175px" name="txtzip" >';

	foreach ($data as $k => $y) $ret .= "<option value='$k'>$y</option>";

	unset ($data);

	return $ret .= '</select>';
}

$osDB->disconnect();

?>
