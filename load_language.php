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

if (isset($_REQUEST['langname'])) {
	if (isset($_REQUEST['loadlang']) && $_REQUEST['loadlang'] != '') {
		$file = LANG_DIR.$language_files[$_REQUEST['langname']];

		$lang = array();

		include $file;

		$file=str_replace('\\','/',$file);

		$osDB->query('delete from ! where lang = ?', array( LANGUAGE_TABLE, strtolower($_REQUEST['langname'])) );

		$sql = 'insert into ! (lang, mainkey, subkey, descr) values (?, ?, ?, ?)';
		foreach ($lang as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $subkey => $descr) {
					$osDB->query($sql, array(LANGUAGE_TABLE, $_REQUEST['langname'], $key, $subkey, htmlspecialchars($descr)));
				}
			} else {
				$osDB->query($sql, array(LANGUAGE_TABLE, $_REQUEST['langname'], $key, "", htmlspecialchars($val)));
			}
		}

		/* Now delete all files for this language from templates_c directory */
		$dir = opendir(TEMPLATE_C_DIR);
		while ($fl = readdir($dir)) {
			if ($fl == '.' || $fl == '..' || $fl == 'index.html') {
				continue;
			} else {
				if (strpos($fl,$_REQUEST['langname']) == 0 ) {
					@unlink(TEMPLATE_C_DIR.$fl);
				}
			}
		}
		closedir($dir);
		$m_msg = str_replace('#LANGUAGE#', strtoupper($_REQUEST['langname']), get_lang('langfile_loaded'));

		$t->assign('msg', $m_msg);
		$t->assign('msg_file',$file);
		/* optimize language table for performance */
		$osDB->query('optimize table '.LANGUAGE_TABLE);

	} elseif (isset($_REQUEST['deletelang']) && $_REQUEST['deletelang'] != '') {
		/* Delete language from DB */

		$osDB->query('delete from ! where lang = ?', array(LANGUAGE_TABLE, $_REQUEST['langname']) );

		/* Now delete all files for this language from templates_c directory */
		$dir = opendir(TEMPLATE_C_DIR);
		while ($fl = readdir($dir)) {
			if ($fl == '.' || $fl == '..' || $fl == 'index.html') {
				continue;
			} else {
				if (strpos($fl,$_REQUEST['langname']) == 0 ) {
					unlink(TEMPLATE_C_DIR.$fl);
				}
			}
		}
		closedir($dir);

		$t->assign('msg', str_replace('#LANGUAGE#', strtoupper($_REQUEST['langname']), get_lang('lang_deleted')));

		/* optimize language table for performance */
		$osDB->query('optimize table '.LANGUAGE_TABLE);
	} elseif (isset($_REQUEST['genlangfile']) && $_REQUEST['genlangfile'] != '') {
		/* Create language file using definitions available in DB. */
		$temp_langdir = TEMP_DIR.'lang_'.$_REQUEST['langname'].'/';
		if (!file_exists($temp_langdir)) mkdir($temp_langdir,777);
		$file = $temp_langdir.'lang_main.php';
		$fp = @fopen($file,'wb');
		fwrite($fp,'<?php'.chr(13).chr(10));

		if (isset($_REQUEST['langname']) && $_REQUEST['langname'] != '') {
			$lang = createLanguageDefs($_REQUEST['langname']);
		} else {
			$lang = createLanguageDefs('english');
		}
		/* Now we have $lang array which contain english definitions and language specific definitions combined.
			This will give a good idea for which language specific definitions are not given  */

		foreach ($lang as $key => $val) {
			if (count($val) > 1 ) {
				fwrite($fp,"\$lang['".$key."'] = array(".chr(13).chr(10));
				foreach($val as $k => $v) {
					fwrite($fp,"'".$k."' => '".$v."',".chr(13).chr(10));
				}
				fwrite($fp,"        );".chr(13).chr(10));
			} else {
				foreach($val as $k=>$v) {
					fwrite($fp,"\$lang['".$key."'] = '".$v."';".chr(13).chr(10));
				}
			}
		}
		fwrite($fp,'?>');
		fclose($fp);

		unset($lang, $eng_defs, $lang_defs);
		$t->assign('msg', str_replace('#LANGUAGE#', strtoupper($_REQUEST['langname']), get_lang('langfile_generated')));
		$t->assign('msg_file',$file);
	} elseif (isset($_REQUEST['vieweditlang']) && $_REQUEST['vieweditlang'] != '') {
		/* View/Edit language file contents */
		$psize = getPageSize();

		$t->assign ( 'psize',  $psize );

		$cpage = isset($_REQUEST['page'])?$_REQUEST['page']:'1';

	    if( $cpage == '' ) $cpage = 1;

		$start = ( $cpage - 1 ) * $psize;

		$t->assign ( 'start', $start );

		$srch='';

		if ($_REQUEST['vieweditlang'] == get_lang('save') ) {
			/* This is the saving of edited text */
		/* Check if this entry is available in requested language definition. May be this is just added from English and changed now. */
			if (!isset($_REQUEST['subkey'])) $_REQUEST['subkey'] = '';

			$id = $osDB->getOne('select id from ! where mainkey=? and subkey=? and lang = ?', array(LANGUAGE_TABLE, $_REQUEST['mainkey'], $_REQUEST['subkey'], $_REQUEST['langname']));

			if (isset($id) && $id > 0) {
				/* This definition is available. THen modify the description only */

				$osDB->query('update ! set descr = ? where id=?', array(LANGUAGE_TABLE, utf8_encode(htmlspecialchars($_REQUEST['descr'])), $id) ) ;
			} else {
				/* oops. This is a new definition for this language */
				$osDB->query('insert into ! (mainkey, subkey, lang, descr) values (?,?,?,?)', array(LANGUAGE_TABLE, $_REQUEST['mainkey'], $_REQUEST['subkey'], $_REQUEST['langname'], utf8_encode(htmlspecialchars($_REQUEST['descr'] )) ) );
			}

			$t->assign('error_message', get_lang('langdefmodified') );

		} elseif ($_REQUEST['vieweditlang'] == 'delete') {
			/* Delete the definition */

			if (isset($_REQUEST['subkey']) && $_REQUEST['subkey'] != '') {
				$osDB->query('delete from ! where lang=? and mainkey=? and subkey=?', array(LANGUAGE_TABLE, $_REQUEST['langname'], $_REQUEST['mainkey'], $_REQUEST['subkey']) );
			} else {
				$osDB->query('delete from ! where lang=? and mainkey=? and (subkey is null or subkey = ?)', array(LANGUAGE_TABLE, $_REQUEST['langname'], $_REQUEST['mainkey'], $_REQUEST['subkey']) );
			}
			$t->assign('error_message', get_lang('langdefdeleted') ) ;

		} elseif($_REQUEST['vieweditlang'] == get_lang('search') && (isset($_REQUEST['srch']) && $_REQUEST['srch'] != '') ) {
			$srch = $_REQUEST['srch'];
		}
		/* NOw convert this array into a record format */

		$langloaded = $osDB->getOne('select count(id) from ! where lang=?', array(LANGUAGE_TABLE, $_REQUEST['langname']) ) ;

		if (!isset($langloaded) || $langloaded <= 0) {
			$t->assign('langnotloaded_descr', str_replace('#SEL_LANG#', $language_options[$_REQUEST['langname']],get_lang('lang_not_loaded_descr')) );
		}

		$langdefs = createLanguageDefs($_REQUEST['langname'], $srch);

		$lang_records=array();
		$rec=array();
		foreach ($langdefs as $mkey => $subrec) {
			$rec['mainkey'] = $mkey;
			foreach ($subrec as $skey => $descr) {
				$rec['subkey'] = $skey;
				$rec['descr'] = $descr;
				$lang_records[]=$rec;
			}
		}

		$rcount = count($lang_records);

		$t->assign( 'totalrecs', $rcount) ;

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

		$t->assign('langdefs', array_slice($lang_records,$start,$psize,true) );

		$t->assign('LANG_ENCODING', $language_conversion[$_REQUEST['langname']]);

		unset($langdefs, $lang_records);

		$t->assign('lang', $lang);
		if (isset($srch) && $srch != '') {
			$t->assign('srch', $srch);
		}

		$t->assign('langname', $_REQUEST['langname']);

		$t->assign('language_options',$language_options);

		$t->assign('rendered_page', $t->fetch('admin/lang_viewedit.tpl'));

		$t->display('admin/index.tpl');

		exit();
	}
}

