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

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');
//-------------------------------------------------------- sections definitions
if (!isset($_GET['section']))
{
  $page['section'] = 'general';
}
else
{
  $page['section'] = $_GET['section'];
}

// templates for fields definitions
$true_false = array('type' => 'radio',
                    'options' => array('true' => $lang['yes'],
                                       'false' => $lang['no']));
$textfield = array('type' => 'textfield');

$nb_image_row = array();
foreach ($conf['nb_image_row'] as $value)
{
  $nb_image_row[$value] = $value;
}

$nb_row_page = array();
foreach ($conf['nb_row_page'] as $value)
{
  $nb_row_page[$value] = $value;
}

$sections = array(
  'general' => array(
    'mail_webmaster' => $textfield,
    'prefix_thumbnail' => $textfield,
    'access' => array('type' => 'radio',
                      'options' => array(
                        'free' => $lang['conf_general_access_1'],
                        'restricted' => $lang['conf_general_access_2'])),
    'log' => $true_false,
    'mail_notification' => $true_false,
   ),
  'comments' => array(
    'show_comments' => $true_false,
    'comments_forall' => $true_false,
    'nb_comment_page' => array('type' => 'textfield','size' => 2),
    'comments_validation' => $true_false
   ),
  'default' => array(
    'default_language' => array('type' => 'select',
                                'options' => get_languages()),
    'nb_image_line' => array('type' => 'radio','options' => $nb_image_row),
    'nb_line_page' => array('type' => 'radio','options' => $nb_row_page),
    'default_template' => array('type' => 'select',
                                'options' => get_templates()),
    'recent_period' => array('type' => 'textfield','size' => 3),
    'auto_expand' => $true_false,
    'show_nb_comments' => $true_false
   ),
  'upload' => array(
    'upload_available' => $true_false,
    'upload_maxfilesize' => array('type' => 'textfield','size' => 4),
    'upload_maxwidth' => array('type' => 'textfield','size' => 4),
    'upload_maxheight' => array('type' => 'textfield','size' => 4),
    'upload_maxwidth_thumbnail' => array('type' => 'textfield','size' => 4),
    'upload_maxheight_thumbnail' => array('type' => 'textfield','size' => 4)
   ),
  'session' => array(
    'authorize_cookies' => $true_false,
    'session_time' => array('type' => 'textfield','size' => 2),
    'session_id_size' => array('type' => 'textfield','size' => 2)
   ),
  'metadata' => array(
    'use_exif' => $true_false,
    'use_iptc' => $true_false,
    'show_exif' => $true_false,
    'show_iptc' => $true_false
   )
 );
//------------------------------------------------------ $conf reinitialization
$result = mysql_query('SELECT param,value FROM '.CONFIG_TABLE);
while ($row = mysql_fetch_array($result))
{
  $conf[$row['param']] = $row['value'];
  // if the parameter is present in $_POST array (if a form is submited), we
  // override it with the submited value
  if (isset($_POST[$row['param']]))
  {
    $conf[$row['param']] = $_POST[$row['param']];
  }
}
//------------------------------ verification and registration of modifications
$errors = array();
if (isset($_POST['submit']))
{
//   echo '<pre>';
//   print_r($_POST);
//   echo '</pre>';
  
  $int_pattern = '/^\d+$/';
  switch ($page['section'])
  {
    case 'general' :
    {
      // thumbnail prefix must only contain simple ASCII characters
      if (!preg_match('/^[\w-]*$/', $_POST['prefix_thumbnail']))
      {
        array_push($errors, $lang['conf_general_prefix_thumbnail_error']);
      }
      // mail must be formatted as follows : name@server.com
      $pattern = '/^[\w-]+(\.[\w-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$/';
      if (!preg_match($pattern, $_POST['mail_webmaster']))
      {
        array_push($errors, $lang['conf_general_mail_webmaster_error']);
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
        array_push($errors, $lang['conf_comments_nb_comment_page_error']);
      }
      break;
    }
    case 'default' :
    {
      // periods must be integer values, they represents number of days
      if (!preg_match($int_pattern, $_POST['recent_period'])
          or $_POST['recent_period'] <= 0)
      {
        array_push($errors, $lang['conf_default_recent_period_error']);
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
        array_push($errors, $lang['conf_upload_upload_maxfilesize_error']);
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
          array_push($errors, $lang['conf_upload_'.$field.'_error']);
        }
      }
      break;
    }
    case 'session' :
    {
      // session_id size must be an integer between 4 and 50
      if (!preg_match($int_pattern, $_POST['session_id_size'])
          or $_POST['session_id_size'] < 4
          or $_POST['session_id_size'] > 50)
      {
        array_push($errors, $lang['conf_session_session_id_size_error']);
      }
      // session_time must be an integer between 5 and 60, in minutes
      if (!preg_match($int_pattern, $_POST['session_time'])
          or $_POST['session_time'] < 5
          or $_POST['session_time'] > 60)
      {
        array_push($errors, $lang['conf_session_session_time_error']);
      }
      break;
    }
  }
  
  // updating configuraiton if no error found
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
$template->set_filenames(array('config'=>'admin/configuration.tpl'));

