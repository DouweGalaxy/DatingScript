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

if (isset($_REQUEST) && is_array($_REQUEST) ) {
	foreach ($_REQUEST as $k => $v) {
		${$k} = $v;
	}
}

/* Loads zip codes */

if ( isset($cntry) && $cntry != '' ) {
	if (isset($_REQUEST['delzip']) && $_REQUEST['delzip'] != '') {
		/* Delete zips for the country */

		$osDB->query('delete from ! where countrycode = ?', array(ZIPCODES_TABLE, $cntry) );

		$msg = str_replace('#COUNTRY#', $lang['countries'][$cntry], get_lang('delzips_succ'));

		/* Analyze the table to adjust index values */
		$osDB->query('optimize table '.ZIPCODES_TABLE);
	}	elseif (isset($zipsdir) && $zipsdir != '') {
		$dirname = "../zipcodes/".$zipsdir;
		$filestoload=array();
		$filesdir = opendir($dirname);
		while($file = readdir($filesdir)) {
			if ($file != '.' && $file != '..' && !is_dir( $dirname.'/'.$file ) && substr_count($file, '.csv') > 0 ) {
				$filestoload[] = $file;

			}
		}
		sort($filestoload);
		reset($filestoload);


		$_SESSION['cntry_'.$cntry]['files'] = $filestoload;
		$_SESSION['cntry_'.$cntry]['filesdir'] = $dirname.'/';

		$t->assign('filestoload', $filestoload);

		$t->assign('filesdir', $dirname.'/');
		/* Analyze the table to adjust index values */
	}
}

$t->assign('cntry', (isset($cntry)?$cntry:''));

$t->assign('lang',$lang);

/* Get list of zip code files from the directory */
$files = array();
$dir = opendir("../zipcodes");
while($file = readdir($dir)) {
	$cntry_dir = '../zipcodes/'.$file;
	if ($file != '.' && $file != '..' && is_dir( $cntry_dir ) )
		$files[] = $file;
}

$t->assign('zipsdir',(isset($zipsdir)?$zipsdir:''));

$t->assign('files', $files);

unset($files, $zipsdir);

$t->assign('rendered_page', $t->fetch('admin/load_zips.tpl'));

$t->display('admin/index.tpl');

?>