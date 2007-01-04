<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-12-21 22:38:20 +0100 (jeu., 4 jan. 2007) $
// | last modifier : $Author: Vincent $
// | revision      : $Revision: 1677 $
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+
//$lang_info['language_name'] = 'Français';
//$lang_info['country'] = 'France';
//$lang_info['charset'] = 'iso-8859-1';
//$lang_info['direction'] = 'ltr';
//$lang_info['code'] = 'fr';
foreach ($conf as $key => $value)
{
  if ( is_string($value) )
  {
    $bool = ($value == 'false') ? false : $value;
    $bool = ($value == 'true') ? true : $bool;
    $conf[$key] = $bool; 
  }
}

//
//               Don't forget to update range for new advices
//
$cases = range(1,35);
srand ((double) microtime() * 10000000);
shuffle($cases);

$cond = false;
foreach ($cases as $id_adv)
{
  if ($cond) break;
  $adv = array();
  switch ($id_adv) {
    Case 1 :
      $adv[] = 'Current value: public. ';
      $adv[] = 'Try $conf[\'newcat_default_status\'] = \'private\';';
      $adv[] = 'You will have more time to describe and check your pictures.';
      $adv[] = 'Time to decide between private and public status.';
      $adv[] = 'If you choose private, time to distribute authorization.';
      $adv[] = 'Your new category will be well prepared.';
      $cond = ($conf['newcat_default_status'] !== 'public');
      $confk = 'newcat_default_status';
      break;  
      
    Case 2 :
      $adv[] = 'Current value: ' . (string) $conf['slideshow_period'] . '.';
      $adv[] = 'This value could be too small for low band connections.';
      $adv[] = 'Think about higher value like 4.';
      $cond = ( $conf['slideshow_period'] < 4 );
      $confk = 'slideshow_period';
      break;  
      
    Case 3 :
      $adv[] = 'Current value: ' . implode(', ', $conf['file_ext']) . '. ';
      $adv[] = 'Should never contains extensions which can be executed';
      $adv[] = 'on the server side like *.php, *.PHP, *.asp, ...';
      $adv[] = 'Think about higher value like 4.';
      $cond = ( in_array('php',$conf['file_ext']) );
      $confk = 'file_ext';
      break;  
      
    Case 4 :
      $adv[] = 'Show ITPC Data from your picture:';
      $adv[] = ' 1 - Copy one of your jpg pictures (a public one)' . 
                   ' in ./tools/<br />' . 
               ' 2 - Rename it as sample.jpg.<br />' . 
               ' 3 - Run ./tools/metadata.php<br />' .
               ' 4 - Analyse results to determine which ITPC fields could be' . 
                   ' useful for your visitors.';
      $adv[] = 'Beginners would prefer to keep $conf[\'show_iptc\'] = false;';
      $adv[] = 'Advanced users would take care of $lang values and impacts' . 
        ' on templates.';
      $cond = true;
      $confk = 'show_iptc_mapping';
      break;  
  
     Case 5 :
      $adv[] = 'Current value: ' . (string) $conf['top_number'] . '.';
      $adv[] = 'This value is maybe too high for low connections, ' . 
               'think about 25-50 depending on your thumbnail sizes.';
      $cond = ( $conf['top_number'] > 50 );
      $confk = 'top_number';
      break;  
  
     Case 6 :
      $adv[] = 'Current value: ' . (string) $conf['top_number'] . '.';
      $adv[] = 'One? It could be too low for random pictures, ' . 
               'think about 5-10 depending on your thumbnail sizes.';
      $cond = ( $conf['top_number'] < 2 ) ? true : false;
      $confk = 'top_number';
      break;  
      
     Case 7 :
      $adv[] = 'Current value: ' . (string) $conf['anti-flood_time'] . '.';
      $adv[] = 'For normal flow processing, your value is probably too high. ' .
			         'Reasonable value is 60 (default).' ;
      $cond = ( $conf['anti-flood_time'] > 100 ) ? true : false;
      $confk = 'anti-flood_time';
      break;  
      
     Case 8 :
      $adv[] = 'Current value: ' . (string) $conf['calendar_datefield'] . '.';
      $adv[] = 'Authorized values are ' .
			         "'date_creation' or 'date_available'" .
               ', otherwise you can get unpredictable results.' ;
      $cond = ( !in_array($conf['calendar_datefield'], 
        array('date_creation','date_available')) );
      $confk = 'calendar_datefield';
      break;  
      
     Case 9 :
      // If (iptc or exif) are used and date_creation is updated
      // Then it's Ok, you can use date_creation by default for calendar
      // else ... Advice
	    $adv[] = 'Current value: ' . (string) $conf['calendar_datefield'] . '.';
      $adv[] = "'date_creation'" . ' is NOT filled by ' . 
      'any activated use metadata mapping fields.';
      $adv[] = 'So activate metadata usage <strong>or</strong> change to ' . 
      '$conf[\'calendar_datefield\'] = \'date_available\'';
      $adv[] = 'Activate metadata usage as you want: <br />' .
      '1 - $conf[\'use_iptc\'] = true; or $conf[\'use_exif\'] = true; ' . 
      'each way will be correct.<br />' .
      '2 - And respectively map:<br />' .
      '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\' ' .
      '=> \'2#055\', ...<br />' .
      'or/and:<br />' .
      '$conf[\'use_exif_mapping\'] = array(\'date_creation\' ' .
      '=> \'DateTimeOriginal\', ...<br />' .
      '3 - Finally, a new task is up to you: Metadata synchronization.'  ;
      $cond2 = ( $conf['use_exif'] and 
			          isset($conf['use_exif_mapping']['date_creation']) );
      $cond3 = ( $conf['use_iptc'] and
			          isset($conf['use_iptc_mapping']['date_creation']) );
      $cond = ( $conf['calendar_datefield'] == 'date_creation' );
			$cond = ( ($cond2 or $cond3) and $cond ) ? false : true;
      $confk = 'calendar_datefield';
      break;  
      
     Case 10 :
      $adv[] = 'Current value: false.';
      $adv[] = 'Not useful, private status is better, so code ' .
               '$conf[\'newcat_default_visible\'] = true;'  ;
      $cond = !$conf['newcat_default_visible'];
      $confk = 'newcat_default_visible';
      break;  
      
     Case 11 :
      $adv[] = 'Current value: true.';
      $adv[] = 'Any new subscriber has access to High Resolution pictures. ' .
      'Is it what you want? No, so try ' . 
			'$conf[\'newuser_default_enabled_high\'] = false;' ;
      $cond = $conf['newuser_default_enabled_high'];
      $confk = 'newuser_default_enabled_high';
      break;  
      
     Case 12 :
      $adv[] = 'Current value: ' . (string) $conf['level_separator'] . '.';
      $adv[] = 'Try something else like $conf[\'level_separator\'] = \'+ \';';
      $cond = ( $conf['level_separator'] == ' / ' );
      $confk = 'level_separator';
      break;  
      
     Case 13 :
      $adv[] = 'Current value: ' . (string) $conf['paginate_pages_around'] . '.';
      $adv[] = 'Usual range is between 2 and 5. To be light, choose ' .
      '$conf[\'paginate_pages_around\'] = 2; <br />' .
      'To offer large jump, choose $conf[\'paginate_pages_around\'] = 7;';
      $cond = (($conf['paginate_pages_around'] < 2) 
			      or ($conf['paginate_pages_around'] > 12));
      $confk = 'paginate_pages_around';
      break;  

     Case 14 :
      $adv[] = 'Current value: ' . (string) $conf['tn_width'] . '.';
      $adv[] = 'Should be a close value to your thumbnail width.' .
      $adv[] = 'Usual range is between 96 and 150, ' . 
							 'about $conf[\'tn_width\'] = 128;';
      $cond = (($conf['tn_width'] < 66) 
			      or ($conf['tn_width'] > 180));
      $confk = 'tn_width';
      break;  

     Case 15 :
      $adv[] = 'Current value: ' . (string) $conf['tn_height'] . '.';
      $adv[] = 'Should be a close value to your thumbnail height.' .
      $adv[] = 'Usual range is between 96 and 150, ' . 
							 'about $conf[\'tn_height\'] = 128;';
      $cond = (($conf['tn_height'] < 66) 
			      or ($conf['tn_height'] > 180));
      $confk = 'tn_height';
      break;  

     Case 16 :
      $adv[] = 'Thumbnail height and width have to be equal.';
      $adv[] = 'Choose $conf[\'tn_height\'] = ' . (string) $conf['tn_width'] . 
			         ';<br />' .
							 'or $conf[\'tn_width\'] = ' . (string) $conf['tn_height'] . ';';
      $cond = ( $conf['tn_height'] !== $conf['tn_width'] );
      $confk = 'tn_height';
      break;  

     Case 17 :
      $adv[] = 'Current value: true.';
      $adv[] = 'For security reason, please set ' .
			         '$conf[\'show_version\'] = false;';
      $cond = $conf['show_version'];
      $confk = 'show_version';
      break;  

     Case 18 :
      $adv[] = 'Current value: true.';
      $adv[] = 'For a lighter gallery just have a look to ' .
               '$conf[\'show_thumbnail_caption\'] = false;';
      $cond = $conf['show_thumbnail_caption'];
      $confk = 'show_thumbnail_caption';
      break;  

     Case 19 :
      $adv[] = 'Current value: true.';
      $adv[] = 'For a lighter gallery just have a look to ' .
               '$conf[\'show_picture_name_on_title\'] = false;';
      $cond = $conf['show_picture_name_on_title'];
      $confk = 'show_picture_name_on_title';
      break;  

     Case 20 :
      $adv[] = 'Current value: true.';
      $adv[] = 'If you do NOT have any category descriptions just have ' .
               'a look to $conf[\'subcatify\'] = false;';
      $cond = $conf['subcatify'];
      $confk = 'subcatify';
      break;  

     Case 21 :
      $adv[] = 'Current value: true.';
      $adv[] = 'Leave $conf[\'allow_random_representative\'] = true; <br />' .
               'but analyze if you can avoid for performance reasons.' ;
      $cond = $conf['allow_random_representative'];
      $confk = 'allow_random_representative';
      break;  

     Case 22 :
      $adv[] = 'Current value: ' . (string) $conf['prefix_thumbnail'] . '.';
      $adv[] = 'Be careful your $conf[\'prefix_thumbnail\'] is NOT standard.';
      $adv[] = 'Do NOT change it except if your thumbnails are NOT visible.';
      $adv[] = 'Distant site may use a different prefix but ' . 
			         'create_listing_file.php must be modified.<br />' .
			         'You will get a warning message during synchronization in ' .
			         'that case.';
      $adv[] = 'Try to keep the same prefix thru all your sites either ' .
			         'local or distants.';
      $adv[] = 'Keep this parameter in your ./include/config_'.
			         '<strong>local.inc.php</strong>. <br />'.
							 'See our wiki configuration page for more information about ' .
							 './include/config_<strong>local.inc.php</strong>.';
      $cond = ( $conf['prefix_thumbnail'] !== 'TN-' );
      $confk = 'prefix_thumbnail';
      break;  

     Case 23 :
      $adv[] = 'Current value: ' . (string) $conf['users_page'] . '.';
      $adv[] = 'Unless you have a low band connection, you can draw up ' .
               '$conf[\'users_page\'] to a higher value ' . 
							 'if you have more than 20 members.';
      $cond = ( $conf['users_page'] < 21 );
      $confk = 'users_page';
      break;  

     Case 24 :
      $adv[] = 'Current value: true.';
      $adv[] = 'Should be false, only few webmasters have to set ' .
               '$conf[\'mail_options\'] = true; <br />' .
							 'A specific advice you can get from an advanced ' . 
							 'user on our forum in some mailing issues.' ;
      $cond = $conf['mail_options'];
      $confk = 'mail_options';
      break;  

     Case 25 :
      $adv[] = 'Current value: true.';
      $adv[] = 'Should be false, only PWG dev Team have to set ' .
               '$conf[\'check_upgrade_feed\'] = true; for test purpose.' ;
      $cond = $conf['check_upgrade_feed'];
      $confk = 'check_upgrade_feed';
      break;  

     Case 26 :
      $adv[] = '$conf[\'rate_items\'] has ' . count($conf['rate_items']) 
             . 'items.';
      $adv[] = 'Your $conf[\'rate_items\'] would have 4 or 5 items not less.';
      $cond = ( count($conf['rate_items']) < 4 );
      $confk = 'rate_items';
      break;  

     Case 27 :
      $adv[] = '$conf[\'rate_items\'] has ' . count($conf['rate_items']) 
             . 'items.';
      $adv[] = 'Your $conf[\'rate_items\'] would have 5 or 6 items not more.';
      $adv[] = 'Check your best rated pictures prior to remove some values.' .
			         '<br />Reduce excessive rating and change your ' .
               '$conf[\'rate_items\'].';
      $cond = ( count($conf['rate_items']) > 6 );
      $confk = 'rate_items';
      break;  
			 
     Case 28 :
      $adv[] = 'Current value: true.';
      $adv[] = 'Could be true, think about $conf[\'show_iptc\'] = false;'
			       . '<br />Some Professional photographers choose false ' .
						   'their reasons are not really professional.' ;
			$adv[] = 'Do NOT confuse between <strong>show</strong>_iptc and ' .
               '<strong>use</strong>_iptc (have a look on metadata page ' .
							 'on our wiki.';
      $cond = $conf['show_iptc'];
      $confk = 'show_iptc';
      break;  
			 
     Case 29 :
      $adv[] = 'Current value: true.';
      $adv[] = 'Documentalists and professionnal photographers would ' .
			         'set it true, but beginners should leave it ' . 
							 'as $conf[\'use_iptc\'] = false;';
      $adv[] = 'Take care of mentionned fields in metadata synchronization.' .
               '<br />Mentionned fields would be rewrited with IPTC values ' .
							 ' even those ones are NOT empty.';
			$adv[] = 'Do NOT confuse between <strong>show</strong>_iptc and ' .
               '<strong>use</strong>_iptc (have a look on metadata page ' .
							 'on our wiki.';
      $cond = $conf['use_iptc'];
      $confk = 'use_iptc';
      break;  
			 
     Case 30 :
      $adv[] = 'How to deal with IPTC:';
      $adv[] = '1 - Copy one of your jpg pictures (a public one) in ./tools/' .
               '<br />2 - Rename it as sample.jpg.' .
               '<br />3 - Run ./tools/metadata.php' .
               '<br />4 - Analyse results to determine which ITPC fields ' . 
							 'could be used to override database fields.';
      $adv[] = 'Beginners would prefer to keep $conf[\'use_iptc\'] = false;';
      $adv[] = 'Advanced users make documentation efforts prior ' .
			         'to upload their pictures.<br />' .
							 'ITPC fields have to be described in ' .
							 '$conf[\'use_iptc_mapping\']';
			$adv[] = 'In any case, <strong>show</strong>_iptc_mapping and ' .
               '<strong>use</strong>_iptc_mapping must be totally different.';
      $cond = true;
      $confk = 'use_iptc';
      break;   
			
     Case 31 :
      $adv[] = 'How to deal with IPTC:';
      $adv[] = '1 - Copy one of your jpg pictures (a public one) in ./tools/' .
               '<br />2 - Rename it as sample.jpg.' .
               '<br />3 - Run ./tools/metadata.php' .
               '<br />4 - Analyse results to determine which ITPC fields ' . 
							 'could be used to override database fields.';
      $adv[] = 'Beginners would prefer to keep $conf[\'use_iptc\'] = false;';
      $adv[] = 'Advanced users make documentation efforts prior ' .
			         'to upload their pictures.';
      $adv[] = 'Take care of mentionned fields in metadata synchronization.' .
               '<br />Mentionned fields would be rewrited with IPTC values ' .
							 ' even those ones are NOT empty.';
			$adv[] = 'In any case, <strong>show</strong>_iptc_mapping and ' .
               '<strong>use</strong>_iptc_mapping must be totally different.';
      $cond = true;
      $confk = 'use_iptc_mapping';
      break;			           
			
     Case 32 :
      $adv[] = 'Current value: ' . ( ( $conf['show_exif'] ) ? 'true':'false' )
             . '.';
      $adv[] = 'Should be true, some information from your camera ' .
			         'can be displayed.';
      $adv[] = 'Think about EXIF information could be different depending ' . 
			         'on camera models.<br />' .
               'If you change your camera these fields could be ' .
							 'partly different.';
			$adv[] = 'Many professional photographers choose false, ' .
						   'their reasons are to protect their knowledge.' ;
			$adv[] = 'Do NOT confuse between <strong>show</strong>_exif and ' .
               '<strong>use</strong>_exif (have a look on metadata page ' .
							 'on our wiki.';
      $cond = true;
      $confk = 'show_exif';
      break;	
			
     Case 33 :
      $adv[] = 'How to deal with EXIF:';
      $adv[] = '1 - Copy one of your jpg pictures (a public one) in ./tools/' .
               '<br />2 - Rename it as sample.jpg.' .
               '<br />3 - Run ./tools/metadata.php' .
               '<br />4 - Analyse results to determine which EXIF fields ' . 
							 'could be used to override database fields.';
      $adv[] = 'Beginners would prefer to let default values.';
      $adv[] = 'Advanced users would take care of $lang values and ' .
			         'impacts on templates.';
			$adv[] = 'In any case, <strong>show</strong>_exif_mapping and ' .
               '<strong>use</strong>_exif_mapping must be totally different.';
      $cond = true;
      $confk = 'show_exif_mapping';
      break;			           
			 
     Case 34 :
      $adv[] = 'Current value: ' . ( ( $conf['use_exif'] ) ? 'true':'false' )
             . '.';
      $adv[] = 'Documentalists and professionnal photographers would ' .
			         'set it true, but beginners should leave the default value.';
      $adv[] = 'Take care of mentionned fields in metadata synchronization.' .
               '<br />Mentionned fields would be rewrited with EXIF values ' .
							 ' even those ones are NOT empty.';
			$adv[] = 'Do NOT confuse between <strong>show</strong>_exif and ' .
               '<strong>use</strong>_exif (have a look on metadata page ' .
							 'on our wiki.';
      $cond = true;
      $confk = 'use_exif';
      break;           						
			
     Case 35 :
      $adv[] = 'How to deal with EXIF:';
      $adv[] = '1 - Copy one of your jpg pictures (a public one) in ./tools/' .
               '<br />2 - Rename it as sample.jpg.' .
               '<br />3 - Run ./tools/metadata.php' .
               '<br />4 - Analyse results to determine which EXIF fields ' . 
							 'could be used to override database fields.';
      $adv[] = 'Beginners would prefer to let default values.';
      $adv[] = 'Advanced users would carefully chose overrided fields ' .
			         'prior to synchronize.';
      $adv[] = 'Take care of mentionned fields in metadata synchronization.' .
               '<br />Mentionned fields would be rewrited with IPTC values ' .
							 ' even those ones are NOT empty.';
			$adv[] = 'In any case, <strong>show</strong>_iptc_mapping and ' .
               '<strong>use</strong>_iptc_mapping must be totally different.';
      $cond = true;
      $confk = 'use_exif_mapping';
      break;			           
  }
}
  
?>
