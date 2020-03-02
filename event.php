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

/* This is just a redirection program to add new event in admin.
	VIjay Nair

*/

if (isset($_GET['event_id']) && $_GET['event_id'] != ''){
	header("location: calendarevents.php?calendarid=".$_GET['calendarid']."&edit=".$_GET['event_id']);
} else {
	header("location: calendarevents.php?calendarid=".$_GET['calendarid']."&insert=event&timestamp=".$_GET['timestamp']);
}
exit;

?>