$t->assign('langname', (isset($_REQUEST['langname'])?$_REQUEST['langname']:''));

$t->assign('language_options',$language_options);

$t->assign('language_files', $language_files);

$t->assign('lang_dir', LANG_DIR);

$t->assign('rendered_page', $t->fetch('admin/load_language.tpl'));

$t->display('admin/index.tpl');

function createLanguageDefs($language, $srch='') {

	global $osDB;

	if (isset($srch) && $srch != '') {
		$eng_defs = $osDB->getAll('select * from ! where lang = ? and lower(mainkey) like ? order by mainkey, subkey',array(LANGUAGE_TABLE,'english','%'.$srch.'%'));
	} else {
		$eng_defs = $osDB->getAll('select * from ! where lang = ? order by mainkey, subkey ',array(LANGUAGE_TABLE,'english'));
	}
	$lang = array();

	/* Add english definitions to the language array */
	foreach ($eng_defs as $row) {
		$lang[$row['mainkey']][$row['subkey']] = html_entity_decode(addslashes($row['descr']));
	}

	if ($language != 'english') {
		if (isset($srch) &&  $srch != '') {
			$lang_defs = $osDB->getAll('select * from ! where lang = ? and lower(mainkey) like ? order by mainkey, subkey',array(LANGUAGE_TABLE, $language,'%'.$srch.'%'));
		} else {
			$lang_defs = $osDB->getAll('select * from ! where lang = ? order by mainkey, subkey ',array(LANGUAGE_TABLE, $language));
		}
		/* Now add language specific definitions to the English array by replacing the values already
		defined */
		foreach ($lang_defs as $row) {
			$lang[$row['mainkey']][$row['subkey']] = html_entity_decode(addslashes($row['descr']));
		}
	}
	return $lang;
}
?>
