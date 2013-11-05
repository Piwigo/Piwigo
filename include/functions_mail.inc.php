<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

/**
 * Returns the name of the mail sender
 * @return string
 */
function get_mail_sender_name()
{
  global $conf;

  return (empty($conf['mail_sender_name']) ? $conf['gallery_title'] : $conf['mail_sender_name']);
}

/**
 * Returns the email of the mail sender
 * @since 2.6
 * @return string
 */
function get_mail_sender_email()
{
  global $conf;

  return (empty($conf['mail_sender_email']) ? get_webmaster_mail_address() : $conf['mail_sender_email']);
}

/**
 * Returns an array of mail configuration parameters :
 * - send_bcc_mail_webmaster
 * - allow_html_email
 * - use_smtp
 * - smtp_host
 * - smtp_user
 * - smtp_password
 * - smtp_secure
 * - email_webmaster
 * - name_webmaster
 *
 * @return array
 */
function get_mail_configuration()
{
  global $conf;

  $conf_mail = array(
    'send_bcc_mail_webmaster' => $conf['send_bcc_mail_webmaster'],
    'allow_html_email' => $conf['allow_html_email'],
    'mail_theme' => $conf['mail_theme'],
    'use_smtp' => !empty($conf['smtp_host']),
    'smtp_host' => $conf['smtp_host'],
    'smtp_user' => $conf['smtp_user'],
    'smtp_password' => $conf['smtp_password'],
    'smtp_secure' => $conf['smtp_secure'],
    'email_webmaster' => get_mail_sender_email(),
    'name_webmaster' => get_mail_sender_name(),
    );

  return $conf_mail;
}

/**
 * Returns an email address with an associated real name
 * @param string name
 * @param string email
 */
function format_email($name, $email)
{
  $cvt_email = trim(preg_replace('#[\n\r]+#s', '', $email));
  $cvt_name = trim(preg_replace('#[\n\r]+#s', '', $name));

  if ($cvt_name!="")
  {
    $cvt_name = '"'.addcslashes($cvt_name,'"').'"'.' ';
  }

  if (strpos($cvt_email, '<') === false)
  {
    return $cvt_name.'<'.$cvt_email.'>';
  }
  else
  {
    return $cvt_name.$cvt_email;
  }
}

/**
 * Returns the mail and the name from a formatted address
 * @since 2.6
 * @param string|array $input
 * @return array
 */
function unformat_email($input)
{
  if (is_array($input))
  {
    return $input;
  }

  if (preg_match('/(.*)<(.*)>.*/', $input, $matches))
  {
    return array(
      'email' => trim($matches[2]),
      'name' => trim($matches[1]),
      );
  }
  else
  {
    return array(
      'email' => trim($input),
      'name' => '',
      );
  }
}
  

/**
 * Returns an email address list with minimal email string
 * @param string $email_list - comma separated
 * @return string
 */
function get_strict_email_list($email_list)
{
  $result = array();
  $list = explode(',', $email_list);

  foreach ($list as $email)
  {
    if (strpos($email, '<') !== false)
    {
       $email = preg_replace('/.*<(.*)>.*/i', '$1', $email);
    }
    $result[] = trim($email);
  }

  return implode(',', array_unique($result));
}


/**
 * Return an new mail template
 * @param string $email_format - text/html or text/plain
 * @return Template
 */
function &get_mail_template($email_format)
{
  $template = new Template(PHPWG_ROOT_PATH.'themes', 'default', 'template/mail/'.$email_format);
  return $template;
}

/**
 * Switch language to specified language
 * All entries are push on language stack
 * @param string $language
 */
