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


/* 	This is modified by Vijay Nair
	22nd March 2007

	This rectify the data population issue.

*/
	define( 'IMPORT_MODULE', "webdate" );

	define( 'PAGE_ID', 'admin_mgt' );
	$messages=array();
	if ( !defined( 'SMARTY_DIR' ) ) {
		include_once( '../init.php' );
	}
	include ( 'sessioninc.php' );
	include("import_config.php");

	// Save database and path configuration if coming from the config page
        save_config('');

	function errhndl_import ( $err )
	{	global $t;
		global $_SESSION;
		$message="Could not connect to database. Please enter valid connection settings below.";
		$t->assign("message",$message);
		$t->assign("db",$_SESSION[IMPORT_MODULE]);
		$t->assign('rendered_page', $t->fetch('admin/import_config_webdate.tpl'));
		$t->display ( 'admin/index.tpl' );
		die();
	}
	//PEAR::setErrorHandling( PEAR_ERROR_CALLBACK, 'errhndl_import' );

	$error=false;
	if(empty($_SESSION[IMPORT_MODULE])) $error=true;
	if(empty($_SESSION[IMPORT_MODULE]["db_name"]) ||
	   empty($_SESSION[IMPORT_MODULE]["db_host"]) ||
	   empty($_SESSION[IMPORT_MODULE]["db_user"])) $error=true;
	if(!$error) {
		// Connecting to database
		$dsn2 = 'mysql://' . $_SESSION[IMPORT_MODULE]["db_user"] . ':' . $_SESSION[IMPORT_MODULE]["db_pass"] . '@' . $_SESSION[IMPORT_MODULE]["db_host"] . '/' . $_SESSION[IMPORT_MODULE]["db_name"];
		$db2 = @DB::connect( $dsn2 );
		if (PEAR::isError($db)) {
		    errhndl_import("");
			exit;
		}
		$db2->setFetchMode( DB_FETCHMODE_ASSOC );

	}
	if ($error)
	{	$t->assign("db",isset($_SESSION[IMPORT_MODULE])?$_SESSION[IMPORT_MODULE]:'');
		$t->assign('rendered_page', $t->fetch('admin/import_config_webdate.tpl'));
		$t->display ( 'admin/index.tpl' );
		exit;
	}

