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

/**
 * - Extract mail fonctions of password.php
 * - Modify pwg_mail (add pararameters + news fonctionnalities)
 * - Var conf_mail, function get_mail_configuration, format_email, pwg_mail
 */

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/*
 * Returns an array of mail configuration parameters :
 *
 * - mail_options: see $conf['mail_options']
 * - send_bcc_mail_webmaster: see $conf['send_bcc_mail_webmaster']
 * - email_webmaster: mail corresponding to $conf['webmaster_id']
 * - formated_email_webmaster: the name of webmaster is $conf['gallery_title']
 * - text_footer: PhpWebGallery and version
 *
 * @return array
 */
function get_mail_configuration()
{
  global $conf;

  $conf_mail = array(
    'mail_options' => $conf['mail_options'],
    'send_bcc_mail_webmaster' => $conf['send_bcc_mail_webmaster'],
    );

  // we have webmaster id among user list, what's his email address ?
  $conf_mail['email_webmaster'] = get_webmaster_mail_address();

  // name of the webmaster is the title of the gallery
  $conf_mail['formated_email_webmaster'] =
    format_email($conf['gallery_title'], $conf_mail['email_webmaster']);

  // what to display at the bottom of each mail ?
  $conf_mail['text_footer'] =
    "\n\n-- \nPhpWebGallery ".($conf['show_version'] ? PHPWG_VERSION : '');
  
  return $conf_mail;
}

/**
 * Returns an email address with an associated real name
 *
 * @param string name
 * @param string email
 */
function format_email($name, $email)
{
  $cvt7b_name = str_translate_to_ascii7bits($name);

  if (strpos($email, '<') === false)
  {
    return $cvt7b_name.' <'.$email.'>';
  }
  else
  {
    return $cvt7b_name.$email;
  }
}

/**
 * sends an email, using PhpWebGallery specific informations
 */
function pwg_mail($to, $from = '', $subject = 'PhpWebGallery', $infos = '')
{
  global $conf, $conf_mail;

  $cvt7b_subject = str_translate_to_ascii7bits($subject);

  if (!isset($conf_mail))
  {
    $conf_mail = get_mail_configuration();
  }

  $to = format_email('', $to);

  if ($from == '')
  {
    $from = $conf_mail['formated_email_webmaster'];
  }
  else
  {
    $from = format_email('', $from);
  }

  $headers = 'From: '.$from."\n";
  $headers.= 'Reply-To: '.$from."\n";
  
  if ($conf_mail['send_bcc_mail_webmaster'])
  {
    $headers.= 'Bcc: '.$conf_mail['formated_email_webmaster']."\n";
  }
  
  $content = $infos;
  $content.= $conf_mail['text_footer'];

  if ($conf_mail['mail_options'])
  {
    $options = '-f '.$from;
    
    return mail($to, $cvt7b_subject, $content, $headers, $options);
  }
  else
  {
    return mail($to, $cvt7b_subject, $content, $headers);
  }
}

?>
