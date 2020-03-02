P<?php
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

/*
	This is the configuration file for Aedating convertion process.
	This will convert following data only

	Vijay Nair
*/


/* Define Aedate db access details */
$aedate_DB_NAME = 'aedate';
$aedate_DB_HOST = 'localhost';
$aedate_DB_USER = 'root';
$aedate_DB_PASS = '';

/* Define full path of the directory where profile images are stored. IT can be http address */
$aedate_USER_PICS_DIR = 'id_img';

/* Define table names of aedating DB */
$profiles_table = 'Profiles';
$blocklist_table = 'BlockList';
$friendlist_table = 'FriendList';
$hotlist_table = 'HotList';
$messages_table = 'Messages';

/*
	Define aedating countries list to get name.
	Using country name, we can get osDate country codefrom countries table array derived from DB

*/

$aedate_countries[0] = "Afghanistan";
$aedate_countries[1] = "Albania";
$aedate_countries[2] = "Algeria";
$aedate_countries[3] = "American Samoa";
$aedate_countries[4] = "Andorra";
$aedate_countries[5] = "Angola";
$aedate_countries[6] = "Anguilla";
$aedate_countries[7] = "Antarctica";
$aedate_countries[8] = "Antigua and Barbuda";
$aedate_countries[9] = "Argentina";
$aedate_countries[10] = "Armenia";
$aedate_countries[11] = "Aruba";
$aedate_countries[12] = "Australia";
$aedate_countries[13] = "Austria";
$aedate_countries[14] = "Azerbaijan";
$aedate_countries[15] = "Bahamas";
$aedate_countries[16] = "Bahrain";
$aedate_countries[17] = "Bangladesh";
$aedate_countries[18] = "Barbados";
$aedate_countries[19] = "Belarus";
$aedate_countries[20] = "Belgium";
$aedate_countries[21] = "Belize";
$aedate_countries[22] = "Benin";
$aedate_countries[23] = "Bermuda";
$aedate_countries[24] = "Bhutan";
$aedate_countries[25] = "Bolivia";
$aedate_countries[26] = "Bosnia/Herzegowina";
$aedate_countries[27] = "Botswana";
$aedate_countries[28] = "Bouvet Island";
$aedate_countries[29] = "Brazil";
$aedate_countries[30] = "British Ind. Ocean";
$aedate_countries[31] = "Brunei Darussalam";
$aedate_countries[32] = "Bulgaria";
$aedate_countries[33] = "Burkina Faso";
$aedate_countries[34] = "Burundi";
$aedate_countries[35] = "Cambodia";
$aedate_countries[36] = "Cameroon";
$aedate_countries[37] = "Canada";
$aedate_countries[38] = "Cape Verde";
$aedate_countries[39] = "Cayman Islands";
$aedate_countries[40] = "Central African Rep.";
$aedate_countries[41] = "Chad";
$aedate_countries[42] = "Chile";
$aedate_countries[43] = "China";
$aedate_countries[44] = "Christmas Island";
$aedate_countries[45] = "Cocoa (Keeling) Is.";
$aedate_countries[46] = "Colombia";
$aedate_countries[47] = "Comoros";
$aedate_countries[48] = "Congo";
$aedate_countries[49] = "Cook Islands";
$aedate_countries[50] = "Costa Rica";
$aedate_countries[51] = "Cote Divoire";
$aedate_countries[52] = "Croatia";
$aedate_countries[53] = "Cuba";
$aedate_countries[54] = "Cyprus";
$aedate_countries[55] = "Czech Republic";
$aedate_countries[56] = "Denmark";
$aedate_countries[57] = "Djibouti";
$aedate_countries[58] = "Dominica";
$aedate_countries[59] = "Dominican Republic";
$aedate_countries[60] = "East Timor";
$aedate_countries[61] = "Ecuador";
$aedate_countries[62] = "Egypt";
$aedate_countries[63] = "El Salvador";
$aedate_countries[64] = "Equatorial Guinea";
$aedate_countries[65] = "Eritrea";
$aedate_countries[66] = "Estonia";
$aedate_countries[67] = "Ethiopia";
$aedate_countries[68] = "Falkland Islands";
$aedate_countries[69] = "Faroe Islands";
$aedate_countries[70] = "Fiji";
$aedate_countries[71] = "Finland";
$aedate_countries[72] = "France";
$aedate_countries[73] = "Gabon";
$aedate_countries[74] = "Gambia";
$aedate_countries[75] = "Georgia";
$aedate_countries[76] = "Germany";
$aedate_countries[77] = "Ghana";
$aedate_countries[78] = "Gibraltar";
$aedate_countries[79] = "Greece";
$aedate_countries[80] = "Greenland";
$aedate_countries[81] = "Grenada";
$aedate_countries[82] = "Guadeloupe";
$aedate_countries[83] = "Guam";
$aedate_countries[84] = "Guatemala";
$aedate_countries[85] = "Guinea";
$aedate_countries[86] = "Guinea-Bissau";
$aedate_countries[87] = "Guyana";
$aedate_countries[88] = "Haiti";
$aedate_countries[89] = "Honduras";
$aedate_countries[90] = "Hong Kong";
$aedate_countries[91] = "Hungary";
$aedate_countries[92] = "Iceland";
$aedate_countries[93] = "India";
$aedate_countries[94] = "Indonesia";
$aedate_countries[95] = "Iran";
$aedate_countries[96] = "Iraq";
$aedate_countries[97] = "Ireland";
$aedate_countries[98] = "Israel";
$aedate_countries[99] = "Italy";
$aedate_countries[100] = "Jamaica";
$aedate_countries[101] = "Japan";
$aedate_countries[102] = "Jordan";
$aedate_countries[103] = "Kazakhstan";
$aedate_countries[104] = "Kenya";
$aedate_countries[105] = "Kiribati";
$aedate_countries[106] = "Korea";
$aedate_countries[107] = "Kuwait";
$aedate_countries[108] = "Kyrgyzstan";
$aedate_countries[109] = "Lao";
$aedate_countries[110] = "Latvia";
$aedate_countries[111] = "Lebanon";
$aedate_countries[112] = "Lesotho";
$aedate_countries[113] = "Liberia";
$aedate_countries[114] = "Liechtenstein";
$aedate_countries[115] = "Lithuania";
$aedate_countries[116] = "Luxembourg";
$aedate_countries[117] = "Macau";
$aedate_countries[118] = "Macedonia";
$aedate_countries[119] = "Madagascar";
$aedate_countries[120] = "Malawi";
$aedate_countries[121] = "Malaysia";
$aedate_countries[122] = "Maldives";
$aedate_countries[123] = "Mali";
$aedate_countries[124] = "Malta";
$aedate_countries[125] = "Marshall Islands";
$aedate_countries[126] = "Martinique";
$aedate_countries[127] = "Mauritania";
$aedate_countries[128] = "Mauritius";
$aedate_countries[129] = "Mayotte";
$aedate_countries[130] = "Mexico";
$aedate_countries[131] = "Micronesia";
$aedate_countries[132] = "Moldova";
$aedate_countries[133] = "Monaco";
$aedate_countries[134] = "Mongolia";
$aedate_countries[135] = "Montserrat";
$aedate_countries[136] = "Morocco";
$aedate_countries[137] = "Mozambique";
$aedate_countries[138] = "Myanmar";
$aedate_countries[139] = "Namibia";
$aedate_countries[140] = "Nauru";
$aedate_countries[141] = "Nepal";
$aedate_countries[142] = "Netherlands";
$aedate_countries[143] = "New Caledonia";
$aedate_countries[144] = "New Zealand";
$aedate_countries[145] = "Nicaragua";
$aedate_countries[146] = "Niger";
$aedate_countries[147] = "Nigeria";
$aedate_countries[148] = "Niue";
$aedate_countries[149] = "Norfolk Island";
$aedate_countries[150] = "Norway";
$aedate_countries[151] = "Oman";
$aedate_countries[152] = "Pakistan";
$aedate_countries[153] = "Palau";
$aedate_countries[154] = "Panama";
$aedate_countries[155] = "Papua New Guinea";
$aedate_countries[156] = "Paraguay";
$aedate_countries[157] = "Peru";
$aedate_countries[158] = "Philippines";
$aedate_countries[159] = "Pitcairn";
$aedate_countries[160] = "Poland";
$aedate_countries[161] = "Portugal";
$aedate_countries[162] = "Puerto Rico";
$aedate_countries[163] = "Qatar";
$aedate_countries[164] = "Reunion";
$aedate_countries[165] = "Romania";
$aedate_countries[166] = "Russia";
$aedate_countries[167] = "Rwanda";
$aedate_countries[168] = "Saint Lucia";
$aedate_countries[169] = "Samoa";
$aedate_countries[170] = "San Marino";
$aedate_countries[171] = "Saudi Arabia";
$aedate_countries[172] = "Senegal";
$aedate_countries[173] = "Seychelles";
$aedate_countries[174] = "Sierra Leone";
$aedate_countries[175] = "Singapore";
$aedate_countries[176] = "Slovakia";
$aedate_countries[177] = "Solomon Islands";
$aedate_countries[178] = "Somalia";
$aedate_countries[179] = "South Africa";
$aedate_countries[180] = "Spain";
$aedate_countries[181] = "Sri Lanka";
$aedate_countries[182] = "St. Helena";
$aedate_countries[183] = "Sudan";
$aedate_countries[184] = "Suriname";
$aedate_countries[185] = "Swaziland";
$aedate_countries[186] = "Sweden";
$aedate_countries[187] = "Switzerland";
$aedate_countries[188] = "Syria";
$aedate_countries[189] = "Taiwan";
$aedate_countries[190] = "Tajikistan";
$aedate_countries[191] = "Tanzania";
$aedate_countries[192] = "Thailand";
$aedate_countries[193] = "Togo";
$aedate_countries[194] = "Tokelau";
$aedate_countries[195] = "Tonga";
$aedate_countries[196] = "Trinidad and Tobago";
$aedate_countries[197] = "Tunisia";
$aedate_countries[198] = "Turkey";
$aedate_countries[199] = "Turkmenistan";
$aedate_countries[200] = "Tuvalu";
$aedate_countries[201] = "Uganda";
$aedate_countries[202] = "Ukraine";
$aedate_countries[203] = "United Arab Emirates";
$aedate_countries[204] = "United Kingdom";
$aedate_countries[205] = "United States";
$aedate_countries[206] = "Uruguay";
$aedate_countries[207] = "Uzbekistan";
$aedate_countries[208] = "Vanuatu";
$aedate_countries[209] = "Vatican";
$aedate_countries[210] = "Venezuela";
$aedate_countries[211] = "Viet Nam";
$aedate_countries[212] = "Virgin Islands";
$aedate_countries[213] = "Western Sahara";
$aedate_countries[214] = "Yeman";
$aedate_countries[215] = "Yugoslavia";
$aedate_countries[216] = "Zaire";
$aedate_countries[217] = "Zambia";
$aedate_countries[218] = "Zimbabwe";