//debug($_SESSION[IMPORT_MODULE]);
//debug($db2);

	if($_REQUEST['action']=="section") {
		$query="select * from ".DB_PREFIX."_sections";
		$sections=$osDB->getAll($query);
		$t->assign("sections",$sections);
		$t->assign('rendered_page', $t->fetch('admin/import_section.tpl'));
		$t->display ( 'admin/index.tpl' );
		exit;
	}

	if($_REQUEST['action']=="config" && ! $_POST['db_config']) {

		$t->assign("db",$_SESSION[IMPORT_MODULE]);
		$t->assign('rendered_page', $t->fetch('admin/import_config_webdate.tpl'));
		$t->display ( 'admin/index.tpl' );
	}

	$prefix = $_SESSION[IMPORT_MODULE]["db_prefix"];

	// =================================================================================
	// IMPORTING USERS
	// =================================================================================
	if($_REQUEST['module']=="users") {
		// 1. DELETING PREVIOUS IMPORTS

		$query="select * from ".DB_PREFIX."_imported_users where module='webdate'";
		$result=$osDB->query($query);
		while(($data=$result->fetchRow()))
		{

			delete_user_records($data["user_id"]);
		}
		// 4. Deleting from imported_users
		$query="delete from ".DB_PREFIX."_imported_users where module='webdate'";
		$osDB->query($query);
		$messages[]="Deleting previous imported users... OK";

		if($_REQUEST['action']=="import") {
			// 2. IMPORTING NEW USERS
			// Importing new users
			$gendres=array("Male"=>"M","Female"=>"F","couple"=>"C", "Couple" => 'C');
			$statuses=array(
				  "Unconfirmed"  => "pending",
				  "Approval"     => "pending",
				  "3"            => "active",
				  "Rejected"     => "reject",
				  "Suspended"    => "suspend"
				 );

			$query="
                            SELECT m.*,login AS username,id AS userid
                            FROM ".$prefix."members m
                        ";
			$result2=$db2->query($query);
			$imgerr = 0;
			$imgctr = 0;
			$videoerr = 0;
			$videoctr = 0;
			$blogctr = 0;
			$qctr = 0;
			while(($data=$result2->fetchRow()))
			{
	// 2.1 IMPORTING SIGNUP INFORMATION
				$fields=array();
                $username = uniq_username($data);
				$fields["username"]     = $username;
				$fields["password"]     = md5($data["pswd"]);

                list($first,$last)      = explode(" ", $data["name"]);

				$fields["firstname"]    = $first;
				$fields["lastname"]     = $last;

				$fields["regdate"]      = $data['reg_date'];
				$fields["active"]       = $data['status'];
                $fields["email"]        = $data["email"];
                $fields["gender"]       = $gendres[$data["gender"]];
                $fields["lookgender"]   = $gendres[$data["looking_for"]];
				$countryname = $db2->getOne('select name from '. $prefix.'countries  where id = '.$data['country']);
				$fields["country"] =  $osDB->getOne("select code from ".COUNTRIES_TABLE." where upper(name) LIKE upper('".$countryname."')");

				$pdata = $db2->getRow("
					SELECT * from ".$prefix."profile
					where member_id = '".$data['id']."'");

				/* Get profile data for the user */
				/* Now populate data for this user from other tables and modify them to
					be used in osDate */
			    $fields["state_province"] = $pdata["state"];
			    $fields["city"]           = $pdata["city"];
				if ($pdata['birth_year'] != '' && $pdata['birth_month'] != '' && $pdata['birth_day'] != '') {
				    $fields["birth_date"] = $pdata["birth_year"]. '-'. $pdata["birth_month"].'-'. $pdata["birth_day"];
				} else {
					$fields['birth_date'] = '0000-00-00';
				}
                $fields["lookagestart"]  = $pdata["age_from"];
                $fields["lookageend"]  = $pdata["age_to"];
				$fields["zip"]   = $pdata["zipcode"];
				$fields["lastvisit"] = $pdata["lastlogin"];
				$fields["level"]  = $pdata["status"];
				$fields["status"]=$statuses[$pdata["status"]];
				// Inserting into osdate_user
				$query=insert_query(DB_PREFIX."_user",$fields);
				$result=$osDB->query($query);
				$imported_user_id=$osDB->getOne("select last_insert_id()");

				// If there's profile data to set, set it also
				//
/*				$pdata = $db2->getRow("
					SELECT
					  p.member_id,
					  c.name  AS country,
					  p.state,
					  p.city,
					  p.email,
					  p.name,
					  p.gender,
					  p.birth_day,
					  p.birth_month,
					  p.birth_year,
					  m.name  AS marital_status,
					  p.children,
					  d.name  AS drinking,
					  s.name  AS smoking,
					  f.name  AS food,
					  e.name  AS eye_color,
					  hc.name AS hair_color,
					  h.name  AS height,
					  b.name  AS body_type,
					  r.name  AS race,
					  rl.name AS religion,
					  o.name  AS occupation,
					  ed.name AS education,
					  l1.name AS language,
					  r1.name AS lang_1_rate,
					  l2.name AS lang_2,
					  r2.name AS lang_2_rate,
					  l3.name AS lang_3,
					  r3.name AS lang_3_rate,
					  l4.name AS lang_4,
					  r4.name AS lang_4_rate,
					  p.looking_for,
					  p.age_from,
					  p.age_to,
					  p.general_info,
					  p.appearance_info,
					  p.looking_for_info,
					  p.status,
					  p.finish_status,
					  p.not_newbie,
					  p.lastlogin,
					  p.zipcode,
					  p.longitude,
					  p.latitude,
					  p.photo_pass,
					  p.view_count

					FROM ".$prefix."profile p

					LEFT JOIN ".$prefix."countries c      ON p.country = c.id
					LEFT JOIN ".$prefix."marital_status m ON p.marital_status = m.id
					LEFT JOIN ".$prefix."drinking d       ON p.drinking       = d.id
					LEFT JOIN ".$prefix."smoking s        ON p.smoking        = s.id
					LEFT JOIN ".$prefix."food f           ON p.food           = f.id
					LEFT JOIN ".$prefix."eye_colors e     ON p.eye_color      = e.id
					LEFT JOIN ".$prefix."hair_colors hc   ON p.hair_color     = hc.id
					LEFT JOIN ".$prefix."heights h        ON p.height         = h.id
					LEFT JOIN ".$prefix."body_types b     ON p.body_type      = b.id
					LEFT JOIN ".$prefix."races r          ON p.race           = r.id
					LEFT JOIN ".$prefix."religions rl     ON p.religion       = rl.id
					LEFT JOIN ".$prefix."occupations o    ON p.occupation     = o.id
					LEFT JOIN ".$prefix."educations ed    ON p.education      = ed.id
					LEFT JOIN ".$prefix."languages l1     ON p.lang_1         = l1.id
					LEFT JOIN ".$prefix."lang_rates r1    ON p.lang_1_rate    = r1.id
					LEFT JOIN ".$prefix."languages l2     ON p.lang_2         = l1.id
					LEFT JOIN ".$prefix."lang_rates r2    ON p.lang_2_rate    = r1.id
					LEFT JOIN ".$prefix."languages l3     ON p.lang_3         = l1.id
					LEFT JOIN ".$prefix."lang_rates r3    ON p.lang_3_rate    = r1.id
					LEFT JOIN ".$prefix."languages l4     ON p.lang_4         = l1.id
					LEFT JOIN ".$prefix."lang_rates r4    ON p.lang_4_rate    = r1.id

					WHERE p.member_id = '".$data['id']."'");
*/
				// Inserting into osdate_inserted_users
				$fields=array();
				$fields["source_id"]=$data["id"];
				$fields["user_id"]=$imported_user_id;
				$fields["module"]="webdate";
				$query = insert_query(DB_PREFIX."_imported_users",$fields);
				$osDB->query($query);

	// 2.2 IMPORTING PHOTOS OF USER
    // If there's profile data to set, set it also
    //
                $phdata = $db2->getRow("SELECT * FROM ".$prefix."photos WHERE member_id = '".$data['id']."'");
                if ( isset($phdata['id']) && $phdata['id']) {

					$priv_album_id = false;

                    if ( $phdata['password'] ) {

                        $priv_album_id = add_album($username, 'Private', false, $phdata['password']);
                    }
                    $picno = 0;
                    for( $m = 0; $m <= 20; $m++ ) {

                                        if( ! empty($phdata["filename_".$m]) )
                                        {
                                                // Creating new image
                                                $imgctr++;

                                                // If set to private, create a private album.
                                                //
                                                $album_id = 0;

                                                if ( $phdata["private_".$m] ) {

                                                    $album_id = $priv_album_id;
                                                }
                                                $filename= $_SESSION[IMPORT_MODULE]["photos_url"].'photos/'.$phdata["filename_".$m];

                                                $result = add_image($album_id ,$filename, 'Y');

                                                if ( ! $result ) {

                                                  $imgerr++;
                                                }
                                        }
                                    }
                                }
				// Importing user videos
				//
				$album_id = false;

				$query="SELECT * FROM ".$prefix."videos WHERE member_id = '".$data['id']."'";

                                $v_result = $db2->query($query);

				while(($v_data = $v_result->fetchRow()))
				{

				        // Adds new album if this is the first photo
					$album_id = add_album($username, 'Videos', $album_id);

					// Creating new image
                                        $filename =  $_SESSION[IMPORT_MODULE]["photos_url"].$v_data["filename_1"].'videos/';

                                        $videoctr++;
                                        $result = add_image($album_id,$filename, 'Y');

                                        if ( ! $result ) {

                                          $videoerr++;
                                        }
              			}
				// Importing user questions
				//
					if ( $pdata ) {

						/* Now populate other information */
						$pdata['marita_status'] = $db2->getOne('select name from '.$prefix."marital_status where id = '".$pdata['marital_status']."'");
						$pdata['drinking'] = $db2->getOne('select name from '.
							$prefix."drinking where id = '".$pdata['drinking']."'");
						$pdata['smoking'] = $db2->getOne('select name from '.
							$prefix."smoking where id = '".$pdata['smoking']."'");
						$pdata['food'] = $db2->getOne('select name from '.
							$prefix."food where id = '".$pdata['food']."'");
						$pdata['eye_color'] = $db2->getOne('select name from '.
							$prefix."eye_colors where id = '".$pdata['eye_color']."'");
						$pdata['hair_color'] = $db2->getOne('select name from '.
							$prefix."hair_colors where id = '".$pdata['hair_color']."'");
						$pdata['height'] = $db2->getOne('select name from '.
							$prefix."heights where id = '".$pdata['height']."'");
						$pdata['body_type'] = $db2->getOne('select name from '.
							$prefix."body_types where id = '".$pdata['body_type']."'");
						$pdata['race'] = $db2->getOne('select name from '.
							$prefix."races where id = '".$pdata['race']."'");
						$pdata['religion'] = $db2->getOne('select name from '.
							$prefix."religions where id = '".$pdata['religion']."'");
						$pdata['occupation'] = $db2->getOne('select name from '.
							$prefix."occupations where id = '".$pdata['occupation']."'");
						$pdata['education'] = $db2->getOne('select name from '.
							$prefix."educations where id = '".$pdata['education']."'");

						foreach ( $pdata AS $field_name => $field_value )
						{
							$text = $q_text['question_text'];
							$q_data = array(
								'question_text' => $field_name,
								'answer_text' => $field_value,
							);
							if ( add_question($q_data) ) {

								$qctr++;
							}
						}
					}
			}
			$messages[]="Importing signup information... OK";
                        if ( ! $imgerr ) {

			   $messages[]="Importing users photos... OK";
                        }
                        elseif ( $imgerr == $imgctr && $imgerr > 0) {

			   $messages[]="Importing users photos... ALL FAIL";
                        }
                        else  {

			   $messages[]="Importing users photos... FAIL: " . $imgerr . " OK: " . ($imgctr - $imgerr);
                        }
                        if ( ! $videoerr ) {

			   $messages[]="Importing users videos... OK: " . $videoctr;
                        }
                        elseif ( $videoerr == $videoctr && $videoerr > 0) {

			   $messages[]="Importing users videos... ALL FAIL";
                        }
                        else  {

			   $messages[]="Importing users videos... FAIL: " . $videoerr . " OK: " . ($videoctr - $videoerr);
                        }
			$messages[]="Importing blogs... OK: " . $blogctr;
			if ( $qctr ) {

			   $messages[]="Importing prefernces... OK: " . $qctr;
			}
			else {

			   $messages[]="Importing prefernces... OK: 0";
			}
			// Now import things that need the user to already exists
			//
			$query="select * from ".IMPORTED_USERS." where module='webdate'";
                        $result=$osDB->query($query);
			$budctr = 0;
			$winkctr = 0;
			$mailctr = 0;

                        while(($data=$result->fetchRow()))
                        {
				// Importing hotlist budies
				//
				$query="SELECT owner_member_id AS userid, member_id AS ref_userid  FROM ".$prefix."hot_lists WHERE owner_member_id = '".$data["source_id"]."' and member_id > 0";

				$b_result=$db2->query($query);
				while(($b_data=$b_result->fetchRow()))
				{

				    $type = "H";
				    if ( add_buddy($b_data,$type) ) {

				        $budctr++;
				    }
				}
				// Importing ban budies
				//
				$query="SELECT member_id AS userid, blocked_id AS ref_userid  FROM ".$prefix."blocked WHERE member_id = '".$data["source_id"]."' and blocked_id > 0";

				$b_result=$db2->query($query);

				while(($b_data=$b_result->fetchRow()))
				{

				    $type = "B";
				    if ( add_buddy($b_data,$type) ) {

				        $budctr++;
				    }
				}
				// Importing friend budies
				//
				$query="SELECT owner_id AS userid, profile_id AS ref_userid  FROM ".$prefix."favourites WHERE owner_id = '".$data["source_id"]."' and profile_id > 0";

				$b_result=$db2->query($query);

				while(($b_data=$b_result->fetchRow()))
				{

				    $type = "F";
				    if ( add_buddy($b_data,$type) ) {

				        $budctr++;
				    }
				}
 				// Importing mailbox messages
				//
				$query="SELECT
                                    rid AS  touserid,
                                    sid AS  fromuserid,
                                    subject AS subject,
                                    message AS message,
                                    timesent AS sendtime,
                                    is_read  AS flagread,
                                    IF(show_in_inbox='0',1,0)  AS todeleted,
                                    IF(show_in_sent='0',1,0) AS fromdeleted

				FROM ".$prefix."messages WHERE rid = '".$data["source_id"]."'";

				$m_result=$db2->query($query);

				while(($m_data=$m_result->fetchRow()))
				{

				    if ( add_message($m_data) ) {

				        $mailctr++;
				    }
				}
                        }
		        $messages[]="Importing buddies... OK: " . $budctr;
		        $messages[]="Importing winks... OK: " . $winkctr;
		        $messages[]="Importing mail... OK: " . $mailctr;
		} // $_REQUEST['action']=="import"
	}



	// Calculating statistics
	$imported=array();
	$imported["users"]=$osDB->getOne("select count(*) from ".DB_PREFIX."_imported_users where module='webdate' ");
	$t->assign("imported",$imported);

	$t->assign("messages",$messages);
	$t->assign('rendered_page', $t->fetch('admin/import_webdate.tpl'));
	$t->display ( 'admin/index.tpl' );
?>