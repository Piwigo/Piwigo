<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_tabsheet.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//-------------------------------------------------------- sections definitions
if (!isset($_GET['section']))
{
  $page['section'] = 'main';
}
else
{
  $page['section'] = $_GET['section'];
}

$main_checkboxes = array(
    'allow_user_registration',
    'obligatory_user_mail_address',
    'rate',
    'rate_anonymous',
    'email_admin_on_new_user',
    'email_admin_on_picture_uploaded',
   );

$history_checkboxes = array(
    'log',
    'history_admin',
    'history_guest'
   );

$comments_checkboxes = array(
    'comments_forall',
    'comments_validation',
    'email_admin_on_comment',
    'email_admin_on_comment_validation',
  );

//------------------------------ verification and registration of modifications
if (isset($_POST['submit']) and !is_adviser())
{
  $int_pattern = '/^\d+$/';

  switch ($page['section'])
  {
    case 'main' :
    {
      if ( !url_is_remote($_POST['gallery_url']) )
      {
        array_push($page['errors'], l10n('conf_gallery_url_error'));
      }
      foreach( $main_checkboxes as $checkbox)
      {
        $_POST[$checkbox] = empty($_POST[$checkbox])?'false':'true';
      }
      break;
    }
    case 'history' :
    {
      foreach( $history_checkboxes as $checkbox)
      {
        $_POST[$checkbox] = empty($_POST[$checkbox])?'false':'true';
      }
      break;
    }
    case 'comments' :
    {
      // the number of comments per page must be an integer between 5 and 50
      // included
      if (!preg_match($int_pattern, $_POST['nb_comment_page'])
           or $_POST['nb_comment_page'] < 5
           or $_POST['nb_comment_page'] > 50)
      {
        array_push($page['errors'], l10n('conf_nb_comment_page_error'));
      }
      foreach( $comments_checkboxes as $checkbox)
      {
        $_POST[$checkbox] = empty($_POST[$checkbox])?'false':'true';
      }
      break;
    }
    case 'default' :
    {
      // Never go here
      break;
    }
  }

  // updating configuration if no error found
  if (count($page['errors']) == 0)
  {
    //echo '<pre>'; print_r($_POST); echo '</pre>';
    $result = pwg_query('SELECT param FROM '.CONFIG_TABLE);
    while ($row = mysql_fetch_array($result))
    {
      if (isset($_POST[$row['param']]))
      {
        $value = $_POST[$row['param']];

        if ('gallery_title' == $row['param'])
        {
          if (!$conf['allow_html_descriptions'])
          {
            $value = strip_tags($value);
          }
        }

        $query = '
UPDATE '.CONFIG_TABLE.'
SET value = \''. str_replace("\'", "''", $value).'\'
WHERE param = \''.$row['param'].'\'
;';
        pwg_query($query);
      }
    }
    array_push($page['infos'], l10n('conf_confirmation'));
  }

  //------------------------------------------------------ $conf reinitialization
  load_conf_from_db();
}

//----------------------------------------------------- template initialization
$template->set_filename('config', 'admin/configuration.tpl');

// TabSheet initialization
$page['tabsheet'] = array
(
  'main' => array
   (
    'caption' => l10n('conf_main_title'),
    'url' => $conf_link.'main'
   ),
  'history' => array
   (
    'caption' => l10n('conf_history_title'),
    'url' => $conf_link.'history'
   ),
  'comments' => array
   (
    'caption' => l10n('conf_comments_title'),
    'url' => $conf_link.'comments'
   ),
  'default' => array
   (
    'caption' => l10n('conf_display'),
    'url' => $conf_link.'default'
   )
);

$page['tabsheet'][$page['section']]['selected'] = true;

// Assign tabsheet to template
template_assign_tabsheet();

$action = PHPWG_ROOT_PATH.'admin.php?page=configuration';
$action.= '&amp;section='.$page['section'];

$template->assign_vars(
  array(
    'L_YES'=>l10n('yes'),
    'L_NO'=>l10n('no'),
    'L_SUBMIT'=>l10n('submit'),
    'L_RESET'=>l10n('reset'),

    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=configuration',

    'F_ACTION'=>$action
    ));

$html_check='checked="checked"';

$include_submit_buttons = true;

switch ($page['section'])
{
  case 'main' :
  {
    $lock_yes = ($conf['gallery_locked']==true)?'checked="checked"':'';
    $lock_no = ($conf['gallery_locked']==false)?'checked="checked"':'';

    $template->assign_block_vars(
      'main',
      array(
        'GALLERY_LOCKED_YES'=>$lock_yes,
        'GALLERY_LOCKED_NO'=>$lock_no,
        'CONF_GALLERY_TITLE' => htmlspecialchars($conf['gallery_title']),
        'CONF_PAGE_BANNER' => htmlspecialchars($conf['page_banner']),
        'CONF_GALLERY_URL' => $conf['gallery_url'],
        ));

    foreach( $main_checkboxes as $checkbox)
    {
      $template->merge_block_vars(
          'main',
          array(
            strtoupper($checkbox) => ($conf[$checkbox]==true)?$html_check:''
            )
        );
    }
    break;
  }
  case 'history' :
  {
    //Necessary for merge_block_vars
    $template->assign_block_vars('history', array());

    foreach( $history_checkboxes as $checkbox)
    {
      $template->merge_block_vars(
          'history',
          array(
            strtoupper($checkbox) => ($conf[$checkbox]==true)?$html_check:''
            )
        );
    }
    break;
  }
  case 'comments' :
  {
    $template->assign_block_vars(
      'comments',
      array(
        'NB_COMMENTS_PAGE'=>$conf['nb_comment_page'],
        ));

    foreach( $comments_checkboxes as $checkbox)
    {
      $template->merge_block_vars(
          'comments',
          array(
            strtoupper($checkbox) => ($conf[$checkbox]==true)?$html_check:''
            )
        );
    }
    break;
  }
  case 'default' :
  {
    $edit_user = build_user($conf['default_user_id'], false);
    include_once(PHPWG_ROOT_PATH.'profile.php');

    $errors = array();
    if ( !is_adviser() )
    {
      if (save_profile_from_post($edit_user, $errors))
      {
        // Reload user
        $edit_user = build_user($conf['default_user_id'], false);
        array_push($page['infos'], l10n('conf_confirmation'));
      }
    }
    $page['errors'] = array_merge($page['errors'], $errors);

    load_profile_in_template(
      $action,
      '',
      $edit_user
      );
    $template->assign_block_vars('default', array());
    $include_submit_buttons = false;
    break;
  }
}

if ($include_submit_buttons)
{
  $template->assign_block_vars('include_submit_buttons', array());
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'config');
?>
