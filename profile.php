<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_CLASSIC);

$userdata = $user;

//------------------------------------------------------ update & customization
$errors = array();
if (isset($_POST['validate']))
{
  $int_pattern = '/^\d+$/';
  if (empty($_POST['nb_image_line'])
      or (!preg_match($int_pattern, $_POST['nb_image_line'])))
  {
    array_push($errors, $lang['nb_image_line_error']);
  }

  if (empty($_POST['nb_line_page'])
      or (!preg_match($int_pattern, $_POST['nb_line_page'])))
  {
    array_push($errors, $lang['nb_line_page_error']);
  }

  if ($_POST['maxwidth'] != ''
      and (!preg_match($int_pattern, $_POST['maxwidth'])
           or $_POST['maxwidth'] < 50))
  {
    array_push($errors, $lang['maxwidth_error']);
  }
  if ($_POST['maxheight']
       and (!preg_match($int_pattern, $_POST['maxheight'])
             or $_POST['maxheight'] < 50))
  {
    array_push($errors, $lang['maxheight_error']);
  }
  // periods must be integer values, they represents number of days
  if (!preg_match($int_pattern, $_POST['recent_period'])
      or $_POST['recent_period'] <= 0)
  {
    array_push($errors, $lang['periods_error']);
  }

  $mail_error = validate_mail_address($_POST['mail_address']);
  if (!empty($mail_error))
  {
    array_push($errors, $mail_error);
  }

  if (!empty($_POST['use_new_pwd']))
  {
    // password must be the same as its confirmation
    if ($_POST['use_new_pwd'] != $_POST['passwordConf'])
    {
      array_push($errors,
                 l10n('New password confirmation does not correspond'));
    }

    // changing password requires old password
    $query = '
SELECT '.$conf['user_fields']['password'].' AS password
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = \''.$userdata['id'].'\'
;';
    list($current_password) = mysql_fetch_row(pwg_query($query));

    if ($conf['pass_convert']($_POST['password']) != $current_password)
    {
      array_push($errors, l10n('Current password is wrong'));
    }
  }

  if (count($errors) == 0)
  {
    // mass_updates function
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

    // update common user informations
    $fields = array($conf['user_fields']['email']);

    $data = array();
    $data{$conf['user_fields']['id']} = $_POST['userid'];
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

    // update user "additional" informations (specific to PhpWebGallery)
    $fields = array(
      'nb_image_line', 'nb_line_page', 'language', 'maxwidth', 'maxheight',
      'expand', 'show_nb_comments', 'recent_period', 'template'
      );

    $data = array();
    $data['user_id'] = $_POST['userid'];

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

    // redirection
    redirect(make_index_url());
  }
}
// +-----------------------------------------------------------------------+
// |                       page header and options                         |
// +-----------------------------------------------------------------------+

$title= $lang['customize_page_title'];
$page['body_id'] = 'theProfilePage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$url_action = PHPWG_ROOT_PATH.'profile.php';

//----------------------------------------------------- template initialization
$template->set_filenames(array('profile_body'=>'profile.tpl'));

$expand = ($userdata['expand'] == 'true') ? 'EXPAND_TREE_YES':'EXPAND_TREE_NO';

$nb_comments =
($userdata['show_nb_comments'] == 'true') ? 'NB_COMMENTS_YES':'NB_COMMENTS_NO';

$template->assign_vars(
  array(
    'USERNAME'=>$userdata['username'],
    'USERID'=>$userdata['id'],
    'EMAIL'=>@$userdata['email'],
    'NB_IMAGE_LINE'=>$userdata['nb_image_line'],
    'NB_ROW_PAGE'=>$userdata['nb_line_page'],
    'RECENT_PERIOD'=>$userdata['recent_period'],
    'MAXWIDTH'=>@$userdata['maxwidth'],
    'MAXHEIGHT'=>@$userdata['maxheight'],

    $expand=>'checked="checked"',
    $nb_comments=>'checked="checked"',

    'U_RETURN' => make_index_url(),

    'F_ACTION'=>$url_action,
    ));

$blockname = 'template_option';

foreach (get_pwg_themes() as $pwg_template)
{
  if (isset($_POST['submit']))
  {
    $selected = $_POST['template']==$pwg_template ? 'selected="selected"' : '';
  }
  else if ($userdata['template'].'/'.$userdata['theme'] == $pwg_template)
  {
    $selected = 'selected="selected"';
  }
  else
  {
    $selected = '';
  }

  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE'=> $pwg_template,
      'CONTENT' => $pwg_template,
      'SELECTED' => $selected
      ));
}

$blockname = 'language_option';

foreach (get_languages() as $language_code => $language_name)
{
  if (isset($_POST['submit']))
  {
    $selected = $_POST['language']==$language_code ? 'selected="selected"':'';
  }
  else if ($userdata['language'] == $language_code)
  {
    $selected = 'selected="selected"';
  }
  else
  {
    $selected = '';
  }

  $template->assign_block_vars(
    $blockname,
    array(
      'VALUE'=> $language_code,
      'CONTENT' => $language_name,
      'SELECTED' => $selected
      ));
}

// +-----------------------------------------------------------------------+
// |                             errors display                            |
// +-----------------------------------------------------------------------+
if (count($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error', array('ERROR'=>$error));
  }
}
// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
$template->assign_block_vars('profile',array());
$template->parse('profile_body');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
