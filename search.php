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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
//-------------------------------------------------- access authorization check
check_login_authorization();
//----------------------------------------------------------------- redirection
$error = array();
if ( isset( $_POST['search'] ) )
{
  $redirect = true;
  $search = array();
  $words = preg_split( '/\s+/', $_POST['search'] );
  foreach ( $words as $i => $word ) {
    if ( strlen( $word ) > 2 and !preg_match( '/[,;:\']/', $word ) )
    {
      array_push( $search, $word );
    }
    else
    {
      $redirect = false;
      array_push( $error, $lang['invalid_search'] );
      break;
    }
  }
  $search = array_unique( $search );
  $search = implode( ',', $search );
  if ( $redirect )
  {
    $url = 'category.php?cat=search&search='.$search.'&mode='.$_POST['mode'];
    $url = add_session_id( $url, true );
    redirect( $url );
  }
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
  'L_COMMENTS' => $lang['search_comments'],
  'L_RETURN' => $lang['search_return_main_page'],
  'L_SUBMIT' => $lang['submit'],
  'L_SEARCH'=>$lang['search_field_search'].' *',
  'L_SEARCH_OR'=>$lang['search_mode_or'],
  'L_SEARCH_AND'=>$lang['search_mode_and'],
  
  'F_ACTION' => add_session_id( 'search.php' ),
  'F_TEXT_VALUE' => isset($_POST['search'])?$_POST['search']:'',
    
  'U_HOME' => add_session_id( 'category.php' )
  )
);

//-------------------------------------------------------------- errors display
if ( sizeof( $error ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $error ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error[$i]));
  }
}
//------------------------------------------------------------ log informations
pwg_log( 'search', $title );
mysql_close();
$template->pparse('search');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