function switch_lang_to($language)
{
  global $switch_lang, $user, $lang, $lang_info, $language_files;

  // explanation of switch_lang
  // $switch_lang['language'] contains data of language
  // $switch_lang['stack'] contains stack LIFO
  // $switch_lang['initialisation'] allow to know if it's first call

  // Treatment with current user
  // Language of current user is saved (it's considered OK on firt call)
  if (!isset($switch_lang['initialisation']) and !isset($switch_lang['language'][$user['language']]))
  {
    $switch_lang['initialisation'] = true;
    $switch_lang['language'][$user['language']]['lang_info'] = $lang_info;
    $switch_lang['language'][$user['language']]['lang'] = $lang;
  }

  // Change current infos
  $switch_lang['stack'][] = $user['language'];
  $user['language'] = $language;

  // Load new data if necessary
  if (!isset($switch_lang['language'][$language]))
  {
    // Re-Init language arrays
    $lang_info = array();
    $lang  = array();

    // language files
    load_language('common.lang', '', array('language'=>$language) );
    // No test admin because script is checked admin (user selected no)
    // Translations are in admin file too
    load_language('admin.lang', '', array('language'=>$language) );
    
    // Reload all plugins files (see load_language declaration)
    if (!empty($language_files))
    {
      foreach ($language_files as $dirname => $files)
        foreach ($files as $filename)
          load_language($filename, $dirname, array('language'=>$language) );
    }
    
    trigger_action('loading_lang');
    load_language('lang', PHPWG_ROOT_PATH.PWG_LOCAL_DIR,
      array('language'=>$language, 'no_fallback'=>true, 'local'=>true)
    );

    $switch_lang['language'][$language]['lang_info'] = $lang_info;
    $switch_lang['language'][$language]['lang'] = $lang;
  }
  else
  {
    $lang_info = $switch_lang['language'][$language]['lang_info'];
    $lang = $switch_lang['language'][$language]['lang'];
  }
}

/**
 * Switch back language pushed with switch_lang_to function
 */
function switch_lang_back()
{
  global $switch_lang, $user, $lang, $lang_info;

  if (count($switch_lang['stack']) > 0)
  {
    // Get last value
    $language = array_pop($switch_lang['stack']);

    // Change current infos
    if (isset($switch_lang['language'][$language]))
    {
      $lang_info = $switch_lang['language'][$language]['lang_info'];
      $lang = $switch_lang['language'][$language]['lang'];
    }
    $user['language'] = $language;
  }
}

/**
 * Returns email of all administrator
 *
 * @return string
 */
/*
 * send en notification email to all administrators
 * if a administrator is doing action,
 * he's be removed to email list
 *
 * @param:
 *   - keyargs_subject: mail subject on l10n_args format
 *   - keyargs_content: mail content on l10n_args format
 *   - send_technical_details: send user IP and browser
 *
 * @return boolean (Ok or not)
 */
