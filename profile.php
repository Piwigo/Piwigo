<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

// customize appearance of the site for a user
// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{//direct script access
  define('PHPWG_ROOT_PATH','./');
  include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

  // +-----------------------------------------------------------------------+
  // | Check Access and exit when user status is not ok                      |
  // +-----------------------------------------------------------------------+
  check_status(ACCESS_CLASSIC);

  if (!empty($_POST))
  {
    check_pwg_token();
  }

  $userdata = $user;

  trigger_notify('loc_begin_profile');

// Reset to default (Guest) custom settings
  if (isset($_POST['reset_to_default']))
  {
    $fields = array(
      'nb_image_page', 'expand',
      'show_nb_comments', 'show_nb_hits', 'recent_period', 'show_nb_hits'
      );

    // Get the Guest custom settings
    $query = '
SELECT '.implode(',', $fields).'
  FROM '.USER_INFOS_TABLE.'
  WHERE user_id = '.$conf['default_user_id'].'
;';
    $result = pwg_query($query);
    $default_user = pwg_db_fetch_assoc($result);
    $userdata = array_merge($userdata, $default_user);
  }

  save_profile_from_post($userdata, $page['errors']);

  $title= l10n('Your Gallery Customization');
  $page['body_id'] = 'theProfilePage';
  $template->set_filename('profile', 'profile.tpl');
  $template->set_filename('profile_content', 'profile_content.tpl');

  load_profile_in_template(
    get_root_url().'profile.php', // action
    make_index_url(), // for redirect
    $userdata );
  $template->assign_var_from_handle('PROFILE_CONTENT', 'profile_content');


  
  // include menubar
  $themeconf = $template->get_template_vars('themeconf');
  if (!isset($themeconf['hide_menu_on']) OR !in_array('theProfilePage', $themeconf['hide_menu_on']))
  {
    include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
  }
  
  include(PHPWG_ROOT_PATH.'include/page_header.php');
  trigger_notify('loc_end_profile');
  flush_page_messages();
  $template->pparse('profile');
  include(PHPWG_ROOT_PATH.'include/page_tail.php');
}

//------------------------------------------------------ update & customization
function save_profile_from_post($userdata, &$errors)
{
  global $conf, $page;
  $errors = array();

  if (!isset($_POST['validate']))
  {
    return false;
  }

  $special_user = in_array($userdata['id'], array($conf['guest_id'], $conf['default_user_id']));
  if ($special_user)
  {
    unset(
      $_POST['username'],
      $_POST['mail_address'],
      $_POST['password'],
      $_POST['use_new_pwd'],
      $_POST['passwordConf'],
      $_POST['theme'],
      $_POST['language']
      );
    $_POST['theme'] = get_default_theme();
    $_POST['language'] = get_default_language();
  }
  
  if (!defined('IN_ADMIN'))
  {
    unset($_POST['username']);
  }

  if ($conf['allow_user_customization'] or defined('IN_ADMIN'))
  {
    $int_pattern = '/^\d+$/';
    if (empty($_POST['nb_image_page'])
        or (!preg_match($int_pattern, $_POST['nb_image_page'])))
    {
      $errors[] = l10n('The number of photos per page must be a not null scalar');
    }

    // periods must be integer values, they represents number of days
    if (!preg_match($int_pattern, $_POST['recent_period'])
        or $_POST['recent_period'] < 0)
    {
      $errors[] = l10n('Recent period must be a positive integer value') ;
    }

    if (!in_array($_POST['language'], array_keys(get_languages())))
    {
      die('Hacking attempt, incorrect language value');
    }

    if (!in_array($_POST['theme'], array_keys(get_pwg_themes())))
    {
      die('Hacking attempt, incorrect theme value');
    }
  }

  if (isset($_POST['mail_address']))
  {
    // if $_POST and $userdata have are same email
    // validate_mail_address allows, however, to check email
    $mail_error = validate_mail_address($userdata['id'], $_POST['mail_address']);
    if (!empty($mail_error))
    {
      $errors[] = $mail_error;
    }
  }

  if (!empty($_POST['use_new_pwd']))
  {
    // password must be the same as its confirmation
    if ($_POST['use_new_pwd'] != $_POST['passwordConf'])
    {
      $errors[] = l10n('The passwords do not match');
    }

    if ( !defined('IN_ADMIN') )
    {// changing password requires old password
      $query = '
  SELECT '.$conf['user_fields']['password'].' AS password
    FROM '.USERS_TABLE.'
    WHERE '.$conf['user_fields']['id'].' = \''.$userdata['id'].'\'
  ;';
      list($current_password) = pwg_db_fetch_row(pwg_query($query));

      if (!$conf['password_verify']($_POST['password'], $current_password))
      {
        $errors[] = l10n('Current password is wrong');
      }
    }
  }

  if (count($errors) == 0)
  {
    // mass_updates function
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

    $activity_details_tables = array();

    if (isset($_POST['mail_address']))
    {
      // update common user informations
      $fields = array($conf['user_fields']['email']);

      $data = array();
      $data{$conf['user_fields']['id']} = $userdata['id'];
      $data{$conf['user_fields']['email']} = $_POST['mail_address'];

      // password is updated only if filled
      if (!empty($_POST['use_new_pwd']))
      {
        $fields[] = $conf['user_fields']['password'];
        // password is hashed with function $conf['password_hash']
        $data{$conf['user_fields']['password']} = $conf['password_hash']($_POST['use_new_pwd']);

        deactivate_user_auth_keys($userdata['id']);
      }
      
      // username is updated only if allowed
      if (!empty($_POST['username']))
      {
        if ($_POST['username'] != $userdata['username'] and get_userid($_POST['username']))
        {
          $page['errors'][] = l10n('this login is already used');
          unset($_POST['redirect']);
        }
        else
        {
          $fields[] = $conf['user_fields']['username'];
          $data{$conf['user_fields']['username']} = $_POST['username'];
          
          // send email to the user
          if ($_POST['username'] != $userdata['username'])
          {
            include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
            switch_lang_to($userdata['language']);
            
            $keyargs_content = array(
              get_l10n_args('Hello', ''),
              get_l10n_args('Your username has been successfully changed to : %s', $_POST['username']),
              );
              
            pwg_mail(
              $_POST['mail_address'],
              array(
                'subject' => '['.$conf['gallery_title'].'] '.l10n('Username modification'),
                'content' => l10n_args($keyargs_content),
                'content_format' => 'text/plain',
                )
              );
              
            switch_lang_back();
          }
        }
      }
      
      mass_updates(USERS_TABLE,
                   array(
                    'primary' => array($conf['user_fields']['id']),
                    'update' => $fields
                    ),
                   array($data));

      $activity_details_tables[] = 'users';
    }

    if ($conf['allow_user_customization'] or defined('IN_ADMIN'))
    {
      // update user "additional" informations (specific to Piwigo)
      $fields = array(
        'nb_image_page', 'language',
        'expand', 'show_nb_hits', 'recent_period', 'theme'
        );
        
      if ($conf['activate_comments'])
      {
        $fields[] = 'show_nb_comments';
      }

      $data = array();
      $data['user_id'] = $userdata['id'];

      foreach ($fields as $field)
      {
        if (isset($_POST[$field]))
        {
          $data[$field] = $_POST[$field];
        }
      }
      mass_updates(USER_INFOS_TABLE,
                   array('primary' => array('user_id'), 'update' => $fields),
                   array($data));

      $activity_details_tables[] = 'user_infos';
    }
    trigger_notify( 'save_profile_from_post', $userdata['id'] );
    pwg_activity('user', $userdata['id'], 'edit', array('function'=>__FUNCTION__, 'tables'=>implode(',', $activity_details_tables)));

    if (!empty($_POST['redirect']))
    {
      redirect($_POST['redirect']);
    }
  }
  return true;
}

