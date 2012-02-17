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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');
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
    'allow_user_registration',
    'obligatory_user_mail_address',
    'rate',
    'rate_anonymous',
    'email_admin_on_new_user',
    'allow_user_customization',
    'log',
    'history_admin',
    'history_guest',
   );

$sizes_checkboxes = array(
    'original_resize',
  );

$comments_checkboxes = array(
    'activate_comments',
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
    'rating_score',
    'privacy_level',
  );
  
// image order management
$sort_fields = array(
  ''                    => '',
  'file ASC'            => l10n('file name, A &rarr; Z'),
  'file DESC'           => l10n('file name, Z &rarr; A'),
  'name ASC'            => l10n('photo title, A &rarr; Z'),
  'name DESC'           => l10n('photo title, Z &rarr; A'),
  'date_creation DESC'  => l10n('date created, new &rarr; old'),
  'date_creation ASC'   => l10n('date created, old &rarr; new'),
  'date_available DESC' => l10n('date posted, new &rarr; old'),
  'date_available ASC'  => l10n('date posted, old &rarr; new'),
  'rating_score DESC'   => l10n('rating score, high &rarr; low'),
  'rating_score ASC'    => l10n('rating score, low &rarr; high'),
  'hit DESC'            => l10n('visits, high &rarr; low'),
  'hit ASC'             => l10n('visits, low &rarr; high'),
  'id ASC'              => l10n('numeric identifier, 1 &rarr; 9'),
  'id DESC'             => l10n('numeric identifier, 9 &rarr; 1'),
  'rank ASC'            => l10n('manual sort order'),
  );
  
$comments_order = array(
  'ASC' => l10n('Show oldest comments first'),
  'DESC' => l10n('Show latest comments first'),
  );


//------------------------------ verification and registration of modifications
if (isset($_POST['submit']))
{
  $int_pattern = '/^\d+$/';

  switch ($page['section'])
  {
    case 'main' :
    {      
      if ( !isset($conf['order_by_custom']) and !isset($conf['order_by_inside_category_custom']) )
      {
        if ( !empty($_POST['order_by']) )
        {         
          // limit to the number of available parameters
          $order_by = $order_by_inside_category = array_slice($_POST['order_by'], 0, ceil(count($sort_fields)/2));
          
          // there is no rank outside categories
          unset($order_by[ array_search('rank ASC', $order_by) ]);
          
          // must define a default order_by if user want to order by rank only
          if ( count($order_by) == 0 )
          {
            $order_by = array('id ASC');
          }
          
          $_POST['order_by'] = 'ORDER BY '.implode(', ', $order_by);
          $_POST['order_by_inside_category'] = 'ORDER BY '.implode(', ', $order_by_inside_category);
        }
        else
        {
          array_push($page['errors'], l10n('No field selected'));
        }
      }
      
      foreach( $main_checkboxes as $checkbox)
      {
        $_POST[$checkbox] = empty($_POST[$checkbox])?'false':'true';
      }
      break;
    }
    case 'sizes' :
    {
      $fields = array(
        'original_resize',
        'original_resize_maxwidth',
        'original_resize_maxheight',
        'original_resize_quality',
        );

      $updates = array();
      
      foreach ($fields as $field)
      {
        $value = !empty($_POST[$field]) ? $_POST[$field] : null;
        $form_values[$field] = $value;
        $updates[$field] = $value;
      }

      save_upload_form_config($updates, $page['errors']);
  
      if (count($page['errors']) == 0)
      {
        array_push(
          $page['infos'],
          l10n('Your configuration settings are saved')
          );
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
  if ('sizes' != $page['section'] and count($page['errors']) == 0)
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
$tabsheet->add('sizes', l10n('Photo sizes'), $conf_link.'sizes');
$tabsheet->add('display', l10n('Display'), $conf_link.'display');
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
    
    function order_by_is_local()
    {
      @include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');
      if (isset($conf['local_dir_site']))
      {
        @include(PHPWG_ROOT_PATH.PWG_LOCAL_DIR. 'config/config.inc.php');
      }
      
      return isset($conf['order_by']) or isset($conf['order_by_inside_category']);
    }
    
    if (order_by_is_local())
    {
      array_push($page['warnings'], l10n('You have specified <i>$conf[\'order_by\']</i> in your local configuration file, this parameter in deprecated, please remove it or rename it into <i>$conf[\'order_by_custom\']</i> !'));
    }
    
    if ( isset($conf['order_by_custom']) or isset($conf['order_by_inside_category_custom']) )
    {
      $order_by = array('');
      $template->assign('ORDER_BY_IS_CUSTOM', true);
    }
    else
    {
      $out = array();
      $order_by = trim($conf['order_by_inside_category']);
      $order_by = str_replace('ORDER BY ', null, $order_by);
      $order_by = explode(', ', $order_by);
    }
  
    $template->assign(
      'main',
      array(
        'CONF_GALLERY_TITLE' => htmlspecialchars($conf['gallery_title']),
        'CONF_PAGE_BANNER' => htmlspecialchars($conf['page_banner']),
        'week_starts_on_options' => array(
          'sunday' => $lang['day'][0],
          'monday' => $lang['day'][1],
          ),
        'week_starts_on_options_selected' => $conf['week_starts_on'],
        'order_by' => $order_by,
        'order_by_options' => $sort_fields,
        )
      );

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
  case 'comments' :
  {
    $template->assign(
      'comments',
      array(
        'NB_COMMENTS_PAGE'=>$conf['nb_comment_page'],
        'comments_order'=>$conf['comments_order'],
        'comments_order_options'=> $comments_order
        )
      );

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
  case 'sizes' :
  {
    $template->assign(
      'sizes',
      array(
        'original_resize_maxwidth' => $conf['original_resize_maxwidth'],
        'original_resize_maxheight' => $conf['original_resize_maxheight'],
        'original_resize_quality' => $conf['original_resize_quality'],
        )
      );
    
    foreach ($sizes_checkboxes as $checkbox)
    {
      $template->append(
        'sizes',
        array(
          $checkbox => $conf[$checkbox]
          ),
        true
        );
    }

    break;
  }
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'config');
?>