function pwg_mail_notification_admins($keyargs_subject, $keyargs_content, $send_technical_details=true)
{
  global $conf, $user;
  
  // Check arguments
  if (empty($keyargs_subject) or empty($keyargs_content))
  {
    return false;
  }

  $return = true;

  $admins = array();

  $query = '
SELECT
    u.'.$conf['user_fields']['username'].' AS username,
    u.'.$conf['user_fields']['email'].' AS mail_address
  FROM '.USERS_TABLE.' AS u
    JOIN '.USER_INFOS_TABLE.' AS i ON i.user_id =  u.'.$conf['user_fields']['id'].'
  WHERE i.status in (\'webmaster\',  \'admin\')
    AND '.$conf['user_fields']['email'].' IS NOT NULL
    AND i.user_id <> '.$user['id'].'
  ORDER BY username
;';

  $datas = pwg_query($query);
  if (!empty($datas))
  {
    while ($admin = pwg_db_fetch_assoc($datas))
    {
      if (!empty($admin['mail_address']))
      {
        $admins[] = format_email($admin['username'], $admin['mail_address']);
      }
    }
  }

  if (count($admins) > 0)
  {
    switch_lang_to(get_default_language());

    $content = l10n_args($keyargs_content)."\n";
    if ($send_technical_details)
    {
      $keyargs_content_admin_info = array(
        get_l10n_args('Connected user: %s', stripslashes($user['username'])),
        get_l10n_args('IP: %s', $_SERVER['REMOTE_ADDR']),
        get_l10n_args('Browser: %s', $_SERVER['HTTP_USER_AGENT'])
        );
      
      $content.= "\n".l10n_args($keyargs_content_admin_info)."\n";
    }

    $return = pwg_mail(
      implode(', ', $admins),
      array(
        'subject' => '['.$conf['gallery_title'].'] '.l10n_args($keyargs_subject),
        'content' => $content,
        'content_format' => 'text/plain',
        'email_format' => 'text/html',
        )
      );
    
    switch_lang_back();
  }

  return $return;
}

/*
 * send en email to user's group
 *
 * @param:
 *   - group_id: mail are sent to group with this Id
 *   - email_format: mail format
 *   - keyargs_subject: mail subject on l10n_args format
 *   - tpl_shortname: short template name without extension
 *   - assign_vars: array used to assign_vars to mail template
 *   - language_selected: send mail only to user with this selected language
 *
 * @return boolean (Ok or not)
 */
function pwg_mail_group(
  $group_id, $email_format, $keyargs_subject,
  $tpl_shortname,
  $assign_vars = array(), $language_selected = '')
{
  // Check arguments
  if
    (
      empty($group_id) or
      empty($email_format) or
      empty($keyargs_subject) or
      empty($tpl_shortname)
    )
  {
    return false;
  }

  global $conf;
  $return = true;

  $query = '
SELECT
  distinct language, theme
FROM
  '.USER_GROUP_TABLE.' as ug
  INNER JOIN '.USERS_TABLE.' as u  ON '.$conf['user_fields']['id'].' = ug.user_id
  INNER JOIN '.USER_INFOS_TABLE.' as ui  ON ui.user_id = ug.user_id
WHERE
        '.$conf['user_fields']['email'].' IS NOT NULL
    AND group_id = '.$group_id;

  if (!empty($language_selected))
  {
    $query .= '
    AND language = \''.$language_selected.'\'';
  }

    $query .= '
;';

  $result = pwg_query($query);

  if (pwg_db_num_rows($result) > 0)
  {
    $list = array();
    while ($row = pwg_db_fetch_assoc($result))
    {
      $list[] = $row;
    }

    foreach ($list as $elem)
    {
      $query = '
SELECT
  u.'.$conf['user_fields']['username'].' as username,
  u.'.$conf['user_fields']['email'].' as mail_address
FROM
  '.USER_GROUP_TABLE.' as ug
  INNER JOIN '.USERS_TABLE.' as u  ON '.$conf['user_fields']['id'].' = ug.user_id
  INNER JOIN '.USER_INFOS_TABLE.' as ui  ON ui.user_id = ug.user_id
WHERE
        '.$conf['user_fields']['email'].' IS NOT NULL
    AND group_id = '.$group_id.'
    AND language = \''.$elem['language'].'\'
    AND theme = \''.$elem['theme'].'\'
;';

      $result = pwg_query($query);

      if (pwg_db_num_rows($result) > 0)
      {
        $Bcc = array();
        while ($row = pwg_db_fetch_assoc($result))
        {
          if (!empty($row['mail_address']))
          {
            $Bcc[] = format_email(stripslashes($row['username']), $row['mail_address']);
          }
        }

        if (count($Bcc) > 0)
        {
          switch_lang_to($elem['language']);

          $mail_template = get_mail_template($email_format, $elem['theme']);
          $mail_template->set_filename($tpl_shortname, $tpl_shortname.'.tpl');

          $mail_template->assign(
            trigger_event('mail_group_assign_vars', $assign_vars));

          $return = pwg_mail
          (
            '',
            array
            (
              'Bcc' => $Bcc,
              'subject' => l10n_args($keyargs_subject),
              'email_format' => $email_format,
              'content' => $mail_template->parse($tpl_shortname, true),
              'content_format' => $email_format,
              'theme' => $elem['theme']
            )
          ) and $return;

          switch_lang_back();
        }
      }
    }
  }

  return $return;
}

/**
 * sends an email, using Piwigo specific informations
 *
 * @param string|string[] $to
 * @param array $args
 *       o from: sender [default value webmaster email]
 *       o Cc: array of carbon copy receivers of the mail. [default value empty]
 *       o Bcc: array of blind carbon copy receivers of the mail. [default value empty]
 *       o subject  [default value 'Piwigo']
 *       o content: content of mail    [default value '']
 *       o content_format: format of mail content  [default value 'text/plain']
 *       o email_format: global mail format  [default value $conf_mail['default_email_format']]
 *       o theme: theme to use [default value $conf_mail['mail_theme']]
 *       o mail_title: main title of the mail [default value $conf['gallery_title']]
 *       o mail_subtitle: subtitle of the mail [default value subject]
 *
 * @return boolean
 */
function pwg_mail($to, $args = array())
{
  global $conf, $conf_mail, $lang_info, $page;

  if (empty($to) and empty($args['Cc']) and empty($args['Bcc']))
  {
    return true;
  }

  if (!isset($conf_mail))
  {
    $conf_mail = get_mail_configuration();
  }

  include_once(PHPWG_ROOT_PATH.'include/phpmailer/class.phpmailer.php');

  $mail = new PHPMailer;

  $recipients = !is_array($to) ? explode(',', $to) : $to;
  foreach ($recipients as $recipient)
  {
    $recipient = unformat_email($recipient);
    $mail->addAddress($recipient['email'], $recipient['name']);
  }

  $mail->WordWrap = 76;
  $mail->CharSet = 'UTF-8';
  
  // Compute root_path in order have complete path
  set_make_full_url();

  if (empty($args['from']))
  {
    $from = array(
      'email' => $conf_mail['email_webmaster'],
      'name' => $conf_mail['name_webmaster'],
      );
  }
  else
  {
    $from = unformat_email($args['from']);
  }
  $mail->setFrom($from['email'], $from['name']);
  $mail->addReplyTo($from['email'], $from['name']);

  // Subject
  if (empty($args['subject']))
  {
    $args['subject'] = 'Piwigo';
  }
  $args['subject'] = trim(preg_replace('#[\n\r]+#s', '', $args['subject']));
  $mail->Subject = $args['subject'];

  // Cc
  if (!empty($args['Cc']))
  {
    foreach ($args['Cc'] as $cc)
    {
      $cc = unformat_email($cc);
      $mail->addCC($cc['email'], $cc['name']);
    }
  }

  // Bcc
  if ($conf_mail['send_bcc_mail_webmaster'])
  {
    $args['Bcc'][] = get_webmaster_mail_address();
  }
  if (!empty($args['Bcc']))
  {
    foreach ($args['Bcc'] as $bcc)
    {
      $bcc = unformat_email($bcc);
      $mail->addBCC($bcc['email'], $bcc['name']);
    }
  }

  // theme
  if (empty($args['theme']) or !in_array($args['theme'], array('clear','dark')))
  {
    $args['theme'] = $conf_mail['mail_theme'];
  }

  // content
  if (!isset($args['content']))
  {
    $args['content'] = '';
  }
  if (!isset($args['mail_title']))
  {
    $args['mail_title'] = $conf['gallery_title'];
  }
  if (!isset($args['mail_subtitle']))
  {
    $args['mail_subtitle'] = $args['subject'];
  }

  // content type
  if (empty($args['content_format']))
  {
    $args['content_format'] = 'text/plain';
  }

  $content_type_list = array();
  if ($conf_mail['allow_html_email'] and @$args['email_format'] != 'text/plain')
  {
    $content_type_list[] = 'text/html';
  }
  $content_type_list[] = 'text/plain';

  $contents = array();
  foreach ($content_type_list as $content_type)
  {
    // key compose of indexes witch allow to cache mail data
    $cache_key = $content_type.'-'.$lang_info['code'];
    $cache_key.= '-'.crc32(@$args['mail_title'] . @$args['mail_subtitle']);

    if (!isset($conf_mail[$cache_key]))
    {
      // instanciate a new Template
      if (!isset($conf_mail[$cache_key]['theme']))
      {
        $conf_mail[$cache_key]['theme'] = get_mail_template($content_type);
        trigger_action('before_parse_mail_template', $cache_key, $content_type);
      }

      $conf_mail[$cache_key]['theme']->set_filename('mail_header', 'header.tpl');
      $conf_mail[$cache_key]['theme']->set_filename('mail_footer', 'footer.tpl');

      $conf_mail[$cache_key]['theme']->assign(
        array(
          'GALLERY_URL' => get_gallery_home_url(),
          'GALLERY_TITLE' => isset($page['gallery_title']) ? $page['gallery_title'] : $conf['gallery_title'],
          'VERSION' => $conf['show_version'] ? PHPWG_VERSION : '',
          'PHPWG_URL' => defined('PHPWG_URL') ? PHPWG_URL : '',
          'CONTENT_ENCODING' => get_pwg_charset(),
          'CONTACT_MAIL' => $conf_mail['email_webmaster'],
          'MAIL_TITLE' => $args['mail_title'],
          'MAIL_SUBTITLE' => $args['mail_subtitle'],
          )
        );

      if ($content_type == 'text/html')
      {
        if ($conf_mail[$cache_key]['theme']->smarty->template_exists('global-mail-css.tpl'))
        {
          $conf_mail[$cache_key]['theme']->set_filename('css', 'global-mail-css.tpl');
          $conf_mail[$cache_key]['theme']->assign_var_from_handle('GLOBAL_MAIL_CSS', 'css');
        }

        if ($conf_mail[$cache_key]['theme']->smarty->template_exists('mail-css-'. $args['theme'] .'.tpl'))
        {
          $conf_mail[$cache_key]['theme']->set_filename('css', 'mail-css-'. $args['theme'] .'.tpl');
          $conf_mail[$cache_key]['theme']->assign_var_from_handle('MAIL_CSS', 'css');
        }
      }

      $conf_mail[$cache_key]['header'] = $conf_mail[$cache_key]['theme']->parse('mail_header', true);
      $conf_mail[$cache_key]['footer'] = $conf_mail[$cache_key]['theme']->parse('mail_footer', true);
    }

    // Header
    $contents[$content_type] = $conf_mail[$cache_key]['header'];

    // Content
    if ($args['content_format'] == 'text/plain' and $content_type == 'text/html')
    {
      // convert plain text to html
      $contents[$content_type].=
        '<p>'.
        nl2br(
          preg_replace(
            '/(https?:\/\/([-\w\.]+[-\w])+(:\d+)?(\/([\w\/_\.\#-]*(\?\S+)?[^\.\s])?)?)/i',
            '<a href="$1">$1</a>',
            htmlspecialchars($args['content'])
            )
          ).
        '</p>';
    }
    else if ($args['content_format'] == 'text/html' and $content_type == 'text/plain')
    {
      // convert html text to plain text
      $contents[$content_type].= strip_tags($args['content']);
    }
    else
    {
      $contents[$content_type].= $args['content'];
    }

    // Footer
    $contents[$content_type].= $conf_mail[$cache_key]['footer'];
  }

  // Undo Compute root_path in order have complete path
  unset_make_full_url();

  // Send content to PHPMailer
  if (isset($contents['text/html']))
  {
    $mail->isHTML(true);
    $mail->Body = move_css_to_body($contents['text/html']);
    
    if (isset($contents['text/plain']))
    {
      $mail->AltBody = $contents['text/plain'];
    }
  }
  else
  {
    $mail->isHTML(false);
    $mail->Body = $contents['text/plain'];
  }

  if ($conf_mail['use_smtp'])
  {
    // now we need to split port number
    if (strpos($conf_mail['smtp_host'], ':') !== false)
    {
      list($smtp_host, $smtp_port) = explode(':', $conf_mail['smtp_host']);
    }
    else
    {
      $smtp_host = $conf_mail['smtp_host'];
      $smtp_port = 25;
    }

    $mail->IsSMTP();

    // enables SMTP debug information (for testing) 2 - debug, 0 - no message
    $mail->SMTPDebug = 0;
    
    $mail->Host = $smtp_host;
    $mail->Port = $smtp_port;

    if (!empty($conf_mail['smtp_secure']) and in_array($conf_mail['smtp_secure'], array('ssl', 'tls')))
    {
      $mail->SMTPSecure = $conf_mail['smtp_secure'];
    }
    
    if (!empty($conf_mail['smtp_user']))
    {
      $mail->SMTPAuth = true;
      $mail->Username = $conf_mail['smtp_user'];
      $mail->Password = $conf_mail['smtp_password'];
    }
  }

  $ret = true;
  $pre_result = trigger_event('before_send_mail', true, $to, $args, $mail);

  if ($pre_result)
  {
    $ret = $mail->send();
    if (!$ret and is_admin())
    {
      trigger_error('Mailer Error: ' . $mail->ErrorInfo, E_USER_WARNING);
    }
  }

  return $ret;
}

/**
 * @deprecated 2.6
 */
function pwg_send_mail($result, $to, $subject, $content, $headers)
{
  trigger_error('pwg_send_mail function is deprecated', E_USER_NOTICE);
  
  if (!$result)
  {
    return pwg_mail($to, array(
        'content' => $content,
        'subject' => $subject,
      ));
  }
  else
  {
    return $result;
  }
}

/**
 * @deprecated 2.6
 */
function move_ccs_rules_to_body($content)
{
  trigger_error('move_ccs_rules_to_body function is deprecated, use move_css_to_body', E_USER_NOTICE);
  
  return move_css_to_body($content);
}

/**
 * Moves CSS rules contained in the <style> tag to inline CSS
 * (for compatibility with Gmail and such clients)
 * @since 2.6
 * @param string $content
 * @return string
 */
function move_css_to_body($content)
{
  include_once(PHPWG_ROOT_PATH.'include/emogrifier.class.php');

  $e = new Emogrifier($content);
  $e->preserveStyleTag = true;
  return $e->emogrify();
}

/**
 * Saves a copy of the mail if _data/tmp
 * @param boolean $result
 * @param string $to
 * @param array $args
 * @param PHPMailer $mail
 * @return boolean $result
 */
function pwg_send_mail_test($result, $to, $args, $mail)
{
  global $conf, $user, $lang_info;
  
  $dir = PHPWG_ROOT_PATH.$conf['data_location'].'tmp';
  if (mkgetdir($dir, MKGETDIR_DEFAULT&~MKGETDIR_DIE_ON_ERROR))
  {
    $filename = $dir.'/mail.'.stripslashes($user['username']).'.'.$lang_info['code'].'.'.$args['theme'].'-'.date('YmdHis');
    if ($args['content_format'] == 'text/plain')
    {
      $filename .= '.txt';
    }
    else
    {
      $filename .= '.html';
    }
    
    $file = fopen($filename, 'w+');
    fwrite($file, implode(', ', $to) ."\n");
    fwrite($file, $mail->Subject ."\n");
    fwrite($file, $mail->createHeader() ."\n");
    fwrite($file, $mail->createBody());
    fclose($file);
  }
  
  return $result;
}

if ($conf['debug_mail'])
{
  add_event_handler('before_send_mail', 'pwg_send_mail_test', EVENT_HANDLER_PRIORITY_NEUTRAL+10, 4);
}

trigger_action('functions_mail_included');

?>