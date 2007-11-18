<?php
/*
Plugin Name: Extended Description
Version: 1.8.a
Description: Allow multilanguage description / Permet d'avoir des descriptions mutilingues
Plugin URI: http://phpwebgallery.net/ext/extension_view.php?eid=175
Author: PhpWebGallery team
Author URI: http://www.phpwebgallery.net
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