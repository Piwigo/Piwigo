<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-11-26 21:15:50 +0100 (sam., 26 nov. 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 958 $
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

// Extract mail fonctions of password.php
// And Modify pwg_mail (add pararameters + news fonctionnalities)
// And var conf_mail, function init_conf_mail, function format_email

define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/*
 * Initialization of global variable $conf_mail
 */
function init_conf_mail()
{
  global $conf, $conf_mail;

  if (count($conf_mail) == 0)
  {
    $conf_mail['mail_options'] = $conf['mail_options'];
    $conf_mail['send_bcc_mail_webmaster'] = ($conf['send_bcc_mail_webmaster'] == true ? true : false);
    list($conf_mail['email_webmaster']) = mysql_fetch_array(pwg_query('select '.$conf['user_fields']['email'].' from '.USERS_TABLE.' where '.$conf['user_fields']['id'].' = '.$conf['webmaster_id'].';'));
    $conf_mail['formated_email_webmaster'] = format_email($conf['gallery_title'], $conf_mail['email_webmaster']);
    $conf_mail['text_footer'] = "\n\n-- \nPhpWebGallery ".($conf['show_version'] ? PHPWG_VERSION : '');
  }

  return true;
}

function format_email($name, $email)
{
  if (strpos($email, '<') === false)
    return $name.' <'.$email.'>';
  else
    return $name.$email;
}

/**
 * sends an email, using PhpWebGallery specific informations
 */
function pwg_mail($to, $from = '', $subject = 'PhpWebGallery', $infos = '')
{
  global $conf, $conf_mail;

  $to = format_email('', $to);

  if ($from =='')
    $from = $conf_mail['formated_email_webmaster'];
  else
    $from = format_email('', $from);

  $headers = 'From: '.$from."\n";
  $headers.= 'Reply-To: '.$from."\n";
  if ($conf_mail['send_bcc_mail_webmaster'])
    $headers.= 'Bcc: '.$conf_mail['formated_email_webmaster']."\n";


  $options = '-f '.$from;

  $content = $infos;
  $content.= $conf_mail['text_footer'];

  if ($conf_mail['mail_options'])
  {
    return mail($to, $subject, $content, $headers, $options);
  }
  else
  {
    return mail($to, $subject, $content, $headers);
  }
}

// +-----------------------------------------------------------------------+
// | Global Variables
// +-----------------------------------------------------------------------+
$conf_mail = array();

init_conf_mail();

?>