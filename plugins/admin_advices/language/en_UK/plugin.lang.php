<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software, you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY, without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program, if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

// --------- Starting below: New or revised $lang ---- from Butterfly (1.8)
$lang['An_advice_about'] = 'A new advice about';
$lang['contribute'] = 'how you can contribute'; 
$lang['navigation'] = 'navigation';
$lang['Metadata'] = 'Metadata';
$lang['current'] =  'Current value: %s.';
$lang['TN-height&width'] = 'Thumbnail height and width have to be equal.';
$lang['Adv_case'][0] = array( /* **contribute */
 'If you want to contribute with your own "tip", please publish it',
 'on Piwigo Forums (or mail it to the one of the developers),', 
 'and we would be happy to add it within next release.', );
$lang['Adv_case'][1] = array( /* newcat_default_status */
 'Try $conf[\'newcat_default_status\'] = \'private\',',
 'You will have more time to describe and check your pictures.',
 'Time to decide between private and public status.',
 'If you choose private, time to distribute authorization.',
 'Your new category will be well prepared.', );
$lang['Adv_case'][2] = array( /* slideshow_period */
 'This value could be too small for low band connections.',
 'Think about higher value like 4.', );
$lang['Adv_case'][3] = array( /* file_ext */
 'Should never contains extensions which can be executed',
 'on the server side like *.php, *.PHP, *.asp, ...', );
$lang['Adv_case'][4] = array( /* show_iptc_mapping */
 'Show IPTC Data from your picture:',
 ' 1 - Copy one of your jpg pictures (a public one) in ./tools/',
 ' 2 - Rename it as sample.jpg.',
 ' 3 - Run ./tools/metadata.php',
 ' 4 - Analyse results to determine which IPTC fields could be useful for your visitors.',
 'Beginners would prefer to keep $conf[\'show_iptc\'] = false,',
 'Advanced users would take care of $lang values and impacts on templates.', );
$lang['Adv_case'][5] = array( /* top_number */
 'This value is maybe too high for low connections, think about 25-50 depending on your thumbnail sizes.', );
$lang['Adv_case'][6] = array( /* top_number */
 'One? It could be too low for random pictures, think about 5-10 depending on your thumbnail sizes.',  );
$lang['Adv_case'][7] = array( /* anti-flood_time */
 'For normal flow processing, your value is probably too high. Reasonable value is 60 (default).' , );
$lang['Adv_case'][8] = array( /* calendar_datefield */
 'Authorized values are ' . "'date_creation' or 'date_available'" . ', otherwise you can get unpredictable results.' , );
$lang['Adv_case'][9] = array( /* calendar_datefield */
 "'date_creation'" . ' is NOT filled by any activated use metadata mapping fields.',
 'So activate metadata usage <strong>or</strong> change to $conf[\'calendar_datefield\'] = \'date_available\'',
 'Activate metadata usage as you want: ',
 '1 - $conf[\'use_iptc\'] = true, or $conf[\'use_exif\'] = true, each way will be correct.',
 '2 - And respectively map:',
 '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\'  => \'2#055\', ...',
 'or/and:',
 '$conf[\'use_exif_mapping\'] = array(\'date_creation\' => \'DateTimeOriginal\', ...',
 '3 - Finally, a new task is up to you: Metadata synchronization.', );
$lang['Adv_case'][10] = array( /* newcat_default_visible */
 'Not useful, private status is better, so code $conf[\'newcat_default_visible\'] = true,', );
$lang['Adv_case'][11] = array( /* level_separator */
 'Try something else like $conf[\'level_separator\'] = \'+ \',',  );
$lang['Adv_case'][12] = array( /* paginate_pages_around */
 'Usual range is between 2 and 5. To be light, choose $conf[\'paginate_pages_around\'] = 2, ',
 'To offer large jump, choose $conf[\'paginate_pages_around\'] = 7,', );
$lang['Adv_case'][13] = array( /* tn_width */
 'Should be a close value to your thumbnail width. Usual range is between 96 and 150, about $conf[\'tn_width\'] = 128,', );
$lang['Adv_case'][14] = array( /* tn_height */
 'Should be a close value to your thumbnail height. Usual range is between 96 and 150, about $conf[\'tn_height\'] = 128,', );
$lang['Adv_case'][15] = array( /* tn_height */
 'Thumbnail height and width have to be equal.',
 'Choose $conf[\'tn_height\'] = $conf[\'tn_width\'],',
 'or $conf[\'tn_width\'] = $conf[\'tn_height\'],', );
$lang['Adv_case'][16] = array( /* show_version */
 'For security reason, please set $conf[\'show_version\'] = false,', );
$lang['Adv_case'][17] = array( /* show_thumbnail_caption */
 'For a lighter gallery just have a look to $conf[\'show_thumbnail_caption\'] = false,', );
$lang['Adv_case'][18] = array( /* show_picture_name_on_title */
 'For a lighter gallery just have a look to $conf[\'show_picture_name_on_title\'] = false,', );
$lang['Adv_case'][19] = array( /* subcatify */
 'If you do NOT have any category descriptions just have a look to $conf[\'subcatify\'] = false,', );
