<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\mail
 */


/**
 * Returns the name of the mail sender
 *
 * @return string
 */
function get_mail_sender_name()
{
  global $conf;

  return (empty($conf['mail_sender_name']) ? $conf['gallery_title'] : $conf['mail_sender_name']);
}

/**
 * Returns the email of the mail sender
 *
 * @since 2.6
 * @return string
 */
function get_mail_sender_email()
{
  global $conf;

  return (empty($conf['mail_sender_email']) ? get_webmaster_mail_address() : $conf['mail_sender_email']);
}

/**
 * Returns an array of mail configuration parameters.
 * - send_bcc_mail_webmaster
 * - mail_allow_html
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
    'mail_allow_html' => $conf['mail_allow_html'],
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
 * Returns an email address with an associated real name.
 * Can return either:
 *    - email@domain.com
 *    - name <email@domain.com>
 *
 * @param string $name
 * @param string $email
 * @return string
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
 * Returns the email and the name from a formatted address.
 * @since 2.6
 *
 * @param string|string[] $input - if is an array must contain email[, name]
 * @return array email, name
 */
function unformat_email($input)
{
  if (is_array($input))
  {
    if (!isset($input['name']))
    {
      $input['name'] = '';
    }
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
 * Return a clean array of hashmaps (email, name) removing duplicates.
 * It accepts various inputs:
 *    - comma separated list
 *    - array of emails
 *    - single hashmap (email[, name])
 *    - array of incomplete hashmaps
 * @since 2.6
 *
 * @param mixed $data
 * @return string[][]
 */
function get_clean_recipients_list($data)
{
  if (empty($data))
  {
    return array();
  }
  else if (is_array($data))
  {
    $values = array_values($data);
    if (!is_array($values[0]))
    {
      $keys = array_keys($data);
      if (is_int($keys[0]))
      { // simple array of emails
        foreach ($data as &$item)
        {
          $item = array(
            'email' => trim($item),
            'name' => '',
            );
        }
        unset($item);
      }
      else
      { // hashmap of one recipient
        $data = array(unformat_email($data));
      }
    }
    else
    { // array of hashmaps
      $data = array_map('unformat_email', $data);
    }
  }
  else
  {
    $data = explode(',', $data);
    $data = array_map('unformat_email', $data);
  }

  $existing = array();
  foreach ($data as $i => $entry)
  {
    if (isset($existing[ $entry['email'] ]))
    {
      unset($data[$i]);
    }
    else
    {
      $existing[ $entry['email'] ] = true;
    }
  }

  return array_values($data);
}

/**
 * Returns an email address list with minimal email string.
 * @deprecated 2.6
 *
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
 * Return an new mail template.
 *
 * @param string $email_format - text/html or text/plain
 * @return Template
 */
function &get_mail_template($email_format)
{
  $template = new Template(PHPWG_ROOT_PATH.'themes', 'default', 'template/mail/'.$email_format);
  return $template;
}

/**
 * Return string email format (text/html or text/plain).
 *
 * @param bool $is_html
 * @return string
 */
function get_str_email_format($is_html)
{
  return ($is_html ? 'text/html' : 'text/plain');
}

/**
 * Switch language to specified language.
 * All entries are push on language stack
 *
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
      {
        foreach ($files as $filename => $options)
        {
          $options['language'] = $language;
          load_language($filename, $dirname, $options);
        }
      }
    }
    
    trigger_notify('loading_lang');
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
 * Switch back language pushed with switch_lang_to() function.
 * @see switch_lang_to()
 * Language files are not reloaded
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
 * Send a notification email to all administrators.
 * current user (if admin) is not notified
 *
 * @param string|array $subject
 * @param string|array $content
 * @param boolean $send_technical_details - send user IP and browser
 * @return boolean
 */
function pwg_mail_notification_admins($subject, $content, $send_technical_details=true)
{
  if (empty($subject) or empty($content))
  {
    return false;
  }

  global $conf, $user;

  if (is_array($subject) or is_array($content))
  {
    switch_lang_to(get_default_language());

    if (is_array($subject))
    {
      $subject = l10n_args($subject);
    }
    if (is_array($content))
    {
      $content = l10n_args($content);
    }

    switch_lang_back();
  }

  $tpl_vars = array();
  if ($send_technical_details)
  {
    $tpl_vars['TECHNICAL'] = array(
      'username' => stripslashes($user['username']),
      'ip' => $_SERVER['REMOTE_ADDR'],
      'user_agent' => $_SERVER['HTTP_USER_AGENT'],
      );
  }

  return pwg_mail_admins(
    array(
      'subject' => '['. $conf['gallery_title'] .'] '. $subject,
      'mail_title' => $conf['gallery_title'],
      'mail_subtitle' => $subject,
      'content' => $content,
      'content_format' => 'text/plain',
      ),
    array(
      'filename' => 'notification_admin',
      'assign' => $tpl_vars,
      )
    );
}

/**
 * Send a email to all administrators.
 * current user (if admin) is excluded
 * @see pwg_mail()
 * @since 2.6
 *
 * @param array $args - as in pwg_mail()
 * @param array $tpl - as in pwg_mail()
 * @return boolean
 */
function pwg_mail_admins($args=array(), $tpl=array(), $exclude_current_user=true, $only_webmasters=false)
{
  if (empty($args['content']) and empty($tpl))
  {
    return false;
  }

  global $conf, $user;
  $return = true;

  $user_statuses = array('webmaster');
  if (!$only_webmasters)
  {
    $user_statuses[] = 'admin';
  }

  // get admins (except ourself)
  $query = '
SELECT
    u.'.$conf['user_fields']['username'].' AS name,
    u.'.$conf['user_fields']['email'].' AS email
  FROM '.USERS_TABLE.' AS u
    JOIN '.USER_INFOS_TABLE.' AS i
    ON i.user_id =  u.'.$conf['user_fields']['id'].'
  WHERE i.status in (\''.implode("','", $user_statuses).'\')
    AND u.'.$conf['user_fields']['email'].' IS NOT NULL';

  if ($exclude_current_user)
  {
    $query.= '
    AND i.user_id <> '.$user['id'];
  }

  $query.= '
  ORDER BY name
;';
  $admins = array_from_query($query);

  if (empty($admins))
  {
    return $return;
  }

  switch_lang_to(get_default_language());

  $return = pwg_mail($admins, $args, $tpl);

  switch_lang_back();

  return $return;
}

/**
 * Send an email to a group.
 * @see pwg_mail()
 *
 * @param int $group_id
 * @param array $args - as in pwg_mail()
 *       o language_selected: filters users of the group by language [default value empty]
 * @param array $tpl - as in pwg_mail()
 * @return boolean
 */
function pwg_mail_group($group_id, $args=array(), $tpl=array())
{  
  if (empty($group_id) or ( empty($args['content']) and empty($tpl) ))
  {
    return false;
  }

  global $conf;
  $return = true;

  // get distinct languages of targeted users
  $query = '
SELECT DISTINCT language
  FROM '.USER_GROUP_TABLE.' AS ug
    INNER JOIN '.USERS_TABLE.' AS u
    ON '.$conf['user_fields']['id'].' = ug.user_id
    INNER JOIN '.USER_INFOS_TABLE.' AS ui
    ON ui.user_id = ug.user_id
  WHERE group_id = '.$group_id.'
    AND '.$conf['user_fields']['email'].' <> ""';
  if (!empty($args['language_selected']))
  {
    $query .= '
    AND language = \''.$args['language_selected'].'\'';
  }

    $query .= '
;';
  $languages = array_from_query($query, 'language');

  if (empty($languages))
  {
    return $return;
  }

  foreach ($languages as $language)
  {
    // get subset of users in this group for a specific language
    $query = '
SELECT
    ui.user_id,
    ui.status,
    u.'.$conf['user_fields']['username'].' AS name,
    u.'.$conf['user_fields']['email'].' AS email
  FROM '.USER_GROUP_TABLE.' AS ug
    INNER JOIN '.USERS_TABLE.' AS u
    ON '.$conf['user_fields']['id'].' = ug.user_id
    INNER JOIN '.USER_INFOS_TABLE.' AS ui
    ON ui.user_id = ug.user_id
  WHERE group_id = '.$group_id.'
    AND '.$conf['user_fields']['email'].' <> ""
    AND language = \''.$language.'\'
;';
    $users = array_from_query($query);

    if (empty($users))
    {
      continue;
    }

    switch_lang_to($language);

    foreach ($users as $u)
    {
      $authkey = create_user_auth_key($u['user_id'], $u['status']);
      
      $user_tpl = $tpl;

      if ($authkey !== false)
      {
        $user_tpl['assign']['LINK'] = add_url_params($tpl['assign']['LINK'], array('auth' => $authkey['auth_key']));

        if (isset($user_tpl['assign']['IMG']['link']))
        {
          $user_tpl['assign']['IMG']['link'] = add_url_params(
            $user_tpl['assign']['IMG']['link'],
            array('auth' => $authkey['auth_key'])
            );
        }
      }

      $user_args = $args;
      if ($authkey !== false)
      {
        $user_args['auth_key'] = $authkey['auth_key'];
      }

      $return &= pwg_mail($u['email'], $user_args, $user_tpl);
    }

    switch_lang_back();
  }

  return $return;
}

/**
 * Sends an email, using Piwigo specific informations.
 *
 * @param string|array $to
 * @param array $args
 *       o from: sender [default value webmaster email]
 *       o Cc: array of carbon copy receivers of the mail. [default value empty]
 *       o Bcc: array of blind carbon copy receivers of the mail. [default value empty]
 *       o subject [default value 'Piwigo']
 *       o content: content of mail [default value '']
 *       o content_format: format of mail content [default value 'text/plain']
 *       o email_format: global mail format [default value $conf_mail['default_email_format']]
 *       o theme: theme to use [default value $conf_mail['mail_theme']]
 *       o mail_title: main title of the mail [default value $conf['gallery_title']]
 *       o mail_subtitle: subtitle of the mail [default value subject]
 *       o auth_key: authentication key to add on footer link [default value null]
 * @param array $tpl - use these options to define a custom content template file
 *       o filename
 *       o dirname (optional)
 *       o assign (optional)
 *
 * @return boolean
 */
function pwg_mail($to, $args=array(), $tpl=array())
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

  // PHPMailer autoloader shows warnings with PHP 7.2. Solution to upgrade to PHPMailer 6
  // implies requiring PHP 5.5, which is bigger than current requirement for Piwigo.
  //
  // include_once(PHPWG_ROOT_PATH.'include/phpmailer/PHPMailerAutoload.php');
  //
  // replace by direct include of the classes:
  include_once(PHPWG_ROOT_PATH.'include/phpmailer/class.smtp.php');
  include_once(PHPWG_ROOT_PATH.'include/phpmailer/class.phpmailer.php');

  $mail = new PHPMailer;

  foreach (get_clean_recipients_list($to) as $recipient)
  {
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
    foreach (get_clean_recipients_list($args['Cc']) as $recipient)
    {
      $mail->addCC($recipient['email'], $recipient['name']);
    }
  }

  // Bcc
  $Bcc = get_clean_recipients_list(@$args['Bcc']);
  if ($conf_mail['send_bcc_mail_webmaster'])
  {
    $Bcc[] = array(
      'email' => get_webmaster_mail_address(),
      'name' => '',
      );
  }
  if (!empty($Bcc))
  {
    foreach ($Bcc as $recipient)
    {
      $mail->addBCC($recipient['email'], $recipient['name']);
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
  
  // try to decompose subject like "[....] ...."
  if (!isset($args['mail_title']) and !isset($args['mail_subtitle']))
  {
    if (preg_match('#^\[(.*)\](.*)$#',  $args['subject'], $matches))
    {
      $args['mail_title'] = $matches[1];
      $args['mail_subtitle'] = $matches[2];
    }
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
  if ($conf_mail['mail_allow_html'] and @$args['email_format'] != 'text/plain')
  {
    $content_type_list[] = 'text/html';
  }
  $content_type_list[] = 'text/plain';

  $contents = array();
  foreach ($content_type_list as $content_type)
  {
    // key compose of indexes witch allow to cache mail data
    $cache_key = $content_type.'-'.$lang_info['code'];
    if (!empty($args['auth_key']))
    {
      $cache_key.= '-'.$args['auth_key'];
    }

    if (!isset($conf_mail[$cache_key]))
    {
      // instanciate a new Template
      if (!isset($conf_mail[$cache_key]['theme']))
      {
        $conf_mail[$cache_key]['theme'] = get_mail_template($content_type);
        trigger_notify('before_parse_mail_template', $cache_key, $content_type);
      }
      $template = &$conf_mail[$cache_key]['theme'];

      $template->set_filename('mail_header', 'header.tpl');
      $template->set_filename('mail_footer', 'footer.tpl');

      $add_url_params = array();
      if (!empty($args['auth_key']))
      {
        $add_url_params['auth'] = $args['auth_key'];
      }

      $template->assign(
        array(
          'GALLERY_URL' => add_url_params(get_gallery_home_url(), $add_url_params),
          'GALLERY_TITLE' => isset($page['gallery_title']) ? $page['gallery_title'] : $conf['gallery_title'],
          'VERSION' => $conf['show_version'] ? PHPWG_VERSION : '',
          'PHPWG_URL' => defined('PHPWG_URL') ? PHPWG_URL : '',
          'CONTENT_ENCODING' => get_pwg_charset(),
          'CONTACT_MAIL' => $conf_mail['email_webmaster'],
          )
        );

      if ($content_type == 'text/html')
      {
        if ($template->smarty->templateExists('global-mail-css.tpl'))
        {
          $template->set_filename('global-css', 'global-mail-css.tpl');
          $template->assign_var_from_handle('GLOBAL_MAIL_CSS', 'global-css');
        }

        if ($template->smarty->templateExists('mail-css-'. $args['theme'] .'.tpl'))
        {
          $template->set_filename('css', 'mail-css-'. $args['theme'] .'.tpl');
          $template->assign_var_from_handle('MAIL_CSS', 'css');
        }
      }
    }
    
    $template = &$conf_mail[$cache_key]['theme'];
    $template->assign(
      array(
        'MAIL_TITLE' => $args['mail_title'],
        'MAIL_SUBTITLE' => $args['mail_subtitle'],
        )
      );

    // Header
    $contents[$content_type] = $template->parse('mail_header', true);

    // Content
    // Stored in a temp variable, if a content template is used it will be assigned
    // to the $CONTENT template variable, otherwise it will be appened to the mail
    if ($args['content_format'] == 'text/plain' and $content_type == 'text/html')
    {
      // convert plain text to html
      $mail_content =
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
      $mail_content = strip_tags($args['content']);
    }
    else
    {
      $mail_content = $args['content'];
    }

    // Runtime template
    if (isset($tpl['filename']))
    {
      if (isset($tpl['dirname']))
      {
        $template->set_template_dir($tpl['dirname'] .'/'. $content_type);
      }
      if ($template->smarty->templateExists($tpl['filename'] .'.tpl'))
      {
        $template->set_filename($tpl['filename'], $tpl['filename'] .'.tpl');
        if (!empty($tpl['assign']))
        {
          $template->assign($tpl['assign']);
        }
        $template->assign('CONTENT', $mail_content);
        $contents[$content_type].= $template->parse($tpl['filename'], true);
      }
      else
      {
        $contents[$content_type].= $mail_content;
      }
    }
    else
    {
      $contents[$content_type].= $mail_content;
    }

    // Footer
    $contents[$content_type].= $template->parse('mail_footer', true);
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
  $pre_result = trigger_change('before_send_mail', true, $to, $args, $mail);

  if ($pre_result)
  {
    $ret = $mail->send();
    if (!$ret and (!ini_get('display_errors') or is_admin()))
    {
      trigger_error('Mailer Error: ' . $mail->ErrorInfo, E_USER_WARNING);
    }
    if ($conf['debug_mail'])
    {
      pwg_send_mail_test($ret, $mail, $args);
    }
  }

  return $ret;
}

/**
 * @deprecated 2.6
 */
function pwg_send_mail($result, $to, $subject, $content, $headers)
{
  if (is_admin())
  {
    trigger_error('pwg_send_mail function is deprecated', E_USER_NOTICE);
  }
  
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
 * Moves CSS rules contained in the <style> tag to inline CSS.
 * Used for compatibility with Gmail and such clients
 * @since 2.6
 *
 * @param string $content
 * @return string
 */
function move_css_to_body($content)
{
  include_once(PHPWG_ROOT_PATH.'include/emogrifier.class.php');

  $e = new Emogrifier($content);
  return @$e->emogrify();
}

/**
 * Saves a copy of the mail if _data/tmp.
 *
 * @param boolean $success
 * @param PHPMailer $mail
 * @param array $args
 */
function pwg_send_mail_test($success, $mail, $args)
{
  global $conf, $user, $lang_info;
  
  $dir = PHPWG_ROOT_PATH.$conf['data_location'].'tmp';
  if (mkgetdir($dir, MKGETDIR_DEFAULT&~MKGETDIR_DIE_ON_ERROR))
  {
    $filename = $dir.'/mail.'.stripslashes($user['username']).'.'.$lang_info['code'].'-'.date('YmdHis').($success ? '' : '.ERROR');
    if ($args['content_format'] == 'text/plain')
    {
      $filename .= '.txt';
    }
    else
    {
      $filename .= '.html';
    }
    
    $file = fopen($filename, 'w+');
    if (!$success)
    {
      fwrite($file, "ERROR: " . $mail->ErrorInfo . "\n\n");
    }
    fwrite($file, $mail->getSentMIMEMessage());
    fclose($file);
  }
}

trigger_notify('functions_mail_included');

?>
