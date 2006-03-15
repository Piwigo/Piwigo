<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-03-09 00:14:53 +0100 (jeu, 09 mar 2006) $
// | last modifier : $Author: rub $
// | revision      : $Revision: 1070 $
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

/**
 * This file is included by the picture page to manage user comments
 * 
 */

if ( isset( $_POST['content'] ) && !empty($_POST['content']) )
{
  $register_comment = true;
  $author = !empty($_POST['author'])?$_POST['author']:$lang['guest'];
  // if a guest try to use the name of an already existing user, he must be
  // rejected
  if ( $author != $user['username'] )
  {
    $query = 'SELECT COUNT(*) AS user_exists';
    $query.= ' FROM '.USERS_TABLE;
    $query.= ' WHERE '.$conf['user_fields']['username']." = '".$author."'";
    $query.= ';';
    $row = mysql_fetch_array( pwg_query( $query ) );
    if ( $row['user_exists'] == 1 )
    {
      $template->assign_block_vars(
        'information',
        array('INFORMATION'=>$lang['comment_user_exists']));
      $register_comment = false;
    }
  }

  if ( $register_comment )
  {
    // anti-flood system
    $reference_date = time() - $conf['anti-flood_time'];
    $query = 'SELECT id FROM '.COMMENTS_TABLE;
    $query.= ' WHERE date > FROM_UNIXTIME('.$reference_date.')';
    $query.= " AND author = '".$author."'";
    $query.= ';';
    if ( mysql_num_rows( pwg_query( $query ) ) == 0
         or $conf['anti-flood_time'] == 0 )
    {
      list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

      $data = array();
      $data{'author'} = $author;
      $data{'date'} = $dbnow;
      $data{'image_id'} = $page['image_id'];
      $data{'content'} = htmlspecialchars( $_POST['content'], ENT_QUOTES);

      if (!$conf['comments_validation'] or is_admin())
      {
        $data{'validated'} = 'true';
        $data{'validation_date'} = $dbnow;
      }
      else
      {
        $data{'validated'} = 'false';
      }

      include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
      $fields = array('author', 'date', 'image_id', 'content', 'validated',
                      'validation_date');
      mass_inserts(COMMENTS_TABLE, $fields, array($data));

      // information message
      $message = $lang['comment_added'];

      if (!$conf['comments_validation'] or is_admin())

      if ( $conf['comments_validation'] and !is_admin() )
      {
        $message.= '<br />'.$lang['comment_to_validate'];
      }
      $template->assign_block_vars('information',
                                   array('INFORMATION'=>$message));
    }
    else
    {
      // information message
      $template->assign_block_vars(
        'information',
        array('INFORMATION'=>$lang['comment_anti-flood']));
    }
  }
}

// the picture is commentable if it belongs at least to one category which
// is commentable
$page['show_comments'] = false;
foreach ($related_categories as $category)
{
  if ($category['commentable'] == 'true')
  {
    $page['show_comments'] = true;
  }
}

if ($page['show_comments'])
{
  // number of comment for this picture
  $query = 'SELECT COUNT(*) AS nb_comments';
  $query.= ' FROM '.COMMENTS_TABLE.' WHERE image_id = '.$page['image_id'];
  $query.= " AND validated = 'true'";
  $query.= ';';
  $row = mysql_fetch_array( pwg_query( $query ) );

  // navigation bar creation
  $url = PHPWG_ROOT_PATH.'picture.php';
  $url.= get_query_string_diff(array('add_fav','start'));

//   $url = duplicate_picture_URL(
//     array(),
//     array('start')
//     );

  if (!isset($_GET['start'])
      or !is_numeric($_GET['start'])
      or (is_numeric($_GET['start']) and $_GET['start'] < 0))
  {
    $page['start'] = 0;
  }
  else
  {
    $page['start'] = $_GET['start'];
  }
  $page['navigation_bar'] = create_navigation_bar(
    $url,
    $row['nb_comments'],
    $page['start'],
    $conf['nb_comment_page'],
    ''
    );
  
  $template->assign_block_vars(
    'comments',
    array(
      'NB_COMMENT' => $row['nb_comments'],
      'NAV_BAR' => $page['navigation_bar'],
      )
    );

  if ($row['nb_comments'] > 0)
  {
    $query = '
SELECT id,author,date,image_id,content
  FROM '.COMMENTS_TABLE.'
  WHERE image_id = '.$page['image_id'].'
    AND validated = \'true\'
  ORDER BY date ASC
  LIMIT '.$page['start'].', '.$conf['nb_comment_page'].'
;';
    $result = pwg_query( $query );

    while ($row = mysql_fetch_array($result))
    {
      $template->assign_block_vars(
        'comments.comment',
        array(
          'COMMENT_AUTHOR' => empty($row['author'])
            ? $lang['guest']
            : $row['author'],
          
          'COMMENT_DATE' => format_date(
            $row['date'],
            'mysql_datetime',
            true),
          
          'COMMENT' => parse_comment_content($row['content']),
          )
        );

      if (is_admin())
      {
        $template->assign_block_vars(
          'comments.comment.delete',
          array(
            'U_COMMENT_DELETE' => $url.'&amp;del='.$row['id']
            )
          );
      }
    }
  }

  if (!$user['is_the_guest']
      or ($user['is_the_guest'] and $conf['comments_forall']))
  {
    $template->assign_block_vars('comments.add_comment', array());
    // display author field if the user is not logged in
    if (!$user['is_the_guest'])
    {
      $template->assign_block_vars(
        'comments.add_comment.author_known',
        array('KNOWN_AUTHOR'=>$user['username'])
        );
    }
    else
    {
      $template->assign_block_vars(
        'comments.add_comment.author_field', array()
        );
    }
  }
}

?>