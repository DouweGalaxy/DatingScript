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

include(LIB_DIR . 'plugin_class.php');

$plugin =& new Plugin();


// Edit the preferences if save button pressed
//
if (isset( $_POST['action']) &&  $_POST['action'] == 'edit_plugin' ) {

      $plugin->editPlugin($_POST['name']);

      if ( $plugin->getErrorMessage() ) {

          $t->assign ( 'error_message', $plugin->getErrorMessage() );
          $data = $_POST;
      }
      else {

          header( 'location: pluginlist.php' );
          exit;
      }
} else {
// Get the plugin info if just clicked a edit link

    $data = $plugin->getPlugin($_REQUEST['name']);

}

$name = $_REQUEST['name'];

include_once(PLUGIN_DIR . $name . '/libs/'. $name . '.php');

$pluginobject =& new $name();

$data['display_name']=$pluginobject->display_name;

$t->assign( 'data',  $data);

$t->assign( 'access',  $plugin->getPluginAccess($data['id']));
$t->assign( 'pluginconfig',  $plugin->getPluginConfig($data['id']));

unset($data);

// Make the page
//
$t->assign('rendered_page', $t->fetch('admin/editplugin.tpl') );

$t->display( 'admin/index.tpl' );

exit;

?>