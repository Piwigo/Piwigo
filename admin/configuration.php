<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

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
    'gallery_locked',
    'allow_user_registration',
    'obligatory_user_mail_address',
    'rate',
    'rate_anonymous',
    'email_admin_on_new_user',
    'allow_user_customization',
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
    'user_can_delete_comment',
    'user_can_edit_comment',
    'email_admin_on_comment_edition',
    'email_admin_on_comment_deletion'
  );

$display_checkboxes = array(
    'menubar_filter_icon',
    'index_sort_order_input',
    'index_flat_icon',
    'index_posted_date_icon',
    'index_created_date_icon',
    'index_slideshow_icon',
    'index_new_icon',
    'picture_metadata_icon',
    'picture_slideshow_icon',
    'picture_favorite_icon',
    'picture_download_icon',
    'picture_navigation_icons',
    'picture_navigation_thumb',
    'picture_menu',
  );

$display_info_checkboxes = array(
    'author',
    'created_on',
    'posted_on',
    'dimensions',
    'file',
    'filesize',
    'tags',
    'categories',
    'visits',
    'average_rate',
    'privacy_level',
  );
  
$order_options = array(
    ' ORDER BY date_available DESC, file ASC, id ASC' => 'Post date DESC, File name ASC',
    ' ORDER BY date_available ASC, file ASC, id ASC' => 'Post date ASC, File name ASC',
    ' ORDER BY file DESC, date_available DESC, id ASC' => 'File name DESC, Post date DESC',
    ' ORDER BY file ASC, date_available DESC, id ASC' => 'File name ASC, Post date DESC',
    'custom' => l10n('Custom'),
  );

