<?php
// +-----------------------------------------------------------------------+
// |                             comments.php                              |
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

//----------------------------------------------------------- include
if (!defined('IN_ADMIN'))
{
  define('PHPWG_ROOT_PATH','./');
  include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
}

//--------------------------------------------------- number of days to display
if ( isset( $_GET['last_days'] ) ) define( 'MAX_DAYS', $_GET['last_days'] );
else                               define( 'MAX_DAYS', 0 );
//----------------------------------------- non specific section initialization
$array_cat_directories = array();
$array_cat_names       = array();
$array_cat_site_id     = array();

// comment deletion
if ( isset( $_POST['delete'] ) )
{
  $mod_sql='';
  while( list($id, $row_id) = @each($_POST['comment_id']) )
  {
	$mod_sql .= ( ( $mod_sql != '' ) ? ', ' : '' ) . $row_id;
  }
  $query = 'DELETE FROM '.COMMENTS_TABLE.' WHERE id IN ('.$mod_sql.');';
  mysql_query( $query );
}

//--------------------------------------------------------- comments validation
if ( isset( $_POST['validate'] ) )
{
  $mod_sql='';
  while( list($id, $row_id) = @each($_POST['comment_id']) )
  {
	$mod_sql .= ( ( $mod_sql != '' ) ? ', ' : '' ) . $row_id;
  }
  $query = 'UPDATE '.COMMENTS_TABLE;
  $query.= " SET validated = 'true'";
  $query.=' WHERE id IN ('.$mod_sql.');';
  mysql_query( $query );
}
//------------------------------------------------------- last comments display

//
// Start output of page
//
if (!defined('IN_ADMIN'))
{
  $title= $lang['title_comments'];
  include(PHPWG_ROOT_PATH.'include/page_header.php');
}

$template->set_filenames( array('comments'=>'comments.tpl') );
$template->assign_vars(array(
  'L_COMMENT_TITLE' => $title,
  'L_COMMENT_STATS' => $lang['stats_last_days'],
  'L_COMMENT_RETURN' => $lang['return_main_page'],
  'L_DELETE' =>$lang['delete'],
  'L_VALIDATE'=>$lang['submit'],
  
  'T_DEL_IMG' =>PHPWG_ROOT_PATH.'template/'.$user['template'].'/theme/delete.gif',
  
  'U_HOME' => add_session_id( PHPWG_ROOT_PATH.'category.php' )
  )
);

foreach ( $conf['last_days'] as $option ) {
  $url = $_SERVER['PHP_SELF'].'?last_days='.($option - 1);
  if (defined('IN_ADMIN')) $url.= '&amp;page=comments';
  $template->assign_block_vars(
    'last_day_option',
    array(
      'OPTION'=>$option,
      'T_STYLE'=>(( $option == MAX_DAYS + 1 )?'text-decoration:underline;':''),
      'U_OPTION'=>add_session_id( $url )
      )
    );
}

