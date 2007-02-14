<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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

//returns string action to perform on a new comment: validate, moderate, reject
function user_comment_check($action, $comment, $picture)
{
  global $conf,$user;

  if ($action=='reject')
    return $action;

  $my_action = $conf['comment_spam_reject'] ? 'reject':'moderate';
  if ($action==$my_action)
    return $action;

  // we do here only BASIC spam check (plugins can do more)
  if ( !$user['is_the_guest'] )
    return $action;

  $link_count = preg_match_all( '/https?:\/\//',
    $comment['content'], $matches);

  if ( $link_count>$conf['comment_spam_max_links'] )
    return $my_action;

  if ( isset($comment['ip']) and $conf['comment_spam_check_ip'] )
  {
    $rev_ip = implode( '.', array_reverse( explode('.',$comment['ip']) ) );
    $lookup = $rev_ip . '.sbl-xbl.spamhaus.org.';
    $res = gethostbyname( $lookup );
    if ( $lookup != $res )
      return $my_action;
  }

  return $action;
}



add_event_handler('user_comment_check', 'user_comment_check',
  EVENT_HANDLER_PRIORITY_NEUTRAL, 3);


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
  if (!$conf['comments_validation'] or is_admin())
  {
    $comment_action='validate'; //one of validate, moderate, reject
  }
  else
  {
    $comment_action='moderate'; //one of validate, moderate, reject
  }

  $_POST['content'] = trim( stripslashes($_POST['content']) );

  if ( $user['is_the_guest'] )
  {
    $author = empty($_POST['author'])?'guest':$_POST['author'];
    // if a guest try to use the name of an already existing user, he must be
    // rejected
    if ( $author != 'guest' )
    {
      $query = 'SELECT COUNT(*) AS user_exists';
      $query.= ' FROM '.USERS_TABLE;
      $query.= ' WHERE '.$conf['user_fields']['username']." = '".$author."'";
      $query.= ';';
      $row = mysql_fetch_assoc( pwg_query( $query ) );
      if ( $row['user_exists'] == 1 )
      {
        $template->assign_block_vars(
          'information',
          array('INFORMATION'=>$lang['comment_user_exists']));
        $comment_action='reject';
      }
    }
  }
  else
  {
    $author = $user['username'];
  }

  $comm = array(
    'author' => $author,
    'content' => $_POST['content'],
    'image_id' => $page['image_id'],
    'ip' => $_SERVER['REMOTE_ADDR'],
    'agent' => $_SERVER['HTTP_USER_AGENT']
   );

  if ($comment_action!='reject' and empty($comm['content']) )
  { // empty comment content
    $comment_action='reject';
  }

  $key = explode(':', @$_POST['key']);
  if ( count($key)!=2
        or $key[0]>time()-2 // page must have been retrieved more than 2 sec ago
        or $key[0]<time()-3600 // 60 minutes expiration
        or hash_hmac('md5', $key[0], $conf['secret_key'])!=$key[1]
      )
  {
    $comment_action='reject';
  }
  
  if ($comment_action!='reject' and $conf['anti-flood_time']>0 )
  { // anti-flood system
    $reference_date = time() - $conf['anti-flood_time'];
    $query = 'SELECT id FROM '.COMMENTS_TABLE;
    $query.= ' WHERE date > FROM_UNIXTIME('.$reference_date.')';
    $query.= " AND author = '".$comm['author']."'";
    $query.= ';';
    if ( mysql_num_rows( pwg_query( $query ) ) > 0 )
    {
      $template->assign_block_vars(
        'information',
        array('INFORMATION'=>$lang['comment_anti-flood']));
      $comment_action='reject';
    }
  }

  // perform more spam check
  $comment_action = trigger_event('user_comment_check',
      $comment_action, $comm, $picture['current']
    );

  if ( $comment_action!='reject' )
  {
    list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

    $data = $comm;
    $data['date'] = $dbnow;
    $data['content'] = addslashes(
        // this htmlpsecialchars is not good here
        htmlspecialchars($comm['content'],ENT_QUOTES)
      );

    if ($comment_action=='validate')
    {
      $data['validated'] = 'true';
      $data['validation_date'] = $dbnow;
    }
    else
    {
      $data['validated'] = 'false';
    }

    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    $fields = array('author', 'date', 'image_id', 'content', 'validated',
                    'validation_date');
    mass_inserts(COMMENTS_TABLE, $fields, array($data));
    $comm['id'] = mysql_insert_id();

    // information message
    $message = $lang['comment_added'];
    if ($comment_action!='validate')
    {
      $message.= '<br />'.$lang['comment_to_validate'];
    }
    $template->assign_block_vars('information',
                                 array('INFORMATION'=>$message));
    if ( ($comment_action=='validate' and $conf['email_admin_on_comment'])
      or $conf['email_admin_on_comment_validation'] )
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

      $del_url = get_absolute_root_url().'comments.php?delete='.$comm['id'];

      $content =
        'Author: '.$comm['author']."\n"
        .'Comment: '.$comm['content']."\n"
        .'IP: '.$comm['ip']."\n"
        .'Browser: '.$comm['agent']."\n\n"
        .'Delete: '.$del_url."\n";

      if ($comment_action!='validate')
      {
        $content .=
          'Validate: '.get_absolute_root_url()
          .'comments.php?validate='.$comm['id'];
      }

      pwg_mail
      (
        format_email('administrators', get_webmaster_mail_address()),
        array
        (
          'subject' => 'PWG comment by '.$comm['author'], 
          'content' => $content,
          'Bcc' => get_administrators_email()
        )
      );
    }
  }
  else
  {
    set_status_header(403);
    $template->assign_block_vars('information',
          array('INFORMATION'=>l10n('comment_not_added') )
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
    $key = time();
    $key .= ':'.hash_hmac('md5', $key, $conf['secret_key']);
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