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
	include_once( 'init.php' );
}

include( 'sessioninc.php' );

include_once(LIB_DIR . 'blog_class.php');

$blog =& new Blog();

// If the preferences are missing, go to the settings page
//
if ( ! $blog->settingsExist($_SESSION['UserId']) ) {

      header( 'location: blogsettings.php?error_name=nosetup' );
      exit;
}

// Edit the preferences if save button pressed
//
if ( isset($_POST['action']) && $_POST['action'] == 'edit_blog' ) {

      $blog->editBlog($_POST['id']);

      if ( $blog->getErrorMessage() ) {

          $t->assign ( 'error_message', $blog->getErrorMessage() );
      } else {

          header( 'location: bloglist.php' );
          exit;
      }
} else {
// Get the blog info if just clicked a edit link

    $blog->loadBlog($_REQUEST['id']);
    $blog->prepData();
}


// If user turned off the gui editor, display the normal text box
//

$data = $blog->getData();

if ($data['userid'] > 0) {
	$blog->loadSettings($data['userid']);
} else {
	$blog->loadSettings($data['adminid']);
}

$t->assign('gui_editor', $blog->settings['gui_editor']);
// Set the values to show on the page
//
$data = $blog->getData();

$t->assign( 'blog_id', $data['id'] ) ;

$t->assign( 'data',  $data);

$t->assign( 'date_posted', date('Y-m-d',$data['date_posted'] )) ;

// Put the javascript and ccs into the head of the document
//

$js = '<script type="text/javascript" src="' . DOC_ROOT . 'javascript/calendar/epoch_classes.js"></script>';

$css = '<link rel="stylesheet" type="text/css" href="' . DOC_ROOT . 'javascript/calendar/epoch_styles.css" />';

$t->assign('addtional_javascript', $js);
$t->assign('addtional_css', $css);

// Make the page
//

unset( $data);

$t->assign('rendered_page', $t->fetch('editblog.tpl') );

$t->display( 'index.tpl' );

exit;

?>