//------------------------------ verification and registration of modifications
if (isset($_POST['submit']))
{
  $int_pattern = '/^\d+$/';

  switch ($page['section'])
  {
    case 'main' :
    {
      $order_regex = '#^(([ \w\']{2,}) (ASC|DESC),{1}){1,}$#';
      // process 'order_by_perso' string
      if ($_POST['order_by'] == 'custom' AND !empty($_POST['order_by_perso']))
      {
        $_POST['order_by_perso'] = stripslashes(trim($_POST['order_by_perso']));
        $_POST['order_by'] = str_ireplace(
          array('order by ', 'asc', 'desc', '"'),
          array(null, 'ASC', 'DESC', '\''),
          $_POST['order_by_perso']
          );
        
        if (preg_match($order_regex, $_POST['order_by'].','))
        {
          $_POST['order_by'] = ' ORDER BY '.addslashes($_POST['order_by']);
        }
        else
        {
          array_push($page['errors'], l10n('Invalid order string').' &laquo; '.$_POST['order_by'].' &raquo;');
        }
      }
      else if ($_POST['order_by'] == 'custom')
      {
        array_push($page['errors'], l10n('Invalid order string'));
      }
      // process 'order_by_inside_category_perso' string
      if ($_POST['order_by_inside_category'] == 'as_order_by')
      {
        $_POST['order_by_inside_category'] = $_POST['order_by'];
      }
      else if ($_POST['order_by_inside_category'] == 'custom' AND !empty($_POST['order_by_inside_category_perso']))
      {
        $_POST['order_by_inside_category_perso'] = stripslashes(trim($_POST['order_by_inside_category_perso']));
        $_POST['order_by_inside_category'] = str_ireplace(
          array('order by ', 'asc', 'desc', '"'),
          array(null, 'ASC', 'DESC', '\''),
          $_POST['order_by_inside_category_perso']
          );
        
        if (preg_match($order_regex, $_POST['order_by_inside_category'].','))
        {
          $_POST['order_by_inside_category'] = ' ORDER BY '.addslashes($_POST['order_by_inside_category']);
        }
        else
        {
          array_push($page['errors'], l10n('Invalid order string').' &laquo; '.$_POST['order_by_inside_category'].' &raquo;');
        }
      }
      else if ($_POST['order_by_inside_category'] == 'custom')
      {
        array_push($page['errors'], l10n('Invalid order string'));
      }
      
      if (empty($_POST['gallery_locked']) and $conf['gallery_locked'])
      {
        $tpl_var = & $template->get_template_vars('header_msgs');
        $msg_key = array_search(l10n('The gallery is locked for maintenance. Please, come back later.'), $tpl_var);
        unset($tpl_var[$msg_key]);
      }
      elseif (!empty($_POST['gallery_locked']) and !$conf['gallery_locked'])
      {
        $template->append('header_msgs', l10n('The gallery is locked for maintenance. Please, come back later.'));
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
        array_push($page['errors'], l10n('The number of comments a page must be between 5 and 50 included.'));
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
    case 'display' :
    {
      foreach( $display_checkboxes as $checkbox)
      {
        $_POST[$checkbox] = empty($_POST[$checkbox])?'false':'true';
      }
      foreach( $display_info_checkboxes as $checkbox)
      {
        $_POST['picture_informations'][$checkbox] =
          empty($_POST['picture_informations'][$checkbox])? false : true;
      }
      $_POST['picture_informations'] = addslashes(serialize($_POST['picture_informations']));
      break;
    }
  }

  // updating configuration if no error found
  if (count($page['errors']) == 0)
  {
    //echo '<pre>'; print_r($_POST); echo '</pre>';
    $result = pwg_query('SELECT param FROM '.CONFIG_TABLE);
    while ($row = pwg_db_fetch_assoc($result))
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
    array_push($page['infos'], l10n('Information data registered in database'));
  }

  //------------------------------------------------------ $conf reinitialization
  load_conf_from_db();
}

//----------------------------------------------------- template initialization
$template->set_filename('config', 'configuration.tpl');

// TabSheet
$tabsheet = new tabsheet();
// TabSheet initialization
$tabsheet->add('main', l10n('Main'), $conf_link.'main');
$tabsheet->add('display', l10n('Display'), $conf_link.'display');
$tabsheet->add('history', l10n('History'), $conf_link.'history');
$tabsheet->add('comments', l10n('Comments'), $conf_link.'comments');
$tabsheet->add('default', l10n('Guest Settings'), $conf_link.'default');
// TabSheet selection
$tabsheet->select($page['section']);
// Assign tabsheet to template
$tabsheet->assign();

$action = get_root_url().'admin.php?page=configuration';
$action.= '&amp;section='.$page['section'];

$template->assign(
  array(
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=configuration',
    'F_ACTION'=>$action
    ));

switch ($page['section'])
{
  case 'main' :
  {
    // process 'order_by' string
    if (array_key_exists($conf['order_by'], $order_options))
    {
      $order_by_selected = $conf['order_by'];
      $order_by_perso = null;
    }
    else
    {
      $order_by_selected = 'custom';
      $order_by_perso = str_replace(' ORDER BY ', null, $conf['order_by']);
    }
    // process 'order_by_inside_category' string
    if ($conf['order_by_inside_category'] == $conf['order_by'])
    {
      $order_by_inside_category_selected = 'as_order_by';
      $order_by_inside_category_perso = null;
    }
    else if (array_key_exists($conf['order_by_inside_category'], $order_options))
    {
      $order_by_inside_category_selected = $conf['order_by_inside_category'];
      $order_by_inside_category_perso = null;
    }
    else
    {
      $order_by_inside_category_selected = 'custom';
      $order_by_inside_category_perso = str_replace(' ORDER BY ', null, $conf['order_by_inside_category']);
    }
     
    $template->assign(
      'main',
      array(
        'CONF_GALLERY_TITLE' => htmlspecialchars($conf['gallery_title']),
        'CONF_PAGE_BANNER' => htmlspecialchars($conf['page_banner']),
        'CONF_GALLERY_URL' => $conf['gallery_url'],
        'week_starts_on_options' => array(
          'sunday' => $lang['day'][0],
          'monday' => $lang['day'][1],
          ),
        'week_starts_on_options_selected' => $conf['week_starts_on'],
        'order_by_options' => $order_options,
        'order_by_selected' => $order_by_selected,
        'order_by_perso' => $order_by_perso,
        'order_by_inside_category_options' => 
          array_merge(
            array('as_order_by'=>l10n('As default order')), 
            $order_options
            ),
        'order_by_inside_category_selected' => $order_by_inside_category_selected,
        'order_by_inside_category_perso' => $order_by_inside_category_perso,
        ));

    foreach ($main_checkboxes as $checkbox)
    {
      $template->append(
          'main',
          array(
            $checkbox => $conf[$checkbox]
            ),
          true
        );
    }
    break;
  }
  case 'history' :
  {
    //Necessary for merge_block_vars
    foreach ($history_checkboxes as $checkbox)
    {
      $template->append(
          'history',
          array(
            $checkbox => $conf[$checkbox]
            ),
          true
        );
    }
    break;
  }
  case 'comments' :
  {
    $template->assign(
      'comments',
      array(
        'NB_COMMENTS_PAGE'=>$conf['nb_comment_page'],
        ));

    foreach ($comments_checkboxes as $checkbox)
    {
      $template->append(
          'comments',
          array(
            $checkbox => $conf[$checkbox]
            ),
          true
        );
    }
    break;
  }
  case 'default' :
  {
    $edit_user = build_user($conf['guest_id'], false);
    include_once(PHPWG_ROOT_PATH.'profile.php');

    $errors = array();
    if (save_profile_from_post($edit_user, $errors))
    {
      // Reload user
      $edit_user = build_user($conf['guest_id'], false);
      array_push($page['infos'], l10n('Information data registered in database'));
    }
    $page['errors'] = array_merge($page['errors'], $errors);

    load_profile_in_template(
      $action,
      '',
      $edit_user
      );
    $template->assign('default', array());
    break;
  }
  case 'display' :
  {
    foreach ($display_checkboxes as $checkbox)
    {
      $template->append(
          'display',
          array(
            $checkbox => $conf[$checkbox]
            ),
          true
        );
    }
    $template->append(
        'display',
        array(
          'picture_informations' => unserialize($conf['picture_informations'])
          ),
        true
      );
    break;
  }
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'config');
?>
