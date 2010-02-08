<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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


//
// New advice needs:
//    $lang['Adv_case'][xx] = 'Text' ==> in all plugin.lang
//    $adv['c'][xx] = Condition (default true)
//    $adv['v'][xx] = Value (if there is one (facultative))
//    $adv['n'][xx] = Name of $conf (A new advice about blah-blah)
//
//    prefix '**' . => Not a $conf['xxxxx']

load_language('plugin.lang', dirname(__FILE__).'/');

// No needed translation here below 

// Use l10n if your need a localization and update all plugin.lang

$adv['n'][0] = '**' . $lang['contribute'];

$adv['n'][1] = 'newcat_default_status';
$adv['c'][1] = ($conf['newcat_default_status'] !== 'public');
$adv['v'][1] = sprintf($lang['current'], 'public');

$adv['n'][2] = 'slideshow_period';
$adv['c'][2] = ( $conf['slideshow_period'] < 4 );
$adv['v'][2] = sprintf($lang['current'], $conf['slideshow_period']);

$adv['n'][3] = 'file_ext';
$adv['c'][3] = ( in_array('php',$conf['file_ext']) );
$adv['v'][3] = sprintf($lang['current'], implode(', ', $conf['file_ext']));

$adv['n'][4] = 'show_iptc_mapping';

$adv['n'][5] = 'top_number';
$adv['c'][5] = ( $conf['top_number'] > 50 );
$adv['v'][5] = sprintf($lang['current'], $conf['top_number']);

$adv['n'][6] = 'top_number';
$adv['c'][6] = ( $conf['top_number'] < 2 ) ? true : false;
$adv['v'][6] = sprintf($lang['current'], $conf['top_number']);

$adv['n'][7] = 'anti-flood_time';
$adv['c'][7] = ( $conf['anti-flood_time'] > 100 ) ? true : false;
$adv['v'][7] = sprintf($lang['current'], $conf['anti-flood_time']);

$adv['n'][8] = 'calendar_datefield';
$adv['c'][8] = ( !in_array($conf['calendar_datefield'],
        array('date_creation','date_available')) );
$adv['v'][8] = sprintf($lang['current'], $conf['calendar_datefield']);

/* Unavailable creation date and default calendar is creation date */
$adv['n'][9] = 'calendar_datefield';
$adv['c'][9] = ( (( $conf['use_exif'] and
                isset($conf['use_exif_mapping']['date_creation']) ) 
                or ( $conf['use_iptc'] and
                isset($conf['use_iptc_mapping']['date_creation']) )) 
                and ( $conf['calendar_datefield'] == 'date_creation' ) ) 
                ? false : true;
$adv['v'][9] = sprintf($lang['current'], $conf['calendar_datefield']);

$adv['n'][10] = 'newcat_default_visible';
$adv['c'][10] = !$conf['newcat_default_visible'];
$adv['v'][10] = sprintf($lang['current'], 'false');

$adv['n'][11] = 'level_separator';
$adv['c'][11] = ( $conf['level_separator'] == ' / ' );
$adv['v'][11] = sprintf($lang['current'], $conf['level_separator']);

$adv['n'][12] = 'paginate_pages_around';
$adv['c'][12] = (($conf['paginate_pages_around'] < 2)
            or ($conf['paginate_pages_around'] > 12));
$adv['v'][12] = sprintf($lang['current'], $conf['paginate_pages_around']);

$adv['n'][13] = 'tn_width';
$adv['c'][13] = (($conf['tn_width'] < 66)
            or ($conf['tn_width'] > 180));
$adv['v'][13] = sprintf($lang['current'], $conf['tn_width']);

$adv['n'][14] = 'tn_height';
$adv['c'][14] = (($conf['tn_height'] < 66)
            or ($conf['tn_height'] > 180));
$adv['v'][14] = sprintf($lang['current'], $conf['tn_height']);

$adv['n'][15] = 'tn_height';

$adv['c'][15] = ( $conf['tn_height'] !== $conf['tn_width'] );
$adv['v'][15] = l10n('TN-height&width');

$adv['n'][16] = 'show_version';
$adv['c'][16] = $conf['show_version'];
$adv['v'][16] = sprintf($lang['current'], 'true');

$adv['n'][17] = 'show_thumbnail_caption';
$adv['c'][17] = $conf['show_thumbnail_caption'];
$adv['v'][17] = sprintf($lang['current'], 'true');

$adv['n'][18] = 'show_picture_name_on_title';
$adv['c'][18] = $conf['show_picture_name_on_title'];
$adv['v'][18] = sprintf($lang['current'], 'true');

$adv['n'][19] = 'tags_default_display_mode';
$adv['c'][15] = ( $conf['tags_default_display_mode'] == 'cloud' );
$adv['v'][19] = sprintf($lang['current'], "'".$conf['tags_default_display_mode']."'");

$adv['n'][20] = 'allow_random_representative';
$adv['c'][20] = $conf['allow_random_representative'];
$adv['v'][20] = sprintf($lang['current'], 'true');

$adv['n'][21] = 'prefix_thumbnail';
$adv['c'][21] = ( $conf['prefix_thumbnail'] !== 'TN-' );
$adv['v'][21] = sprintf($lang['current'], $conf['prefix_thumbnail']);

$adv['n'][22] = 'users_page';
$adv['c'][22] = ( $conf['users_page'] < 21 );
$adv['v'][22] = sprintf($lang['current'], $conf['users_page']);

$adv['n'][23] = 'mail_options';
$adv['c'][23] = $conf['mail_options'];
$adv['v'][23] = sprintf($lang['current'], 'true');

$adv['n'][24] = 'check_upgrade_feed';
$adv['c'][24] = $conf['check_upgrade_feed'];
$adv['v'][24] = sprintf($lang['current'], 'true');

$adv['n'][25] = 'rate_items';
$adv['c'][25] = ( count($conf['rate_items']) < 4 );
$adv['v'][25] = sprintf($lang['current'], $conf['rate_items']);

$adv['n'][26] = 'rate_items';
$adv['c'][26] = ( count($conf['rate_items']) > 6 );
$adv['v'][26] = sprintf($lang['current'], $conf['rate_items']);

$adv['n'][27] = 'show_iptc';
$adv['c'][27] = $conf['show_iptc'];
$adv['v'][27] = sprintf($lang['current'], 'true');

$adv['n'][28] = 'use_iptc';
$adv['c'][28] = $conf['use_iptc'];
$adv['v'][28] = sprintf($lang['current'], 'true');

$adv['n'][29] = 'use_iptc';

$adv['n'][30] = 'use_iptc_mapping';

$adv['n'][31] = 'show_exif';
$adv['v'][31] = sprintf($lang['current'], (($conf['show_exif'])? 'true':'false' ));

$adv['n'][32] = 'use_exif';
$adv['v'][32] = sprintf($lang['current'], (($conf['use_exif'])? 'true':'false' ));

$adv['n'][33] = '**' . $lang['navigation'];

$adv['n'][34] = 'compiled_template_cache_language';
$adv['v'][34] = sprintf($lang['current'], (($conf['compiled_template_cache_language'])? 'true':'false' ));

$adv['n'][35] = 'template_compile_check';
$adv['v'][35] = sprintf($lang['current'], (($conf['template_compile_check'])? 'true':'false' ));

?>