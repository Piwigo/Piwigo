<?php
// +-----------------------------------------------------------------------+
// |                           configuration.php                           |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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

include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );
//-------------------------------------------------------- sections definitions
if (!isset($_GET['section']))
{
  $page['section'] = 'general';
}
else
{
  $page['section'] = $_GET['section'];
}
//------------------------------------------------------ $conf reinitialization
$result = mysql_query('SELECT param,value FROM '.CONFIG_TABLE);
while ($row = mysql_fetch_array($result))
{
  $conf[$row['param']] = $row['value'];
  // if the parameter is present in $_POST array (if a form is submited), we
  // override it with the submited value
  if (isset($_POST[$row['param']]) && !isset($_POST['reset']))
  {
    $conf[$row['param']] = $_POST[$row['param']];
  }
}					   
//------------------------------ verification and registration of modifications
$errors = array();
if (isset($_POST['submit']))
{
  $int_pattern = '/^\d+$/';
  switch ($page['section'])
  {
    case 'general' :
    {
      // thumbnail prefix must only contain simple ASCII characters
      if (!preg_match('/^[\w-]*$/', $_POST['prefix_thumbnail']))
      {
        array_push($errors, $lang['conf_prefix_thumbnail_error']);
      }
      // mail must be formatted as follows : name@server.com
      $pattern = '/^[\w-]+(\.[\w-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$/';
      if (!preg_match($pattern, $_POST['mail_webmaster']))
      {
        array_push($errors, $lang['conf_mail_webmaster_error']);
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
        array_push($errors, $lang['conf_nb_comment_page_error']);
      }
      break;
    }
    case 'default' :
    {
      // periods must be integer values, they represents number of days
      if (!preg_match($int_pattern, $_POST['recent_period'])
          or $_POST['recent_period'] <= 0)
      {
        array_push($errors, $lang['periods_error']);
      }
      break;
    }
    case 'upload' :
    {
      // the maximum upload filesize must be an integer between 10 and 1000
      if (!preg_match($int_pattern, $_POST['upload_maxfilesize'])
          or $_POST['upload_maxfilesize'] < 10
          or $_POST['upload_maxfilesize'] > 1000)
      {
        array_push($errors, $lang['conf_upload_maxfilesize_error']);
      }
      
      foreach (array('upload_maxwidth',
                     'upload_maxheight',
                     'upload_maxwidth_thumbnail',
                     'upload_maxheight_thumbnail')
               as $field)
      {
        if (!preg_match($int_pattern, $_POST[$field])
          or $_POST[$field] < 10)
        {
          array_push($errors, $lang['conf_'.$field.'_error']);
        }
      }
      break;
    }
  }
  
  // updating configuration if no error found
  if (count($errors) == 0)
  {
    $result = mysql_query('SELECT * FROM '.CONFIG_TABLE);
    while ($row = mysql_fetch_array($result))
    {
      if (isset($_POST[$row['param']]))
      {
        $query = '
UPDATE '.CONFIG_TABLE.'
  SET value = \''. str_replace("\'", "''", $_POST[$row['param']]).'\'
  WHERE param = \''.$row['param'].'\'
;';
        mysql_query($query);
      }
    }
  }
}

//----------------------------------------------------- template initialization
$template->set_filenames( array('config'=>'admin/configuration.tpl') );

$action = PHPWG_ROOT_PATH.'admin.php?page=configuration';
$action.= '&amp;section='.$page['section'];

$template->assign_vars(
  array(
    'L_CONFIRM'=>$lang['conf_confirmation'],
    'L_YES'=>$lang['yes'],
    'L_NO'=>$lang['no'],
    'L_SUBMIT'=>$lang['submit'],
    'L_RESET'=>$lang['reset'],
    
    'F_ACTION'=>add_session_id($action)
    ));

