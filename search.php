<?php
// +-----------------------------------------------------------------------+
// |                              search.php                               |
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

//------------------------------------------------------------------- functions
// date_display displays 3 select input fields. The first one is the
// day of the month, from 0 to 31. The second is the month of the year,
// from 01 to 12. The last one is the year. The years displayed are the
// ones given by get_available_years (see function description in
// ./include/functions.inc.php).
function display_date($fieldname, $datefield)
{
  global $template;

  // years
  for ($i = 1990; $i < 2006; $i++)
  {
    $selected = '';
    $key = $datefield.':year';
    if (isset($_POST[$key]) and $i == $_POST[$key])
    {
      $selected = ' selected="selected"';
    }
    
    $template->assign_block_vars(
      $fieldname.'year_option',
      array('OPTION'=>$i,
            'SELECTED'=>$selected
        ));
  }
  // months of year
  for ($i = 1; $i <= 12; $i++)
  {
    $selected = '';
    $key = $datefield.':month';
    if (isset($_POST[$key]) and $i == $_POST[$key])
    {
      $selected = ' selected="selected"';
    }

    $template->assign_block_vars(
      $fieldname.'month_option',
      array('OPTION'=>sprintf('%02s', $i),
            'SELECTED'=>$selected
        ));
  }
  // days of the month
  for ($i = 1; $i <= 31; $i++)
  {
    $selected = '';
    $key = $datefield.':day';
    if (isset($_POST[$key]) and $i == $_POST[$key])
    {
      $selected = ' selected="selected"';
    }

    $template->assign_block_vars(
      $fieldname.'day_option',
      array('OPTION'=>sprintf('%02s', $i),
            'SELECTED'=>$selected
        ));
  }
}

function display_3dates($fieldname)
{
  display_date('datefield.', $fieldname);
  display_date('datefield.after_', $fieldname.'-after');
  display_date('datefield.before_', $fieldname.'-before');
}
//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
//-------------------------------------------------- access authorization check
check_login_authorization();
//----------------------------------------------------------------- form fields
$textfields = array('file', 'name', 'comment', 'keywords', 'author');
$datefields = array('date_available', 'date_creation');
//------------------------------------------------------------------ form check
$errors = array();
$search = array();
$search['fields'] = array();
if (isset($_POST['submit']))
{
  $search['mode'] = $_POST['mode'];

  foreach ($textfields as $textfield)
  {
    if (isset($_POST[$textfield.'-content'])
        and !preg_match('/^\s*$/', $_POST[$textfield.'-content']))
    {
      $local_search = array();
      $words = preg_split('/\s+/', $_POST[$textfield.'-content']);
      foreach ($words as $i => $word)
      {
        if (strlen($word) > 2 and !preg_match('/[,;:\']/', $word))
        {
          array_push($local_search, $word);
        }
        else
        {
          array_push($errors, $lang['invalid_search']);
        }
      }
      $local_search = array_unique($local_search);
      $search['fields'][$textfield] = array();
      $search['fields'][$textfield]['words'] = $local_search;
      if (count($local_search) > 1)
      {
        $search['fields'][$textfield]['mode'] = $_POST[$textfield.'-mode'];
      }
    }
  }
  foreach ($datefields as $datefield)
  {
    $suffixes = array('','-after','-before');
    foreach ($suffixes as $suffix)
    {
      $field = $datefield.$suffix;
      if (isset($_POST[$field.'-check']))
      {
        $year = $_POST[$field.':year'];
        $month = $_POST[$field.':month'];
        $day = $_POST[$field.':day'];
        $date = $year.'.'.$month.'.'.$day;
        if (!checkdate($month, $day, $year))
        {
          array_push($errors, $date.$lang['search_wrong_date']);
        }
        $search['fields'][$field] = array();
        $search['fields'][$field]['words'] = array($date);
        if ($suffix == '-after' or $suffix == '-before')
        {
          if (isset($_POST[$field.'-included']))
          {
            $search['fields'][$field]['mode'] = 'inc';
          }
        }
      }
    }
    if ($search['mode'] == 'AND')
    {
      // before date must be superior to after date
      if (isset($search['fields'][$datefield.'-before'])
          and isset($search['fields'][$datefield.'-after']))
      {
        $after = $search['fields'][$datefield.'-after']['words'][0];
        $before = $search['fields'][$datefield.'-before']['words'][0];
        if ($after >= $before)
        {
          array_push($errors, $lang['search_wrong_date_order']);
        }
      }
      // having "search is" and ("search is after" or "search is before") is
      // not coherent
      if (isset($search['fields'][$datefield])
          and (isset($search['fields'][$datefield.'-before'])
               or isset($search['fields'][$datefield.'-after'])))
      {
        array_push($errors, $lang['search_incoherent_date_search']);
      }
    }
  }
  if (isset($_POST['categories-check']))
  {
    $field = 'cat';
    $search['fields'][$field] = array();
    $search['fields'][$field]['words'] = $_POST['cat'];
    if (isset($_POST['subcats-included']))
    {
      $search['fields'][$field]['mode'] = 'sub_inc';
    }
  }
  // search string (for URL) creation
  $search_string = '';
  $tokens = array();
  foreach (array_keys($search['fields']) as $field)
  {
    $token = $field.':';
    $token.= implode(',', $search['fields'][$field]['words']);
    if (isset($search['fields'][$field]['mode']))
    {
      $token.= '~'.$search['fields'][$field]['mode'];
    }
    array_push($tokens, $token);
  }
  $search_string.= implode(';', $tokens);
  if (count($tokens) > 1)
  {
    $search_string.= '|'.$search['mode'];
  }
  
  if (count($tokens) == 0)
  {
    array_push($errors, $lang['search_one_clause_at_least']);
  }
}
//----------------------------------------------------------------- redirection
if (isset($_POST['submit']) and count($errors) == 0)
{
  $url = 'category.php?cat=search&search='.$search_string;
  $url = add_session_id($url, true);
  redirect($url);
}
//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= $lang['search_title'];
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('search'=>'search.tpl') );
$template->assign_vars(array(
  'L_TITLE' => $lang['search_title'],
  'L_SEARCH_COMMENTS' => $lang['search_comments'],
  'L_RETURN' => $lang['gallery_index'],
  'L_SUBMIT' => $lang['submit'],
  'L_SEARCH_OR'=>$lang['search_mode_or'],
  'L_SEARCH_AND'=>$lang['search_mode_and'],
  'L_SEARCH_OR_CLAUSES'=>$lang['search_or_clauses'],
  'L_SEARCH_AND_CLAUSES'=>$lang['search_and_clauses'],
  'L_SEARCH_CATEGORIES'=>$lang['categories'],
  'L_SEARCH_SUBCATS_INCLUDED'=>$lang['search_subcats_included'],
  'L_SEARCH_DATE_INCLUDED'=> $lang['search_date_included'],
  'L_SEARCH_DATE_IS'=>$lang['search_date_is'],
  'L_SEARCH_DATE_IS_AFTER'=>$lang['search_date_is_after'],
  'L_SEARCH_DATE_IS_BEFORE'=>$lang['search_date_is_before'],
  
  'F_ACTION' => add_session_id( 'search.php' ),
    
  'U_HOME' => add_session_id( 'category.php' )
  )
);

