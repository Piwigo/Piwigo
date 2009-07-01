<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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
  if ( is_a_guest() and !$conf['comments_forall'] )
  {
    die ('Session expired');
  }

  $comm = array(
    'author' => trim(@$_POST['author']),
    'content' => trim($_POST['content']),
    'image_id' => $page['image_id'],
   );

  include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');

  $comment_action = insert_user_comment($comm, @$_POST['key'], $infos );

  switch ($comment_action)
  {
    case 'moderate':
      array_push( $infos, l10n('comment_to_validate') );
    case 'validate':
      array_push( $infos, l10n('comment_added'));
      break;
    case 'reject':
      set_status_header(403);
      array_push($infos, l10n('comment_not_added') );
      break;
    default:
      trigger_error('Invalid comment action '.$comment_action, E_USER_WARNING);
  }

  $template->assign(
      ($comment_action=='reject') ? 'errors' : 'infos',
      $infos
    );

  // allow plugins to notify what's going on
  trigger_action( 'user_comment_insertion',
      array_merge($comm, array('action'=>$comment_action) )
    );
}
elseif ( isset($_POST['content']) )
{
  set_status_header(403);
  die('ugly spammer');
}

if ($page['show_comments'])
{
  // number of comments for this picture
  $query = '
SELECT COUNT(*) AS nb_comments
  FROM '.COMMENTS_TABLE.'
  WHERE image_id='.$page['image_id']." AND validated = 'true'";
  $row = mysql_fetch_array( pwg_query( $query ) );

  // navigation bar creation
  if (!isset($page['start']))
  {
    $page['start'] = 0;
  }

  $navigation_bar = create_navigation_bar(
    duplicate_picture_url(array(), array('start')),
    $row['nb_comments'],
    $page['start'],
    $conf['nb_comment_page'],
    true // We want a clean URL
    );

  $template->assign(
    array(
      'COMMENT_COUNT' => $row['nb_comments'],
      'navbar' => $navigation_bar,
      )
    );

  if ($row['nb_comments'] > 0)
  {
    if ( !is_admin() )
    {
      $validated_clause = '  AND validated = \'true\'';
    }
    else
    {
      $validated_clause = '';
    }

    $query = '
SELECT com.id,author,author_id,'.$conf['user_fields']['username'].' AS username,
  date,image_id,content,validated
  FROM '.COMMENTS_TABLE.' AS com
  LEFT JOIN '.USERS_TABLE.' AS u
    ON u.'.$conf['user_fields']['id'].' = author_id
  WHERE image_id = '.$page['image_id'].
$validated_clause.'
  ORDER BY date ASC
  LIMIT '.$page['start'].', '.$conf['nb_comment_page'].'
;';
    $result = pwg_query( $query );

    while ($row = mysql_fetch_array($result))
    {
      if (!empty($row['author']))
      {
	$author = $row['author'];
	if ($author == 'guest')
	{
	  $author = l10n('guest');
	}
      }
      else
      {
	$author = $row['username'];
      }

      $tpl_comment =
        array(
          'AUTHOR' => trigger_event('render_comment_author', $author),

          'DATE' => format_date( $row['date'], true),

          'CONTENT' => trigger_event('render_comment_content',$row['content']),
        );

      if (can_manage_comment('delete', $row['author_id']))
      {
        $tpl_comment['U_DELETE'] =
	  add_url_params($url_self,
			 array(
			   'action'=>'delete_comment',
			   'comment_to_delete'=>$row['id']
			       )
			 );
      }
      if (can_manage_comment('edit', $row['author_id']))
      {
	$tpl_comment['U_EDIT'] =
	  add_url_params($url_self,
			 array(
			   'action'=>'edit_comment',
			   'comment_to_edit'=>$row['id']
			       )
			 );
	if (isset($edit_comment) and ($row['id'] == $edit_comment))
	{
	  $tpl_comment['IN_EDIT'] = true;
	  $key = get_comment_post_key($page['image_id']);
	  $tpl_comment['KEY'] = $key;
	  $tpl_comment['CONTENT'] = $row['content'];
	}
      }
      if (is_admin())
      {
	if ($row['validated'] != 'true')
	{
	  $tpl_comment['U_VALIDATE'] =
	    add_url_params($url_self,
			   array('action' => 'validate_comment',
				 'comment_to_validate' => $row['id']
				 )
			   );
	}
      }
      $template->append('comments', $tpl_comment);
    }
  }

  if (!is_a_guest()
      or (is_a_guest() and $conf['comments_forall'])
      or (empty($edit_comment)))
  {
    $key = get_comment_post_key($page['image_id']);
    $content = '';
    if ('reject'===@$comment_action)
    {
      $content = htmlspecialchars($comm['content']);
    }
    $template->assign('comment_add',
        array(
          'F_ACTION' => $url_self,
          'KEY' => $key,
          'CONTENT' => $content,
          'SHOW_AUTHOR' => !is_classic_user()
        ));
  }
}

?>