switch ($page['section'])
{
  case 'general' :
  {
    $access_free = ($conf['access']=='free')?'checked="checked"':'';
    $access_restricted = ($conf['access']=='restricted')?'checked="checked"':'';
    $history_yes = ($conf['log']=='true')?'checked="checked"':'';
    $history_no  = ($conf['log']=='false')?'checked="checked"':'';
    $notif_yes = ($conf['mail_notification']=='true')?'checked="checked"':'';
    $notif_no = ($conf['mail_notification']=='false')?'checked="checked"':'';
    
    $template->assign_block_vars(
      'general',
      array(
        'L_CONF_TITLE'=>$lang['conf_general_title'],
        'L_CONF_MAIL'=>$lang['conf_mail_webmaster'],
        'L_CONF_MAIL_INFO'=>$lang['conf_mail_webmaster_info'],
        'L_CONF_TN_PREFIX'=>$lang['conf_prefix'],
        'L_CONF_TN_PREFIX_INFO'=>$lang['conf_prefix_info'],
        'L_CONF_ACCESS'=>$lang['conf_access'],
        'L_CONF_ACCESS_INFO'=>$lang['conf_access_info'],
        'L_CONF_ACCESS_FREE'=>$lang['free'],
        'L_CONF_ACCESS_RESTRICTED'=>$lang['restricted'],
        'L_CONF_HISTORY'=>$lang['history'],
        'L_CONF_HISTORY_INFO'=>$lang['conf_log_info'],
        'L_CONF_NOTIFICATION'=>$lang['conf_notification'],
        'L_CONF_NOTIFICATION_INFO'=>$lang['conf_notification_info'],
          
        'ADMIN_MAIL'=>$conf['mail_webmaster'],
        'THUMBNAIL_PREFIX'=>$conf['prefix_thumbnail'],
        'ACCESS_FREE'=>$access_free,
        'ACCESS_RESTRICTED'=>$access_restricted,
        'HISTORY_YES'=>$history_yes,
        'HISTORY_NO'=>$history_no,
        'NOTIFICATION_YES'=>$notif_yes,
        'NOTIFICATION_NO'=>$notif_no
        ));
    break;
  }
  case 'comments' :
  {
    $show_yes = ($conf['show_comments']=='true')?'checked="checked"':'';
    $show_no = ($conf['show_comments']=='false')?'checked="checked"':'';
    $all_yes = ($conf['comments_forall']=='true')?'checked="checked"':'';
    $all_no  = ($conf['comments_forall']=='false')?'checked="checked"':'';
    $validate_yes = ($conf['comments_validation']=='true')?'checked="checked"':'';
    $validate_no = ($conf['comments_validation']=='false')?'checked="checked"':'';
      
    $template->assign_block_vars(
      'comments',
      array(
        'L_CONF_TITLE'=>$lang['conf_comments_title'],
        'L_CONF_SHOW_COMMENTS'=>$lang['conf_show_comments'],
        'L_CONF_SHOW_COMMENTS_INFO'=>$lang['conf_show_comments_info'],
        'L_CONF_COMMENTS_ALL'=>$lang['conf_comments_forall'],
        'L_CONF_COMMENTS_ALL_INFO'=>$lang['conf_comments_forall_info'],
        'L_CONF_NB_COMMENTS_PAGE'=>$lang['conf_nb_comment_page'],
        'L_CONF_NB_COMMENTS_PAGE_INFO'=>$lang['conf_nb_comment_page'],
        'L_CONF_VALIDATE'=>$lang['conf_comments_validation'],
        'L_CONF_VALIDATE_INFO'=>$lang['conf_comments_validation_info'],
          
        'NB_COMMENTS_PAGE'=>$conf['nb_comment_page'],
        'SHOW_COMMENTS_YES'=>$show_yes,
        'SHOW_COMMENTS_NO'=>$show_no,
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
        'L_CONF_TITLE'=>$lang['conf_default_title'],
        'L_CONF_LANG'=>$lang['language'],
        'L_CONF_LANG_INFO'=>$lang['conf_default_language_info'],
        'L_NB_IMAGE_LINE'=>$lang['nb_image_per_row'],
        'L_NB_IMAGE_LINE_INFO'=>$lang['conf_nb_image_line_info'],
        'L_NB_ROW_PAGE'=>$lang['nb_row_per_page'],
        'L_NB_ROW_PAGE_INFO'=>$lang['conf_nb_line_page_info'],
        'L_CONF_STYLE'=>$lang['theme'],
        'L_CONF_STYLE_INFO'=>$lang['conf_default_theme_info'],
        'L_CONF_RECENT'=>$lang['recent_period'],
        'L_CONF_RECENT_INFO'=>$lang['conf_recent_period_info'],
        'L_CONF_EXPAND'=>$lang['auto_expand'],
        'L_CONF_EXPAND_INFO'=>$lang['conf_default_expand_info'],
        'L_NB_COMMENTS'=>$lang['show_nb_comments'],
        'L_NB_COMMENTS_INFO'=>$lang['conf_show_nb_comments_info'],
  
        'CONF_LANG_SELECT'=>language_select($conf['default_language'], 'default_language'),
        'NB_IMAGE_LINE'=>$conf['nb_image_line'],
        'NB_ROW_PAGE'=>$conf['nb_line_page'],
        'CONF_STYLE_SELECT'=>style_select($conf['default_template'], 'default_template'),
        'CONF_RECENT'=>$conf['recent_period'],
        'NB_COMMENTS_PAGE'=>$conf['nb_comment_page'],
        'EXPAND_YES'=>$expand_yes,
        'EXPAND_NO'=>$expand_no,
        'SHOW_COMMENTS_YES'=>$show_yes,
        'SHOW_COMMENTS_NO'=>$show_no
        ));
    break;
  }
  case 'upload' :
  {
    $upload_yes = ($conf['upload_available']=='true')?'checked="checked"':'';
    $upload_no = ($conf['upload_available']=='false')?'checked="checked"':'';
      
    $template->assign_block_vars(
      'upload',
      array(
        'L_CONF_TITLE'=>$lang['conf_upload_title'],
        'L_CONF_UPLOAD'=>$lang['conf_authorize_upload'],
        'L_CONF_UPLOAD_INFO'=>$lang['conf_authorize_upload_info'],
        'L_CONF_MAXSIZE'=>$lang['conf_upload_maxfilesize'],
        'L_CONF_MAXSIZE_INFO'=>$lang['conf_upload_maxfilesize_info'],
        'L_CONF_MAXWIDTH'=>$lang['conf_upload_maxwidth'],
        'L_CONF_MAXWIDTH_INFO'=>$lang['conf_upload_maxwidth_info'],
        'L_CONF_MAXHEIGHT'=>$lang['conf_upload_maxheight'],
        'L_CONF_MAXHEIGHT_INFO'=>$lang['conf_upload_maxheight_info'],
        'L_CONF_TN_MAXWIDTH'=>$lang['conf_upload_tn_maxwidth'],
        'L_CONF_TN_MAXWIDTH_INFO'=>$lang['conf_upload_tn_maxwidth_info'],
        'L_CONF_TN_MAXHEIGHT'=>$lang['conf_upload_tn_maxheight'],
        'L_CONF_TN_MAXHEIGHT_INFO'=>$lang['conf_upload_tn_maxheight_info'],
          
        'UPLOAD_MAXSIZE'=>$conf['upload_maxfilesize'],
        'UPLOAD_MAXWIDTH'=>$conf['upload_maxwidth'],
        'UPLOAD_MAXHEIGHT'=>$conf['upload_maxheight'],
        'TN_UPLOAD_MAXWIDTH'=>$conf['upload_maxwidth_thumbnail'],
        'TN_UPLOAD_MAXHEIGHT'=>$conf['upload_maxheight_thumbnail'],
        'UPLOAD_YES'=>$upload_yes,
        'UPLOAD_NO'=>$upload_no
        ));
    break;
  }
  case 'session' :
  {
    $authorize_remembering_yes =
      ($conf['authorize_remembering']=='true')?'checked="checked"':'';
    $authorize_remembering_no =
      ($conf['authorize_remembering']=='false')?'checked="checked"':'';
      
    $template->assign_block_vars(
      'session',
      array(
        'L_CONF_TITLE'=>$lang['conf_session_title'],
        'L_CONF_AUTHORIZE_REMEMBERING'=>$lang['conf_authorize_remembering'],
        'L_CONF_AUTHORIZE_REMEMBERING_INFO' =>
        $lang['conf_authorize_remembering_info'],

        'AUTHORIZE_REMEMBERING_YES'=>$authorize_remembering_yes,
        'AUTHORIZE_REMEMBERING_NO'=>$authorize_remembering_no
        ));
    break;
  }
  case 'metadata' :
  {
    $exif_yes = ($conf['use_exif']=='true')?'checked="checked"':'';
    $exif_no = ($conf['use_exif']=='false')?'checked="checked"':'';
    $iptc_yes = ($conf['use_iptc']=='true')?'checked="checked"':'';
    $iptc_no = ($conf['use_iptc']=='false')?'checked="checked"':'';
    $show_exif_yes = ($conf['show_exif']=='true')?'checked="checked"':'';
    $show_exif_no = ($conf['show_exif']=='false')?'checked="checked"':'';
    $show_iptc_yes = ($conf['show_iptc']=='true')?'checked="checked"':'';
    $show_iptc_no = ($conf['show_iptc']=='false')?'checked="checked"':'';
      
    $template->assign_block_vars(
      'metadata',
      array(
        'L_CONF_TITLE'=>$lang['conf_metadata_title'],
        'L_CONF_EXIF'=>$lang['conf_use_exif'],
        'L_CONF_EXIF_INFO'=>$lang['conf_use_exif_info'],
        'L_CONF_IPTC'=>$lang['conf_use_iptc'],
        'L_CONF_IPTC_INFO'=>$lang['conf_use_iptc_info'],
        'L_CONF_SHOW_EXIF'=>$lang['conf_show_exif'],
        'L_CONF_SHOW_EXIF_INFO'=>$lang['conf_show_exif_info'],
        'L_CONF_SHOW_IPTC'=>$lang['conf_show_iptc'],
        'L_CONF_SHOW_IPTC_INFO'=>$lang['conf_show_iptc_info'],
          
        'USE_EXIF_YES'=>$exif_yes,
        'USE_EXIF_NO'=>$exif_no,
        'USE_IPTC_YES'=>$iptc_yes,
        'USE_IPTC_NO'=>$iptc_no,
        'SHOW_EXIF_YES'=>$show_exif_yes,
        'SHOW_EXIF_NO'=>$show_exif_no,
        'SHOW_IPTC_YES'=>$show_iptc_yes,
        'SHOW_IPTC_NO'=>$show_iptc_no
        ));
    break;
  }
}
//-------------------------------------------------------------- errors display
if ( sizeof( $errors ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $errors ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$errors[$i]));
  }
}
elseif ( isset( $_POST['submit'] ) )
{
  $template->assign_block_vars('confirmation' ,array());
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'config');
?>
