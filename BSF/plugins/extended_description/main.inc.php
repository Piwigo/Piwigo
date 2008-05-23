<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 Piwigo team    http://phpwebgallery.net |
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

/*
Plugin Name: Extended Description
Version: 1.8
Description: Allow multilanguage description / Permet d'avoir des descriptions mutilingues
Plugin URI: http://phpwebgallery.net/ext/extension_view.php?eid=175
Author: Piwigo team
Author URI: http://piwigo.org
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

function get_user_language_desc($desc)
{
	global $user;

	$user_lang = substr($user['language'], 0, 2);

	if (!substr_count(strtolower($desc), '[lang=' . $user_lang . ']'))
	{
		$user_lang = 'default';
		if (!substr_count(strtolower($desc), '[lang=default]'))
		{
			$desc = preg_replace("#(\A|\[/lang\])(.*?)(\[lang=(.*?)\]|\Z)#is", '$1[lang=default]$2[/lang]$3', $desc);
		}
	}

	preg_match_all("#\[lang=(" . $user_lang . "|all)\](.*?)\[/lang\]#is", $desc, $matches);
	
	return implode('', $matches[2]);
}

function extended_desc_mail_group_assign_vars($assign_vars)
{
	if (isset($assign_vars['CPL_CONTENT']))
	{
		$assign_vars['CPL_CONTENT'] = get_user_language_desc($assign_vars['CPL_CONTENT']);
	}
	return $assign_vars;
}

add_event_handler ('render_category_description', 'get_user_language_desc');
add_event_handler ('render_element_description', 'get_user_language_desc');
add_event_handler('nbm_render_user_customize_mail_content', 'get_user_language_desc');
add_event_handler('mail_group_assign_vars', 'extended_desc_mail_group_assign_vars');

?>