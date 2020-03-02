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

include( 'sessioninc.php' );

include_once(LIB_DIR . 'blog_class.php');

$blog =& new Blog(true);

// If we were bounced here with a error message, save it
//
if ( isset($_GET['error_name']) ) {

      $t->assign ( 'error_message', get_lang('blog_errors', $_GET['error_name']) );
}

// Edit the preferences if save button pressed
//
if (isset($_POST['action']) &&  $_POST['action'] == 'edit_pref' ) {

      $blog->saveSettings($_SESSION['AdminId']);


      if ( $blog->getErrorMessage() ) {

          $t->assign ( 'error_message', $blog->getErrorMessage() );

          $row = $blog->getSettings();
      }
      else {

		if ($blog->getStoryCount($_SESSION['UserId']) <= 0) {
			header( 'location: addblog.php' );
		} else {
         // After saving, go to blog list page
         header( 'location: bloglist.php' );
		}
         exit;
      }

} else {
// Display current info
//
   $blog->loadSettings($_SESSION['AdminId']);
   $blog->prepSettings();
   // Strip slashes

   $row = $blog->getSettings();
}

// If user turned off the gui editor, display the normal text box
//

$t->assign('gui_editor', $blog->settings['gui_editor']);

$t->assign( 'row', $row );
$t->assign( 'blog_description_form', html_entity_decode($row['description']) );
unset($row);

// Make the page
//
$t->assign('rendered_page', $t->fetch('admin/blogsettings.tpl') );

$t->display( 'admin/index.tpl' );

exit;

?>