/* Create respective membership levels of Aedating in osDate using admin Membership Management option. Please DO NOT remove or rename 'Visitor' level i.e. 4.

Note down these corresponding levels and define it here in the format
$memlevel[aedatingid] = osDate Membership level ID;

e.g.
$memlevel['1'] = '6';
$memlevel['3'] = '7';

*/
$memlevel['4'] = '1';
$memlevel['3'] = '2';
$memlevel['1'] = '3';

/* define genders and lookfor genders array */
$genders=array(
	"male"	=>	"M",
	"female"=>	"F",
	"couple"=>	"C"
	);
$lookgenders=array(
	"male"	=>	"M",
	"female"=>"F",
	"couple"=>"C",
	"both"	=>"B"
	);

/* Now define status codes of aedating for osDate equivalent */
$status=array(
	"Unconfirmed"	=>	"pending",
	"Approval"		=>	"pending",
	"Active"		=>	"active",
	"Rejected"		=>	"rejected",
	"Suspended"		=>	"suspended"
	);

$imp_hdr = array(
	'1'	=>	'Now Importing User Data',
	'2'	=>	'Now importing User Block List data',
	'3'	=>	'Now importing User Friends List data',
	'4'	=>	'Now importing User Hot List data',
	'5'	=>	'Now importing User Messages data',
	);
?>