$action = PHPWG_ROOT_PATH.'admin.php?page=configuration';
$action.= '&amp;section='.$page['section'];

$template->assign_vars(
  array(
    'L_CONFIRM'=>$lang['conf_confirmation'],
    'L_SUBMIT'=>$lang['submit'],
    'F_ACTION'=>add_session_id($action)
   )
 );

$base_url = PHPWG_ROOT_PATH.'admin.php?page=configuration&amp;section=';
foreach (array_keys($sections) as $section)
{
  if ($section == $page['section'])
  {
    $class = 'opened';
  }
  else
  {
    $class = '';
  }
  
  $template->assign_block_vars(
    'confmenu_item',
    array(
      'CLASS' => $class,
      'URL' => add_session_id($base_url.$section),
      'NAME' => $lang['conf_'.$section.'_title']
     ));
}

$fields = $sections[$page['section']];
foreach ($fields as $field_name => $field)
{
  $template->assign_block_vars(
    'line',
    array(
      'NAME' => $lang['conf_'.$page['section'].'_'.$field_name],
      'INFO' => $lang['conf_'.$page['section'].'_'.$field_name.'_info']
     ));
  if ($field['type'] == 'textfield')
  {
    if (isset($field['size']))
    {
      $size = $field['size'];
    }
    else
    {
      $size = '';
    }
    
    $template->assign_block_vars(
      'line.textfield',
      array(
        'NAME' => $field_name,
        'VALUE' => $conf[$field_name],
        'SIZE' => $size
       ));
  }
  else if ($field['type'] == 'radio')
  {
    foreach ($field['options'] as $option_value => $option)
    {
      if ($conf[$field_name] == $option_value)
      {
        $checked = 'checked="checked"';
      }
      else
      {
        $checked = '';
      }
      
      $template->assign_block_vars(
        'line.radio',
        array(
          'NAME' => $field_name,
          'VALUE' => $option_value,
          'CHECKED' => $checked,
          'OPTION' => $option
         ));
    }
  }
  else if ($field['type'] == 'select')
  {
    $template->assign_block_vars(
      'line.select',
      array(
        'NAME' => $field_name
       ));
    foreach ($field['options'] as $option_value => $option)
    {
      if ($conf[$field_name] == $option_value)
      {
        $selected = 'selected="selected"';
      }
      else
      {
        $selected = '';
      }
      
      $template->assign_block_vars(
        'line.select.select_option',
        array(
          'VALUE' => $option_value,
          'SELECTED' => $selected,
          'OPTION' => $option
         ));
    }
  }
}
//-------------------------------------------------------------- errors display
if (count($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}
else if (isset($_POST['submit']))
{
  $template->assign_block_vars('confirmation' ,array());
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'config');
?>
