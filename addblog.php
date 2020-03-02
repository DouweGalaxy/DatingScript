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

// If the preferences are missing, go to the settings page
//
if ( ! $blog->settingsExist($_SESSION['AdminId']) ) {

      header( 'location: blogsettings.php?error_name=nosetup' );
      exit;
}

$blog->loadSettings($_SESSION['AdminId']);

// Load template if load template button pushed
//
if ( isset($_POST['action']) && $_POST['action'] == 'add_blog' && isset($_POST['load_template']) ) {

     $loadtemp = $blog->loadTemplate();

} elseif ( isset($_POST['action']) && $_POST['action'] == 'add_blog' ) {
// Add Blog if save button pressed
//

      $blog->addBlog($_SESSION['AdminId']);

      if ( $blog->getErrorMessage() ) {

          $t->assign ( 'error_message', $blog->getErrorMessage() );
      } else {

          header( 'location: bloglist.php' );
          exit;
      }
}

// Set the values to show on the page
//
$data = $blog->getData();

// If there's a saved template, give the oportunity to use it
//
$t->assign( 'date_posted', $data['date_posted'] ) ;

if (isset($loadtemp)) {
	$t->assign( 'loadtemp',  $loadtemp);
}
$t->assign( 'data',  $data);
unset($data);

$t->assign( 'docroot', DOC_ROOT );
$js = '<script type="text/javascript" src="'.DOC_ROOT.'javascript/calendar/epoch_classes.js"></script>';

$css = '<link rel="stylesheet" type="text/css" href="' . DOC_ROOT . 'javascript/calendar/epoch_styles.css" />';

$t->assign('addtional_javascript', $js );
$t->assign('addtional_css', $css );

$t->assign('gui_editor', $blog->settings['gui_editor']);


// Make the page
//

$t->assign('rendered_page', $t->fetch('admin/addblog.tpl') );

$t->display( 'admin/index.tpl' );

exit;

?>
