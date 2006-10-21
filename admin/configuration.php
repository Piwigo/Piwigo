<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//-------------------------------------------------------- sections definitions
if (!isset($_GET['section']))
{
  $page['section'] = 'general';
}
else
{
  $page['section'] = $_GET['section'];
}
//------------------------------ verification and registration of modifications
if (isset($_POST['submit']) and !is_adviser())
{
  $int_pattern = '/^\d+$/';
  switch ($page['section'])
  {
    case 'general' :
    {
      if ( !url_is_remote($_POST['gallery_url']) )
      {
        array_push($page['errors'], $lang['conf_gallery_url_error']);
      }
      $_POST['log'] = empty($_POST['log'])?'false':'true';
      $_POST['history_admin'] = empty($_POST['history_admin'])?'false':'true';
      $_POST['history_guest'] = empty($_POST['history_guest'])?'false':'true';
      $_POST['login_history'] = empty($_POST['login_history'])?'false':'true';
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
        array_push($page['errors'], $lang['conf_nb_comment_page_error']);
      }
      break;
    }
    case 'default' :
    {
      // periods must be integer values, they represents number of days
      if (!preg_match($int_pattern, $_POST['recent_period'])
          or $_POST['recent_period'] <= 0)
      {
        array_push($page['errors'], $lang['periods_error']);
      }
      // maxwidth
      if (isset($_POST['default_maxwidth'])
          and !empty($_POST['default_maxwidth'])
          and (!preg_match($int_pattern, $_POST['default_maxwidth'])
               or $_POST['default_maxwidth'] < 50))
      {
        array_push($page['errors'], $lang['maxwidth_error']);
      }
      // maxheight
      if (isset($_POST['default_maxheight'])
          and !empty($_POST['default_maxheight'])
          and (!preg_match($int_pattern, $_POST['default_maxheight'])
               or $_POST['default_maxheight'] < 50))
      {
        array_push($page['errors'], $lang['maxheight_error']);
      }
      break;
    }
  }

  // updating configuration if no error found
  if (count($page['errors']) == 0)
  {
    //echo '<pre>'; print_r($_POST); echo '</pre>';
    $result = pwg_query('SELECT * FROM '.CONFIG_TABLE);
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
    array_push($page['infos'], $lang['conf_confirmation']);
  }
}

//------------------------------------------------------ $conf reinitialization
$result = pwg_query('SELECT param,value FROM '.CONFIG_TABLE);
while ($row = mysql_fetch_array($result))
{
  $conf[$row['param']] = $row['value'];
}

//----------------------------------------------------- template initialization
$template->set_filenames( array('config'=>'admin/configuration.tpl') );

$action = PHPWG_ROOT_PATH.'admin.php?page=configuration';
$action.= '&amp;section='.$page['section'];

$template->assign_vars(
  array(
    'L_YES'=>$lang['yes'],
    'L_NO'=>$lang['no'],
    'L_SUBMIT'=>$lang['submit'],
    'L_RESET'=>$lang['reset'],

    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=configuration',

    'F_ACTION'=>$action
    ));

switch ($page['section'])
{
  case 'general' :
  {
    $html_check='checked="checked"';

    $lock_yes = ($conf['gallery_locked']=='true')?'checked="checked"':'';
    $lock_no = ($conf['gallery_locked']=='false')?'checked="checked"':'';
    $history_users = ($conf['log']=='true')?$html_check:'';
    $history_admin = ($conf['history_admin']=='true')?$html_check:'';
    $history_guest = ($conf['history_guest']=='true')?$html_check:'';
    $login_history = ($conf['login_history']=='true')?$html_check:'';

    $template->assign_block_vars(
      'general',
      array(
        'HISTORY_USERS'=>$history_users,
        'HISTORY_ADMIN'=>$history_admin,
        'HISTORY_GUEST'=>$history_guest,
        'LOGIN_HISTORY'=>$login_history,
        'GALLERY_LOCKED_YES'=>$lock_yes,
        'GALLERY_LOCKED_NO'=>$lock_no,
        ($conf['rate']=='true'?'RATE_YES':'RATE_NO')=>$html_check,
        ($conf['rate_anonymous']=='true'
             ? 'RATE_ANONYMOUS_YES' : 'RATE_ANONYMOUS_NO')=>$html_check,
        'CONF_GALLERY_TITLE' => $conf['gallery_title'],
        'CONF_PAGE_BANNER' => $conf['page_banner'],
        'CONF_GALLERY_URL' => $conf['gallery_url'],
        ));
    break;
  }
  case 'comments' :
  {
    $all_yes = ($conf['comments_forall']=='true')?'checked="checked"':'';
    $all_no  = ($conf['comments_forall']=='false')?'checked="checked"':'';
    $validate_yes = ($conf['comments_validation']=='true')?'checked="checked"':'';
    $validate_no = ($conf['comments_validation']=='false')?'checked="checked"':'';

    $template->assign_block_vars(
      'comments',
      array(
        'NB_COMMENTS_PAGE'=>$conf['nb_comment_page'],
        'COMMENTS_ALL_YES'=>$all_yes,
        'COMMENTS_ALL_NO'=>$all_no,
        'VALIDATE_YES'=>$validate_yes,
        'VALIDATE_NO'=>$validate_no
        ));
    break;
  }
  case 'default' :
  {
    $show_yes = ($conf['show_nb_comments']=='true')?'checked="checked"':'';
    $show_no = ($conf['show_nb_comments']=='false')?'checked="checked"':'';
    $expand_yes = ($conf['auto_expand']=='true')?'checked="checked"':'';
    $expand_no  = ($conf['auto_expand']=='false')?'checked="checked"':'';

    $template->assign_block_vars(
      'default',
      array(
        'NB_IMAGE_LINE'=>$conf['nb_image_line'],
        'NB_ROW_PAGE'=>$conf['nb_line_page'],
        'CONF_RECENT'=>$conf['recent_period'],
        'NB_COMMENTS_PAGE'=>$conf['nb_comment_page'],
        'MAXWIDTH'=>$conf['default_maxwidth'],
        'MAXHEIGHT'=>$conf['default_maxheight'],
        'EXPAND_YES'=>$expand_yes,
        'EXPAND_NO'=>$expand_no,
        'SHOW_COMMENTS_YES'=>$show_yes,
        'SHOW_COMMENTS_NO'=>$show_no
        ));

    $blockname = 'default.language_option';

    foreach (get_languages() as $language_code => $language_name)
    {
      if (isset($_POST['submit']))
      {
        $selected =
          $_POST['default_language'] == $language_code
            ? 'selected="selected"' : '';
      }
      else if ($conf['default_language'] == $language_code)
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

    $blockname = 'default.template_option';

    foreach (get_pwg_themes() as $pwg_template)
    {
      if (isset($_POST['submit']))
      {
        $selected =
          $_POST['default_template'] == $pwg_template
            ? 'selected="selected"' : '';
      }
      else if ($conf['default_template'] == $pwg_template)
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
          )
        );
    }


    break;
  }
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'config');
?>
