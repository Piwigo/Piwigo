<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
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

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
//-------------------------------------------------- access authorization check
check_login_authorization();
//------------------------------------------------------------------ form check
$errors = array();
$search = array();
if (isset($_POST['submit']))
{
  if (isset($_POST['search_allwords'])
      and !preg_match('/^\s*$/', $_POST['search_allwords']))
  {
    $local_search = array();
    $search_allwords = $_POST['search_allwords'];
    $drop_char_match = array(
      '-','^','$',';','#','&','(',')','<','>','`','\'','"','|',',','@','_',
      '?','%','~','.','[',']','{','}',':','\\','/','=','\'','!','*');
    $drop_char_replace = array(
      ' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','','',' ',' ',' ',' ','',' ',
      ' ',' ',' ',' ',' ',' ',' ',' ','' ,' ',' ',' ',' ',' ');
    $search_allwords = str_replace($drop_char_match,
                                   $drop_char_replace,
                                   $search_allwords);
	
    // Split words
    $words = preg_split('/\s+/', $search_allwords);
    $words = array_unique($words);
    $search['fields']['allwords'] = array();
    $search['fields']['allwords']['words'] = $words;
    $search['fields']['allwords']['mode'] = $_POST['mode'];
  }
  
  if ($_POST['search_author'])
  {
    $search['fields']['author'] = array();
    $search['fields']['author']['words'] = array($_POST['search_author']);
  }
  
  if (isset($_POST['cat']))
  {
    $search['fields']['cat'] = array();
    $search['fields']['cat']['words'] = $_POST['cat'];
    if ($_POST['subcats-included'] == 1)
    {
      $search['fields']['cat']['mode'] = 'sub_inc';
    }
  }

  // dates
  $type_date = $_POST['date_type'];
  
  if (!empty($_POST['start_year']))
  {
    $year = $_POST['start_year'];
    $month = $_POST['start_month'] != 0 ? $_POST['start_month'] : '01';
    $day = $_POST['start_day'] != 0 ? $_POST['start_day'] : '01';
    $date = $year.'-'.$month.'-'.$day;
    
    $search['fields'][$type_date.'-after']['words'] = array($date);
    $search['fields'][$type_date.'-after']['mode'] = 'inc';
  }

  if (!empty($_POST['end_year']))
  {
    $year = $_POST['end_year'];
    $month = $_POST['end_month'] != 0 ? $_POST['end_month'] : '12';
    $day = $_POST['end_day'] != 0 ? $_POST['end_day'] : '31';
    $date = $year.'-'.$month.'-'.$day;
    
    $search['fields'][$type_date.'-before']['words'] = array($date);
    $search['fields'][$type_date.'-before']['mode'] = 'inc';
  }
    
  // search string (for URL) creation
  $search_string = '';
  $tokens = array();
  if (!empty($search))
  {
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
    $search_string.= implode('+', $tokens);
    if (count($tokens) > 1)
    {
      $search_string.= '|AND';
    }
  }
  else
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

// start date
get_day_list('start_day', @$_POST['start_day']);
get_month_list('start_month', @$_POST['start_month']);
// end date
get_day_list('end_day', @$_POST['end_day']);
get_month_list('end_month', @$_POST['end_month']);

//
// Start output of page
//
$title= $lang['search_title'];
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('search'=>'search.tpl') );
$template->assign_vars(array(
  'L_SEARCH_TITLE' => $lang['search_title'],
  'L_SEARCH_OPTIONS' => $lang['search_options'],
  'L_RETURN' => $lang['home'],
  'L_SUBMIT' => $lang['submit'],
  'L_RESET' => $lang['reset'],
  'L_SEARCH_KEYWORDS'=>$lang['search_keywords'],
  'L_SEARCH_KEYWORDS_HINT'=>$lang['search_keywords_hint'],
  'L_SEARCH_ANY_TERMS'=>$lang['search_mode_or'],
  'L_SEARCH_ALL_TERMS'=>$lang['search_mode_and'],
  'L_SEARCH_AUTHOR'=>$lang['search_author'],
  'L_SEARCH_AUTHOR_HINT'=>$lang['search_explain'],
  'L_SEARCH_CATEGORIES'=>$lang['search_categories'],
  'L_SEARCH_CATEGORIES_HINT'=>$lang['search_categories_hint'],
  'L_SEARCH_SUBFORUMS'=>$lang['search_subcats_included'],
  'L_YES' => $lang['yes'],
  'L_NO' => $lang['no'],
  'L_SEARCH_DATE' => $lang['search_date'],
  'L_SEARCH_DATE_HINT' => $lang['search_date_hint'],
  'L_TODAY' => $lang['today'],
  'L_SEARCH_DATE_FROM'=>$lang['search_date_from'],
  'L_SEARCH_DATE_TO'=>$lang['search_date_to'],
  'L_DAYS'=>$lang['days'],
  'L_MONTH'=>$lang['w_month'],
  'L_SEARCH_DATE_TYPE'=>$lang['search_date_type'],
  'L_SEARCH_CREATION'=>$lang['search_date_creation'],
  'L_SEARCH_AVAILABILITY'=>$lang['search_date_available'],
  'L_RESULT_SORT'=>$lang['search_sort'],
  'L_SORT_ASCENDING'=>$lang['search_ascending'],
  'L_SORT_DESCENDING'=>$lang['search_descending'],
  
  'TODAY_DAY' => date('d', time()),
  'TODAY_MONTH' => date('m', time()),
  'TODAY_YEAR' => date('Y', time()),
  'S_SEARCH_ACTION' => add_session_id( 'search.php' ),   
  'U_HOME' => add_session_id( 'category.php' )
  )
);

//------------------------------------------------------------- categories form
$query = '
SELECT name,id,date_last,nb_images,global_rank,uppercats
  FROM '.CATEGORIES_TABLE;
if ($user['forbidden_categories'] != '')
{
  $query.= '
  WHERE id NOT IN ('.$user['forbidden_categories'].')';
}
$query.= '
;';

$selecteds = array();
display_select_cat_wrapper($query, $selecteds, 'category_option', false);

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
$template->parse('search');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