// 1. retrieving picture ids which have comments recently added
$date = date( 'Y-m-d', time() - ( MAX_DAYS*24*60*60 ) );
list($year,$month,$day) = explode( '-', $date);
$maxtime = mktime( 0,0,0,$month,$day,$year );
$query = 'SELECT DISTINCT(ic.image_id) as image_id,';
$query.= '(ic.category_id) as category_id';
$query.= ' FROM '.COMMENTS_TABLE.' AS c';
$query.= ', '.IMAGE_CATEGORY_TABLE.' AS ic';
$query.= ' WHERE c.image_id = ic.image_id';
$query.= ' AND date > FROM_UNIXTIME('.$maxtime.')';
if ( $user['status'] != 'admin' )
{
  $query.= " AND validated = 'true'";
  // we must not show pictures of a forbidden category
  if ( $user['forbidden_categories'] != '' )
  {
    $query.= ' AND category_id NOT IN ';
    $query.= '('.$user['forbidden_categories'].')';
  }
}
$query.= ' ORDER BY ic.image_id DESC';
$query.= ';';
$result = mysql_query( $query );
if ( $user['status'] == 'admin' )
{
  $template->assign_block_vars('validation', array());
}
while ( $row = mysql_fetch_array( $result ) )
  {
    $category_id=$row['category_id'];

    // for each picture, getting informations for displaying thumbnail and
    // link to the full size picture
    $query = 'SELECT name,file,storage_category_id as cat_id,tn_ext';
    $query.= ' FROM '.IMAGES_TABLE;
    $query.= ' WHERE id = '.$row['image_id'];
    $query.= ';';
    $subresult = mysql_query( $query );
    $subrow = mysql_fetch_array( $subresult );

    if ( !isset($array_cat_directories[$subrow['cat_id']]) )
    {
      $array_cat_directories[$subrow['cat_id']] =
        get_complete_dir( $subrow['cat_id'] );
      $cat_result = get_cat_info( $subrow['cat_id'] );
      $array_cat_site_id[$subrow['cat_id']] = $cat_result['site_id'];
      $array_cat_names[$subrow['cat_id']] =
        get_cat_display_name( $cat_result['name'], ' &gt; ', '' );
    }

    $file = get_filename_wo_extension( $subrow['file'] );
    // name of the picture
    $name = $array_cat_names[$category_id].' &gt; ';
    if (!empty($subrow['name'])) $name.= $subrow['name'];
    else                         $name.= str_replace( '_', ' ', $file );
    $name.= ' [ '.$subrow['file'].' ]';
    // source of the thumbnail picture
    if (isset($subrow['tn_ext']) and $subrow['tn_ext'] != '')
    {
      $src = $array_cat_directories[$subrow['cat_id']];
      $src.= 'thumbnail/'.$conf['prefix_thumbnail'];
      $src.= $file.'.'.$subrow['tn_ext'];
    }
    else
    {
      $src = './template/'.$user['template'].'/mimetypes/';
      $src.= strtolower(get_extension($subrow['file'])).'.png';
    }
    
    // link to the full size picture
    $url = PHPWG_ROOT_PATH.'picture.php?cat='.$category_id;
    $url.= '&amp;image_id='.$row['image_id'];
    
    $template->assign_block_vars(
      'picture',
      array(
        'TITLE_IMG'=>$name,
        'I_THUMB'=>$src,
        'U_THUMB'=>add_session_id( $url )
        ));
    
    // for each picture, retrieving all comments
    $query = 'SELECT * FROM '.COMMENTS_TABLE;
    $query.= ' WHERE image_id = '.$row['image_id'];
    $query.= ' AND date > FROM_UNIXTIME('.$maxtime.')';
    if ( $user['status'] != 'admin' )
    {
      $query.= " AND validated = 'true'";
    }
    $query.= ' ORDER BY date DESC';
    $query.= ';';
    $handleresult = mysql_query( $query );
    while ( $subrow = mysql_fetch_array( $handleresult ) )
    {
      $author = $subrow['author'];
      if ( empty($subrow['author'] )) $author = $lang['guest'];
      $content = nl2br( $subrow['content'] );
      
      // replace _word_ by an underlined word
      $pattern = '/_([^\s]*)_/';
      $replacement = '<span style="text-decoration:underline;">\1</span>';
      $content = preg_replace( $pattern, $replacement, $content );
      
      // replace *word* by a bolded word
      $pattern = '/\*([^\s]*)\*/';
      $replacement = '<span style="font-weight:bold;">\1</span>';
      $content = preg_replace( $pattern, $replacement, $content );

      // replace /word/ by an italic word
      $pattern = '/\/([^\s]*)\//';
      $replacement = '<span style="font-style:italic;">\1</span>';
      $content = preg_replace( $pattern, $replacement, $content );
      $template->assign_block_vars(
        'picture.comment',array(
          'COMMENT_AUTHOR'=>$author,
          'COMMENT_DATE'=>format_date( $subrow['date'],'mysql_datetime',true ),
          'COMMENT'=>$content,
          ));
      if ( $user['status'] == 'admin' )
      {
        $template->assign_block_vars(
          'picture.comment.validation', array(
            'ID'=> $subrow['id'],
            'CHECKED'=>($subrow['validated']=='false')?'checked="checked"': ''
            ));
      }
    }
  }
//----------------------------------------------------------- html code display
if (defined('IN_ADMIN'))
{
  $template->assign_var_from_handle('ADMIN_CONTENT', 'comments');
}
else
{
  $template->assign_block_vars('title',array());
  $template->pparse('comments');
  include(PHPWG_ROOT_PATH.'include/page_tail.php');
}
?>
