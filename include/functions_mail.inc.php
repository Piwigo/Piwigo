<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
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
    'default_email_format' => $conf['default_email_format']
    );

  // we have webmaster id among user list, what's his email address ?
  $conf_mail['email_webmaster'] = get_webmaster_mail_address();

  // name of the webmaster is the title of the gallery
  $conf_mail['formated_email_webmaster'] =
    format_email($conf['gallery_title'], $conf_mail['email_webmaster']);

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
  global $conf;

  if ($conf['enabled_format_email'])
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
  else
  {
    return $email;
  }
}

/**
 * Returns an new mail template
 *
 * @param none
 */
function get_mail_template($email_format)
{
  global $user;

  $mail_template = new Template(PHPWG_ROOT_PATH.'template/'.$user['template'], $user['theme']);
  $mail_template->set_rootdir(PHPWG_ROOT_PATH.'template/'.$user['template'].'/mail/'.$email_format);

  return $mail_template;
}

/**
 * Return string email format (html or not) 
 *
 * @param string format
 */
function get_str_email_format($is_html)
{
  return ($is_html ? 'text/html' : 'text/plain');
}

/**
 * sends an email, using PhpWebGallery specific informations
 */
function pwg_mail($to, $from = '', $subject = 'PhpWebGallery', $infos = '', $format_infos = 'text/plain', $email_format = null)
{
  global $conf, $conf_mail, $lang_info, $page, $user;

  $cvt7b_subject = str_translate_to_ascii7bits($subject);

  if (!isset($conf_mail))
  {
    $conf_mail = get_mail_configuration();
  }

  if (is_null($email_format))
  {
    $email_format = $conf_mail['default_email_format'];
  }

  if (($format_infos == 'text/html') and ($email_format == 'text/plain'))
  {
    // Todo find function to convert html text to plain text
    return false;
  }

  // Compute root_path in order have complete path
  if ($email_format == 'text/html')
  {
    set_make_full_url();
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
  $headers.= 'Content-Type: '.$email_format.';format=flowed;charset="'.$lang_info['charset'].'";';
  $headers.= 'reply-type=original'."\n";

  if ($conf_mail['send_bcc_mail_webmaster'])
  {
    $headers.= 'Bcc: '.$conf_mail['formated_email_webmaster']."\n";
  }

  $content = '';

  if (!isset($conf_mail[$email_format][$lang_info['charset']][$user['template']][$user['theme']]))
  {
    if (!isset($mail_template))
    {
      $mail_template = get_mail_template($email_format);
    }

    $mail_template->set_filename('mail_header', 'header.tpl');
    $mail_template->set_filename('mail_footer', 'footer.tpl');

    $mail_template->assign_vars(
      array(
        //Header
        'CONTENT_ENCODING' => $lang_info['charset'],
        'LANG' => $lang_info['code'],
        'DIR' => $lang_info['direction'],
        
        // Footer
        'GALLERY_URL' =>
          isset($page['gallery_url']) ?
                $page['gallery_url'] : $conf['gallery_url'],
        'GALLERY_TITLE' =>
          isset($page['gallery_title']) ?
                $page['gallery_title'] : $conf['gallery_title'],
        'VERSION' => $conf['show_version'] ? PHPWG_VERSION : '',
        'PHPWG_URL' => PHPWG_URL,

        'TITLE_MAIL' => urlencode(l10n('title_send_mail')),
        'MAIL' => get_webmaster_mail_address()
        ));

    $mail_template->set_filename('mail_css_default_template', 'default-layout-mail-css.tpl');
    $mail_template->assign_var_from_handle('MAIL_CSS_DEFAULT_TEMPLATE', 'mail_css_default_template');

    $old_root = $mail_template->root;

    $mail_template->root = PHPWG_ROOT_PATH.'template/'.$user['template'].'/theme/'.$user['theme'];
    if (is_file($mail_template->root.'/layout-mail-css.tpl'))
    {
      $mail_template->set_filename('mail_css_theme', 'layout-mail-css.tpl');
      $mail_template->assign_var_from_handle('MAIL_CSS_THEME', 'mail_css_theme');
    }

    $mail_template->root = PHPWG_ROOT_PATH.'template-common';
    if (is_file($mail_template->root.'/local-layout-mail-css.tpl'))
    {
      $mail_template->set_filename('mail_css_local_template', 'local-layout-mail-css.tpl');
      $mail_template->assign_var_from_handle('MAIL_CSS_LOCAL_COMMON', 'mail_css_local_template');
    }

    $mail_template->root = $old_root;
    if (is_file($mail_template->root.'/local-layout-mail-css.tpl'))
    {
      $mail_template->set_filename('mail_css_local_template', 'local-layout-mail-css.tpl');
      $mail_template->assign_var_from_handle('MAIL_CSS_LOCAL_TEMPLATE', 'mail_css_local_template');
    }

    // what are displayed on the header of each mail ?
    $conf_mail[$email_format]
      [$lang_info['charset']]
      [$user['template']][$user['theme']]['header'] =
        $mail_template->parse('mail_header', true);

    // what are displayed on the footer of each mail ?
    $conf_mail[$email_format]
      [$lang_info['charset']]
      [$user['template']][$user['theme']]['footer'] =
        $mail_template->parse('mail_footer', true);
  }

  $content.= $conf_mail[$email_format]
              [$lang_info['charset']]
              [$user['template']][$user['theme']]['header'];

  if (($format_infos == 'text/plain') and ($email_format == 'text/html'))
  {
    $content.= '<pre>'.htmlentities($infos).'</pre>';
  }
  else
  {
    $content.= $infos;
  }

  $content.= $conf_mail[$email_format]
              [$lang_info['charset']]
              [$user['template']][$user['theme']]['footer'];
  
   // Undo Compute root_path in order have complete path
  if ($email_format == 'text/html')
  {
    unset_make_full_url();
  }

  /*Testing block
  {
    global $user;
    @mkdir(PHPWG_ROOT_PATH.'testmail');
    $filename = PHPWG_ROOT_PATH.'testmail/mail.'.$user['username'];
    if ($format_infos == 'text/plain')
    {
      $filename .= '.txt';
    }
    else
    {
      $filename .= '.html';
    }
    $file = fopen($filename, 'w+');
    fwrite($file, $content);
    fclose($file);
    return true;
  }*/

  if ($conf_mail['mail_options'])
  {
    $options = '-f '.$conf_mail['email_webmaster'];
    
    return mail($to, $cvt7b_subject, $content, $headers, $options);
  }
  else
  {
    return mail($to, $cvt7b_subject, $content, $headers);
  }
}

?>
