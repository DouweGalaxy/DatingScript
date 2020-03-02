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

include(JSCRIPT_DIR . "FCKeditor/fckeditor.php") ;

include(LIB_DIR . 'poll_class.php');

$poll =& new poll();


// Edit the preferences if save button pressed
//
if ( $_POST['action'] == 'edit_poll' ) {

      $poll->editPoll($_POST['id']);

      if ( $poll->getErrorMessage() ) {

          $t->assign ( 'error_message', $poll->getErrorMessage() );
      }  else {

          header( 'location: polllist.php' );
          exit;
      }
} else {
// Get the poll info if just clicked a edit link

    $poll->loadPoll($_REQUEST['id']);
    $poll->prepPoll();
}


// Set the values to show on the page
//
$question = $poll->getQuestion();

$t->assign( 'questionid', $question['id'] ) ;

$t->assign( 'question',    $question);

$options = $poll->getOption();

$t->assign( 'option',  $options );

unset($options);

// Make the page
//
$t->assign('rendered_page', $t->fetch('editpoll.tpl') );

$t->display( 'index.tpl' );

exit;

?>
