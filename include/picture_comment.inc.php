<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

/**
 * This file is included by the picture page to manage user comments
 *
 */

// the picture is commentable if it belongs at least to one category which
// is commentable
$page['show_comments'] = false;
foreach ($related_categories as $category)
{
  if ($category['commentable'] == 'true')
  {
    $page['show_comments'] = true;
    break;
  }
}

if ( $page['show_comments'] and isset( $_POST['content'] ) )
{
  if ( $user['is_the_guest'] and !$conf['comments_forall'] )
  {
    die ('Session expired');
  }

  $comm = array(
    'author' => trim( stripslashes(@$_POST['author']) ),
    'content' => trim( stripslashes($_POST['content']) ),
    'image_id' => $page['image_id'],
   );

  include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');
  
  $comment_action = insert_user_comment($comm, @$_POST['key'], $infos );

  switch ($comment_action)
  {
    case 'moderate':
      array_push( $infos, $lang['comment_to_validate'] );
    case 'validate':
      array_push( $infos, $lang['comment_added']);
      break;
    case 'reject': 
      set_status_header(403);
      array_push($infos, l10n('comment_not_added') );
      break;
    default:
      trigger_error('Invalid comment action '.$comment_action, E_USER_WARNING);
  }

  foreach ($infos as $info)
  {
    $template->assign_block_vars(
        'information',
        array( 'INFORMATION'=>$info )
      );
  }

  // allow plugins to notify what's going on
  trigger_action( 'user_comment_insertion',
      array_merge($comm, array('action'=>$comment_action) )
    );
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
  if (!isset($page['start']))
  {
    $page['start'] = 0;
  }

  $page['navigation_bar'] = create_navigation_bar(
    duplicate_picture_url(array(), array('start')),
    $row['nb_comments'],
    $page['start'],
    $conf['nb_comment_page'],
    true // We want a clean URL
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

          'COMMENT' => trigger_event('render_comment_content',$row['content']),
          )
        );

      if (is_admin())
      {
        $template->assign_block_vars(
          'comments.comment.delete',
          array(
            'U_COMMENT_DELETE' =>
              add_url_params(
                    $url_self,
                    array(
                      'action'=>'delete_comment',
                      'comment_to_delete'=>$row['id']
                    )
                )
            )
          );
      }
    }
  }

  if (!$user['is_the_guest']
      or ($user['is_the_guest'] and $conf['comments_forall']))
  {
    include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');
    $key = get_comment_post_key($page['image_id']);
    $content = '';
    if ('reject'===@$comment_action)
    {
      $content = htmlspecialchars($comm['content']);
    }
    $template->assign_block_vars('comments.add_comment',
        array(
          'KEY' => $key,
          'CONTENT' => $content
        ));
    // display author field if the user is not logged in
    if ($user['is_the_guest'])
    {
      $template->assign_block_vars(
        'comments.add_comment.author_field', array()
        );
    }
  }
}

?>