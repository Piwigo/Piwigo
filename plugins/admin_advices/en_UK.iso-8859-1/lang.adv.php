<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-12-21 22:38:20 +0100 (jeu., 21 dÃ©c. 2006) $
// | last modifier : $Author: rub $
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
//$lang_info['language_name'] = 'English';
//$lang_info['country'] = ''Great Britain';
//$lang_info['charset'] = 'iso-8859-1';
//$lang_info['direction'] = 'ltr';
//$lang_info['code'] = 'en';
$nomore = false;
$cases = array();
for ($i = 1; $i < 5; $i++) 
{
   $cases[$i] = $i;
}  
srand ((double) microtime() * 10000000);
$set_adv = array_rand ($cases, 3);

foreach ($set_adv as $id_adv)
{
  switch ($id_adv) {
    Case 1 :
      $adv[] = 'Votre valeur actuelle: public. ';
      $adv[] = 'Essayez $conf[\'newcat_default_status\'] = \'private\';';
      $adv[] = 'Vous aurez ainsi le temps pour décrire et vérifier vos images,';
      $adv[] = 'de décider si vous laisserez la catégorie en privé';
      $adv[] = 'et donnerez quelques droits, ou si vous la passerez en public.';
      $adv[] = 'Soit un temps de réflexion pour bien préparer la catégorie.';
      $cond = ($conf['newcat_default_status'] !== 'public') ? true : false;
      $confk = 'newcat_default_status';
      break 2;
      
    Case 2 :
      $adv[] = 'Current value: ' . (string) $conf['slideshow_period'] . '.';
      $adv[] = 'This value could be too small for low band connections.';
      $adv[] = 'Think about higher value like 4.';
      $cond = ( $conf['slideshow_period'] < 4 ) ? true : false;
      $confk = 'slideshow_period';
      break 2;
      
    Case 3 :
      $adv[] = 'Current value: ' . implode(', ', $conf['file_ext']) . '. ';
      $adv[] = 'Should never contains extensions which can be executed';
      $adv[] = 'on the server side like *.php, *.PHP, *.asp, ...';
      $adv[] = 'Think about higher value like 4.';
      $cond = ( in_array('php',$conf['file_ext']) ) ? true : false;
      $confk = 'file_ext';
      break 2;
      
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
      break 2;
  
  
    default :
      $nomore = true;
      $adv[] = '';
      $cond = false;
  }
}
/* 
A Ajouter si besoin:
sprintf($adv[0/1], $conf[$confk]);

Conseils à intégrer:
( $conf['top_number'] > 50 )
      'top_number',
       'Your current value (%u) is maybe too high for low ' .
      'connexions, think about 25-50 depending on your thumbnail sizes.' 
      
       
( $conf['top_number'] < 2 )
      'top_number', 
      'Your current value (%u) may be too low for some people,' .
      ' might be about 5-10 depending on your thumbnail sizes.' 
       
( $conf['anti-flood_time'] > 100 )
      'anti-flood_time', 
      'Your current value (%u) could be too high,' .
      ' should be arround 60 for flow reasons only.' 
       

( !in_array($conf['calendar_datefield'], 
array('date_creation','date_available') )
      'calendar_datefield', 
      'Current value: %s. Authorized values are ' .
      '\'date_creation\' or \'date_available\', otherwise you can get ' .
      'unpredictable results.'  
  
  // If (iptc & exif) are used and date_creation is updated
  // Then it's Ok
  // else Using Calendar by date_creation is quite stupid
  // Take care! condition has been reversed by first ! (so then is else)
  if ( ! ( ($conf['use_iptc'] == true) or ($conf['use_exif'] == true) ) and
       ( (isset($conf['use_iptc']['date_creation'])) or
           (isset($conf['use_exif']['date_creation'])) ) )
  { 
( $conf['calendar_datefield'] == 'date_creation' )
      'calendar_datefield', 
      'Current value: %s. ' .
      ' \'date_creation\' is NOT filled by any activated use metadata ' . 
      'mapping fields.' ,
      'So activate metadata usage or change to $conf[\'calendar_datefield\'] ' . 
      '= \'date_available\'' ,
      '1 - Activate metadata usage as you want: ',
      '$conf[\'use_iptc\'] = true; or $conf[\'use_exif\'] = true; each way ' . 
      'may be correct.' ,
      '2 - And respectively map:' ,
      '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\' ' .
      '=> \'2#055\', ...' ,
      'or/and:' ,
      '$conf[\'use_exif_mapping\'] = array(\'date_creation\' ' .
      '=> \'DateTimeOriginal\', ...' ,
      '3 - Finally, a new task is up to you: Metadata synchronization.' 
  }
  
  
( $conf['newcat_default_visible'] == false )
      'newcat_default_visible', 
      'Your current value: false.' .
      'Not useful, private status is better, so code ' .
      '$conf[\'newcat_default_visible\'] = true;' 
      
( $conf['newcat_default_status'] == 'public' )
      'newcat_default_status', 'Your current value: public.' ,
      'Try $conf[\'newcat_default_status\'] = \'private\'; could allow ' .
      'you time to prepare.' 
      
( $conf['newuser_default_enabled_high'] == true )
      'newuser_default_enabled_high', 
      'Your current value: true.' .
      ' Any new subscriber has access to High Resolution pictures is what ' .
      'you want, if NOT try $conf[\'newuser_default_enabled_high\'] = false;' 
      
( $conf['level_separator'] == ' / ' )
      'level_separator', 
      'Current value: \' / \'.' .
      'Try just a comma like this $conf[\'level_separator\'] = \', \';' 
      
(($conf['paginate_pages_around'] < 2) or ($conf['paginate_pages_around'] > 12))
      'paginate_pages_around', 
      'Current value: %u.' .
      'Usual range is between 2 and 5, be light as ' .
      '$conf[\'paginate_pages_around\'] = 2;' .
      ' or dynamic for visitors as $conf[\'paginate_pages_around\'] = 7;' 
      
( ($conf['tn_width'] < 66) or ($conf['tn_width'] > 180) )
      'tn_width', 
      'Current value: %u.' .
      'Should be a closed value to your thumbnail width.' .
      ' Usual range is between 96 and 150, about $conf[\'tn_width\'] = 128;'
      
( ($conf['tn_height'] < 66) or ($conf['tn_height'] > 180))
      'tn_height', 
      'Current value: %u.' .
      'Should be a closed value to your thumbnail height.' .
      ' Usual range is between 96 and 150, about $conf[\'tn_height\'] = 128;'
       
( $conf['tn_height'] == $conf['tn_width'] )
      'tn_height', 
      'Try equal values like this $conf[\'tn_height\'] = $conf[\'tn_width\'];' .
      'or $conf[\'tn_width\'] = $conf[\'tn_height\']; ' .
      'depending on the first set value.' 
      
( $conf['show_version'] == true )
      'show_version', 
      'Current value: true.' .
      'For security reason, please set $conf[\'show_version\'] = false;' 
      
( $conf['show_thumbnail_caption'] == true )
      'show_thumbnail_caption', 
      'Current value: true.' .
      'For a lighter gallery just have a look to ' .
      '$conf[\'show_thumbnail_caption\'] = false;' 
      
( $conf['show_picture_name_on_title'] == true )
      'show_picture_name_on_title', 
      'Current value: true.' .
      'For a lighter gallery just have a look to ' .
      '$conf[\'show_picture_name_on_title\'] = false;' 
      
( $conf['subcatify'] == true )
      'subcatify', 
      'Current value: true.' .
      'If you do NOT have any category descriptions just have a look to' .
      ' $conf[\'subcatify\'] = false;' 
      
( $conf['allow_random_representative'] == true )
      'allow_random_representative', 
      'Current value: true.' .
      'Leave $conf[\'allow_random_representative\'] = true; but study ' .
      'if you can avoid' 
      
( $conf['prefix_thumbnail'] !== 'TN-' )
      'prefix_thumbnail', 
      'Current value: \'%s\'.' .
      'Be careful your $conf[\'prefix_thumbnail\'] is NOT standard.',
      'Return to default if your thumbnail are NOT visible.' ,
      'Default is $conf[\'prefix_thumbnail\'] = \'TN-\';' .
      'Distant site may use a different prefix but create_listing_file.php ' .
      'must be changed.'
      
( $conf['users_page'] < 21 )
      'users_page', 
      'Current value: %u.' .
      'Unless your connexion is low, you can draw up $conf[\'users_page\'] ' .
      'to a higher value.' 
      
( $conf['mail_options'] == true )
      'mail_options', 
      'Current value: true.' .
      'Should be false, only few webmasters have to set ' .
      '$conf[\'mail_options\'] = true; On specific advice.' 
      
( $conf['check_upgrade_feed'] == true )
      'check_upgrade_feed', 
      'Current value: true.' .
      'Should be false, only PWG Team have to set ' .
      '$conf[\'check_upgrade_feed\'] = true; for BSF tests purpose.' 
      
( count( $conf['rate_items'] ) < 4 )
      'rate_items', 
      'Your $conf[\'rate_items\'] would have 4 or 5 items not less.'
      
( count( $conf['rate_items'] ) > 6)
      'rate_items', 
      'Your $conf[\'rate_items\'] would have 5 or 6 items not more.' 
            
( $conf['show_itpc'] == true )
      'show_itpc', 
      'Current value: true.' .
      ' Could be true, but usualy $conf[\'show_iptc\'] = false; ' . 
      'Only set true if you want to show other IPTC fields.',
      'Or if you want different descriptions between PWG ' .
      'database and IPTC fields.',
      'In any case, show_iptc_mapping and use_iptc_mapping must be ' .
      'totally different.' 
      
( $conf['use_itpc'] == true )
      'use_itpc', 
      'Current value: true.' .
      ' Documentalists and professionnal photographers would set it true,' . 
      ' but beginners should leave as $conf[\'use_iptc\'] = false;' .
      ' Take care of mentionned fields in metadata synchronization.' .
      ' Mentionned fields would be rewrited with IPTC values even those ones are empty.' 
      
( true )
      'use_iptc_mapping', 
      ' 1 - Copy one of your jpg pictures (a public one) in ./tools/' .
      ' 2 - Rename it as sample.jpg.' .
      ' 3 - Run ./tools/metadata.php' .
      ' 4 - Analyse results to determine which ITPC fields could be used to override database fields.' .
      ' Beginners would prefer to keep $conf[\'use_iptc\'] = false;' .
      ' Advanced users make documentation efforts prior to upload their pictures.' 
      
( true )
      'show_exif', 
      'Current value: ' . (( $conf['show_exif']) ? 'true.':'false.') . 
      ' Should be true, some information from your camera can be displayed.' .
      ' Think about EXIF information could be different depending on camera models.' .
      ' If you change your camera these fields could be partly different.' );
      
( true )
      'show_exif_mapping', 
      ' Process as for iptc mapping.' .
      ' Beginners would prefer to let default values.' .
      ' Advanced users would take care of $lang values and impacts on templates.' 
      
( $conf['use_exif'] == true )
      'use_exif', 
      'Current value: true.' .
      ' Documentalists and professionnal photographers would set it true,' . 
      ' but beginners should leave the default value;' .
      ' Take care of mentionned fields in metadata synchronization.' .
      ' Mentionned fields would be rewrited with EXIF values even those ones are empty.' 
      
( true )
      'use_exif_mapping', 
      ' Process as for iptc mapping.' .
      ' Beginners would prefer to let default values again.' .
      ' Advanced users would carefully chose overrided fields prior to synchronize.' 


 */
  
?>