$lang['Adv_case'][20] = array( /* allow_random_representative */
 'Leave $conf[\'allow_random_representative\'] = true, ',
 'but analyze if you can avoid for performance reasons.' , );
$lang['Adv_case'][21] = array( /* prefix_thumbnail */
 'Be careful your $conf[\'prefix_thumbnail\'] is NOT standard.',
 'Do NOT change it except if your thumbnails are NOT visible.',
 'Distant site may use a different prefix but create_listing_file.php must be modified.',
 'You will get a warning message during synchronization in that case.',
 'Try to keep the same prefix thru all your sites either local or distants.',
 'Keep this parameter in your ./include/config_ <strong>local.inc.php</strong>',
 'See our wiki configuration page for more information about ./include/config_<strong>local.inc.php</strong>.', );
$lang['Adv_case'][22] = array( /* users_page */
 'Unless you have a low band connection, you can draw up $conf[\'users_page\'] to a higher value if you have more than 20 members.', );
$lang['Adv_case'][23] = array( /* mail_options */
 'Should be false, only few webmasters have to set $conf[\'mail_options\'] = true, ',
 'A specific advice you can get from an advanced user on our forum in some mailing issues.', );
$lang['Adv_case'][24] = array( /* check_upgrade_feed */
 'Should be false, only PWG dev Team have to set $conf[\'check_upgrade_feed\'] = true, for test purpose.' , );
$lang['Adv_case'][25] = array( /* rate_items */
 'Your $conf[\'rate_items\'] would have 4 or 5 items not less.', );
$lang['Adv_case'][26] = array( /* rate_items */
 'Your $conf[\'rate_items\'] would have 5 or 6 items not more.',
 'Check your best rated pictures prior to remove some values.',
 'Reduce excessive rating and change your $conf[\'rate_items\'].', );
$lang['Adv_case'][27] = array( /* show_iptc */
 'Could be true, think about $conf[\'show_iptc\'] = false,',
 'Some Professional photographers choose false their reasons are not really professional.' ,
 'Do NOT confuse between <strong>show</strong>_iptc and <strong>use</strong>_iptc (have a look on metadata page on our wiki).', );
$lang['Adv_case'][28] = array( /* use_iptc */
 'Documentalists and professionnal photographers would set it true, but beginners should leave it as $conf[\'use_iptc\'] = false,',
 'Take care of mentionned fields in metadata synchronization.',
 'Mentionned fields would be rewrited with IPTC values even those ones are NOT empty.',
 'Do NOT confuse between <strong>show</strong>_iptc and <strong>use</strong>_iptc (have a look on metadata page on our wiki).', );
$lang['Adv_case'][29] = array( /* use_iptc */
 'How to deal with IPTC:',
 '1 - Copy one of your jpg pictures (a public one) in ./tools/',
 '2 - Rename it as sample.jpg.',
 '3 - Run ./tools/metadata.php', 
 '4 - Analyse results to determine which IPTC fields could be used to override database fields.',
 'Beginners would prefer to keep $conf[\'use_iptc\'] = false,',
 'Advanced users make documentation efforts prior to upload their pictures.',
 'IPTC fields have to be described in $conf[\'use_iptc_mapping\']',
 'In any case, <strong>show</strong>_iptc_mapping and <strong>use</strong>_iptc_mapping must be totally different.', );
$lang['Adv_case'][30] = array( /* use_iptc_mapping */
 'How to deal with IPTC:',
 'Take care of mentionned fields in metadata synchronization.',
 'Mentionned fields would be rewrited with IPTC values even those ones are NOT empty.',
 'In any case, <strong>show</strong>_iptc_mapping and <strong>use</strong>_iptc_mapping must be totally different.', );
$lang['Adv_case'][31] = array( /* show_exif */
 'Should be true, some information from your camera can be displayed.',
 'Think about EXIF information could be different depending on camera models.',
 'If you change your camera these fields could be partly different.',
 'Many professional photographers choose false, their reasons are to protect their knowledge.' ,
 'Do NOT confuse between <strong>show</strong>_exif and <strong>use</strong>_exif (have a look on metadata page on our wiki).', );
$lang['Adv_case'][32] = array( /* use_exif */
 'Documentalists and professionnal photographers would set it true, but beginners should leave the default value.',
 'Take care of mentionned fields in metadata synchronization.',
 'Mentionned fields would be rewrited with EXIF values even those ones are NOT empty.',
 'Do NOT confuse between <strong>show</strong>_exif and <strong>use</strong>_exif (have a look on metadata page on our wiki).', );
$lang['Adv_case'][33] = array( /* **navigation */
 'You can use keyboard arrows to navigate between pictures.', );
$lang['Adv_case'][34] = array( /* compiled_template_cache_language */
 'Should be true, translation will be done at compilation time.',
 'If you are modifying a language (translators), you should consider to set it false.',
 'Setting it false, template @translate function are called at usage time.' );
$lang['Adv_case'][35] = array( /* template_compile_check */
 'Default is true, template changes are detected and the template is compiled.',
 'If you are not updating template any more for several days, ',
 'you should consider to set it false.',
 'Choosing false, template changes are not detected, this improves response time.',
 'Anyway Specials > Maintenance > Purge compiled templates, is recommended after this $conf change.' );
?>