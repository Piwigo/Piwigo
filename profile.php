<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

  trigger_action('loc_begin_profile');

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

  load_profile_in_template(
    get_root_url().'profile.php', // action
    make_index_url(), // for redirect
    $userdata );

  
  // include menubar
  $themeconf = $template->get_template_vars('themeconf');
  if (!isset($themeconf['hide_menu_on']) OR !in_array('theProfilePage', $themeconf['hide_menu_on']))
  {
    include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
  }
  
  include(PHPWG_ROOT_PATH.'include/page_header.php');
  trigger_action('loc_end_profile');
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
        or $_POST['recent_period'] <= 0)
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

      if ($conf['pass_convert']($_POST['password']) != $current_password)
      {
        $errors[] = l10n('Current password is wrong');
      }
    }
  }

  if (count($errors) == 0)
  {
    // mass_updates function
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

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
        array_push($fields, $conf['user_fields']['password']);
        // password is encrpyted with function $conf['pass_convert']
        $data{$conf['user_fields']['password']} = $conf['pass_convert']($_POST['use_new_pwd']);
      }
      
      // username is updated only if allowed
      if (!empty($_POST['username']))
      {
        if ($_POST['username'] != $userdata['username'] and get_userid($_POST['username']))
        {
          array_push($page['errors'], l10n('this login is already used'));
          unset($_POST['redirect']);
        }
        else
        {
          array_push($fields, $conf['user_fields']['username']);
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
        array_push($fields, 'show_nb_comments');
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
    }
    trigger_action( 'save_profile_from_post', $userdata['id'] );

    if (!empty($_POST['redirect']))
    {
      redirect($_POST['redirect']);
    }
  }
  return true;
}


function load_profile_in_template($url_action, $url_redirect, $userdata)
{
  global $template, $conf;

  $template->set_filename('profile_content', 'profile_content.tpl');

  $template->assign('radio_options',
    array(
      'true' => l10n('Yes'),
      'false' => l10n('No')));

  $template->assign(
    array(
      'USERNAME'=>stripslashes($userdata['username']),
      'EMAIL'=>get_email_address_as_display_text(@$userdata['email']),
      'ALLOW_USER_CUSTOMIZATION'=>$conf['allow_user_customization'],
      'ACTIVATE_COMMENTS'=>$conf['activate_comments'],
      'NB_IMAGE_PAGE'=>$userdata['nb_image_page'],
      'RECENT_PERIOD'=>$userdata['recent_period'],
      'EXPAND' =>$userdata['expand'] ? 'true' : 'false',
      'NB_COMMENTS'=>$userdata['show_nb_comments'] ? 'true' : 'false',
      'NB_HITS'=>$userdata['show_nb_hits'] ? 'true' : 'false',
      'REDIRECT' => $url_redirect,
      'F_ACTION'=>$url_action,
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
  trigger_action( 'load_profile_in_template', $userdata );

  $template->assign('PWG_TOKEN', get_pwg_token());
  $template->assign_var_from_handle('PROFILE_CONTENT', 'profile_content');
}
?>