//------------------------------------------------------------ text fields form
foreach ($textfields as $textfield)
{
  if (isset($_POST[$textfield.'-mode']))
  {
    if ($_POST[$textfield.'-mode'] == 'AND')
    {
      $and_checked = 'checked="checked"';
      $or_checked  = '';
    }
    else
    {
      $or_checked  = 'checked="checked"';
      $and_checked = '';
    }
  }
  else
  {
    $or_checked  = 'checked="checked"';
    $and_checked = '';
  }

  $value = '';
  if (isset($_POST[$textfield.'-content']))
  {
    $value = $_POST[$textfield.'-content'];
  }
  
  $template->assign_block_vars(
    'textfield',
    array('NAME'=>$lang['search_'.$textfield],
          'L_NAME'=>$textfield,
          'VALUE'=>$value,
          'OR_CHECKED'=>$or_checked,
          'AND_CHECKED'=>$and_checked
          ));
}
//------------------------------------------------------------- date field form
foreach ($datefields as $datefield)
{
  $checked = '';
  if (isset($_POST[$datefield.'-check']))
  {
    $checked = ' checked="checked"';
  }

  $after_checked = '';
  if (isset($_POST[$datefield.'-after-check']))
  {
    $after_checked = ' checked="checked"';
  }

  $before_checked = '';
  if (isset($_POST[$datefield.'-before-check']))
  {
    $before_checked = ' checked="checked"';
  }

  $after_included_check = '';
  if (isset($_POST[$datefield.'-after-included']))
  {
    $after_included_check = ' checked="checked"';
  }

  $before_included_check = '';
  if (isset($_POST[$datefield.'-before-included']))
  {
    $before_included_check = ' checked="checked"';
  }
  
  $template->assign_block_vars(
    'datefield',
    array('NAME'=>$datefield,
          'L_NAME'=>$lang['search_'.$datefield],
          'CHECKED'=>$checked,
          'AFTER_CHECKED'=>$after_checked,
          'BEFORE_CHECKED'=>$before_checked,
          'AFTER_INCLUDED_CHECKED'=>$after_included_check,
          'BEFORE_INCLUDED_CHECKED'=>$before_included_check
          ));
  display_3dates($datefield);
}
//------------------------------------------------------------- categories form
// this is a trick : normally, get_user_plain_structure is used to create
// the categories structure for menu (in category.php) display, but here, we
// want all categories to be shown...
$user['expand'] = true;
$structure = create_user_structure('');

$selecteds = array();
if (isset($_POST['submit']))
{
  $selecteds = $_POST['cat'];
}
display_select_categories($structure,
                          '&nbsp;',
                          $selecteds,
                          'category_option');

$categories_selected = '';
if (isset($_POST['categories-check']))
{
  $categories_selected = 'checked="checked"';
}

$categories_subcats_selected = '';
if (isset($_POST['subcats-included']))
{
  $categories_subcats_selected = 'checked="checked"';
}

$template->assign_vars(
  array(
    'CATEGORIES_SELECTED'=>$categories_selected,
    'CATEGORIES_SUBCATS_SELECTED'=>$categories_subcats_selected
    )
  );
//---------------------------------------------------------------------- OR/AND
if (isset($_POST['mode']))
{
  if ($_POST['mode'] == 'AND')
  {
    $and_checked = 'checked="checked"';
    $or_checked  = '';
  }
  else
  {
    $or_checked  = 'checked="checked"';
    $and_checked = '';
  }
}
else
{
  $or_checked  = 'checked="checked"';
  $and_checked = '';
}

$template->assign_vars(
  array(
    'OR_CHECKED'=>$or_checked,
    'AND_CHECKED'=>$and_checked
    )
  );
//-------------------------------------------------------------- errors display
if (sizeof($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}
//------------------------------------------------------------ log informations
pwg_log( 'search', $title );
mysql_close();
$template->pparse('search');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
