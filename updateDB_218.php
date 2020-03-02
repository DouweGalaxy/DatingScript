<?PHP
/* This program will just update the database with changes needed for osDate 2.1.8  */

include('../minimum_init.php');

/* Update the questions table for making 'A' as default value for gender */
$osDB->query("update ".DB_PREFIX."_questions set gender='A' where gender is null or gender = ''");

$osDB->query('delete from ! where adminid = ?', array(DB_PREFIX.'_admin_permissions', 1) );
$osDB->query("INSERT INTO `".DB_PREFIX."_admin_permissions` (`id`, `adminid`, `site_stats`, `profie_approval`, `profile_mgt`, `section_mgt`, `affiliate_mgt`, `affiliate_stats`, `news_mgt`, `article_mgt`, `story_mgt`, `poll_mgt`, `search`, `ext_search`, `send_letter`, `pages_mgt`, `chat`, `chat_mgt`, `forum_mgt`, `mship_mgt`, `payment_mgt`, `banner_mgt`, `seo_mgt`, `admin_mgt`, `admin_permit_mgt`, `global_mgt`, `change_pwd`,`cntry_mgt`,`snaps_require_approval`,`featured_profiles_mgt`, `calendar_mgt`, `event_mgt`, `import_mgt`,`profile_ratings`, `plugin_mgt`,`promo_mgt`, `blog_mgt`    ) VALUES (1, 1, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1','1','1','1','1','1','1','1', '1','1','1')");
echo('Updating "admin" user with all privileges... Done<br /><br />');flush();

$payment_modules = $osDB->getAll('select distinct module_key from '.DB_PREFIX.'_payment_config');
foreach ($payment_modules as $key => $val) {
	$osDB->query('update '.DB_PREFIX.'_payment_modules set enabled="Y" where module_key="'.$val['module_key'].'"');
}
echo('Setting installed payment modules... Done<br /><br />');flush();

$loadlang[]='lang_english';
if (count($loadlang) > 0) {
/* we need to load language files. If the table is available, remove current data for the language

This has to work in initial loading as well as upgrade
*/
	foreach ($loadlang as $key => $ldlang) {
		$language= str_replace('lang_','',$ldlang);
		$osDB->query('delete from '.DB_PREFIX."_languages where lang='".$language."'");

		$file = dirname(__FILE__) . '/language/'.$ldlang.'/lang_main.php';
		$file = str_replace( 'install_files/', '', $file );

		$lang = array();

		include $file;

		$sql = 'insert into ! (lang, mainkey, subkey, descr) values (?, ?, ?, ?)';
		foreach ($lang as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $subkey => $descr) {
					$osDB->query($sql, array(DB_PREFIX.'_languages', $language, $key, $subkey, htmlentities($descr)));
				}
			} else {
				$osDB->query($sql, array(DB_PREFIX.'_languages', $language, $key, "", htmlentities($val)));
			}
		}
		echo('<tr><td><span style="margin-left:12px;">'.ucfirst($language).' language  is loaded...</span></td></tr>'); flush();
	}
}
echo("Loading language files...Done <br /><br />");flush();

/* Now update pictures loaded counts for the sample data */
$pics = $osDB->getAll('select userid, count(*) as cnt from ! group by userid', array(USER_SNAP_TABLE));

foreach ($pics as $pic) {
	$osDB->query('update ! set pictures_cnt=? where id=?', array(USER_TABLE, $pic['cnt'], $pic['userid']));
}

/* Update extsearchable flag to 'Y' to enable extended search for all items */
$osDB->query('update ! set extsearchable=?',array(DB_PREFIX.'_questions', 'Y') );

$question_details = $osDB->getAll('select * from ! order by questionid, id',array(OPTIONS_TABLE));

$questionid = '';

$seq = 0;

foreach ($question_details as $k=>$option) {
	if ($questionid != $option['questionid']) {
		$questionid = $option['questionid'];
		$seq = 0;
	}
	$seq++;
	$osDB->query('update ! set displayorder = ? where id = ?', array( OPTIONS_TABLE, $seq, $option['id']) );
}
echo("All updates completed...<br /><br />");flush();

?>