<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

  $userdata = $user;

  trigger_action('loc_begin_profile');

  save_profile_from_post($userdata, $errors);

  $title= l10n('customize_page_title');
  $page['body_id'] = 'theProfilePage';
  include(PHPWG_ROOT_PATH.'include/page_header.php');

  load_profile_in_template(
    get_root_url().'profile.php', // action
    make_index_url(), // for redirect
    $userdata );

  // +-----------------------------------------------------------------------+
  // |                             errors display                            |
  // +-----------------------------------------------------------------------+
  if (count($errors) != 0)
  {
    $template->assign('errors', $errors);
  }
  $template->set_filename('profile', 'profile.tpl');
  trigger_action('loc_end_profile');
  $template->parse('profile');
  include(PHPWG_ROOT_PATH.'include/page_tail.php');
}

//------------------------------------------------------ update & customization
function save_profile_from_post($userdata, &$errors)
{
  global $conf;
  $errors = array();

  if (!isset($_POST['validate']))
  {
    return false;
  }

  $special_user = in_array($userdata['id'], array($conf['guest_id'], $conf['default_user_id']));
  if ($special_user)
  {
    unset($_POST['mail_address'],
          $_POST['password'],
          $_POST['use_new_pwd'],
          $_POST['passwordConf']
          );
  }

  $int_pattern = '/^\d+$/';
  if (empty($_POST['nb_image_line'])
      or (!preg_match($int_pattern, $_POST['nb_image_line'])))
  {
    $errors[] = l10n('nb_image_line_error');
  }

  if (empty($_POST['nb_line_page'])
      or (!preg_match($int_pattern, $_POST['nb_line_page'])))
  {
    $errors[] = l10n('nb_line_page_error');
  }

  if ($_POST['maxwidth'] != ''
      and (!preg_match($int_pattern, $_POST['maxwidth'])
           or $_POST['maxwidth'] < 50))
  {
    $errors[] = l10n('maxwidth_error');
  }
  if ($_POST['maxheight']
       and (!preg_match($int_pattern, $_POST['maxheight'])
             or $_POST['maxheight'] < 50))
  {
    $errors[] = l10n('maxheight_error');
  }
  // periods must be integer values, they represents number of days
  if (!preg_match($int_pattern, $_POST['recent_period'])
      or $_POST['recent_period'] <= 0)
  {
    $errors[] = l10n('periods_error') ;
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
      $errors[] = l10n('New password confirmation does not correspond');
    }

    if ( !defined('IN_ADMIN') )
    {// changing password requires old password
      $query = '
  SELECT '.$conf['user_fields']['password'].' AS password
    FROM '.USERS_TABLE.'
    WHERE '.$conf['user_fields']['id'].' = \''.$userdata['id'].'\'
  ;';
      list($current_password) = mysql_fetch_row(pwg_query($query));
  
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
        $data{$conf['user_fields']['password']} =
          $conf['pass_convert']($_POST['use_new_pwd']);
      }
      mass_updates(USERS_TABLE,
                   array('primary' => array($conf['user_fields']['id']),
                         'update' => $fields),
                   array($data));
    }

    // update user "additional" informations (specific to PhpWebGallery)
    $fields = array(
      'nb_image_line', 'nb_line_page', 'language', 'maxwidth', 'maxheight',
      'expand', 'show_nb_comments', 'show_nb_hits', 'recent_period', 'template'
      );

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
      'USERNAME'=>$userdata['username'],
      'EMAIL'=>get_email_address_as_display_text(@$userdata['email']),
      'NB_IMAGE_LINE'=>$userdata['nb_image_line'],
      'NB_ROW_PAGE'=>$userdata['nb_line_page'],
      'RECENT_PERIOD'=>$userdata['recent_period'],
      'MAXWIDTH'=>@$userdata['maxwidth'],
      'MAXHEIGHT'=>@$userdata['maxheight'],
      'EXPAND' =>$userdata['expand'] ? 'true' : 'false',
      'NB_COMMENTS'=>$userdata['show_nb_comments'] ? 'true' : 'false',
      'NB_HITS'=>$userdata['show_nb_hits'] ? 'true' : 'false',
      'REDIRECT' => $url_redirect,
      'F_ACTION'=>$url_action,
      ));

  foreach (get_pwg_themes() as $pwg_template)
  {
    if (isset($_POST['submit'])
      or $userdata['template'].'/'.$userdata['theme'] == $pwg_template)
    {
      $template->assign('template_selection', $pwg_template);
    }
    $template_options[$pwg_template] = $pwg_template;
  }
  $template->assign('template_options', $template_options);

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
  
  $template->assign_var_from_handle('PROFILE_CONTENT', 'profile_content');
}
?>