/**
 * Assign template variables, from arguments
 * Used to build profile edition pages
 * 
 * @param string $url_action
 * @param string $url_redirect
 * @param array $userdata
 */
function load_profile_in_template($url_action, $url_redirect, $userdata, $template_prefixe=null)
{
  global $template, $conf;

  $template->assign('radio_options',
    array(
      'true' => l10n('Yes'),
      'false' => l10n('No')));

  $template->assign(
    array(
      $template_prefixe.'USERNAME'=>stripslashes($userdata['username']),
      $template_prefixe.'EMAIL'=>@$userdata['email'],
      $template_prefixe.'ALLOW_USER_CUSTOMIZATION'=>$conf['allow_user_customization'],
      $template_prefixe.'ACTIVATE_COMMENTS'=>$conf['activate_comments'],
      $template_prefixe.'NB_IMAGE_PAGE'=>$userdata['nb_image_page'],
      $template_prefixe.'RECENT_PERIOD'=>$userdata['recent_period'],
      $template_prefixe.'EXPAND' =>$userdata['expand'] ? 'true' : 'false',
      $template_prefixe.'NB_COMMENTS'=>$userdata['show_nb_comments'] ? 'true' : 'false',
      $template_prefixe.'NB_HITS'=>$userdata['show_nb_hits'] ? 'true' : 'false',
      $template_prefixe.'REDIRECT' => $url_redirect,
      $template_prefixe.'F_ACTION'=>$url_action,
      ));

  $template->assign('template_selection', $userdata['theme']);
  $template->assign('template_options', get_pwg_themes());

  foreach (get_languages() as $language_code => $language_name)
  {
    if (isset($_POST['submit']) or $userdata['language'] == $language_code)
    {
      $template->assign('language_selection', $language_code);
    }
    $language_options[$language_code] = $language_name;
  }

  $template->assign('language_options', $language_options);

  $special_user = in_array($userdata['id'], array($conf['guest_id'], $conf['default_user_id']));
  $template->assign('SPECIAL_USER', $special_user);
  $template->assign('IN_ADMIN', defined('IN_ADMIN'));

  // allow plugins to add their own form data to content
  trigger_notify( 'load_profile_in_template', $userdata );

  $template->assign('PWG_TOKEN', get_pwg_token());
}
?>
