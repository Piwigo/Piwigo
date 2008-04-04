<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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
 * returns a "secret key" that is to be sent back when a user enters a comment
 */
function get_comment_post_key($image_id)
{
  global $conf;

  $time = time();

  return sprintf(
    '%s:%s',
    $time,
    hash_hmac(
      'md5',
      $time.':'.$image_id,
      $conf['secret_key']
      )
    );
}

//returns string action to perform on a new comment: validate, moderate, reject
function user_comment_check($action, $comment)
{
  global $conf,$user;

  if ($action=='reject')
    return $action;

  $my_action = $conf['comment_spam_reject'] ? 'reject':'moderate';

  if ($action==$my_action)
    return $action;

  // we do here only BASIC spam check (plugins can do more)
  if ( !is_a_guest() )
    return $action;

  $link_count = preg_match_all( '/https?:\/\//',
    $comment['content'], $matches);

  if ( strpos($comment['author'], 'http://')!==false )
  {
    $link_count++;
  }

  if ( $link_count>$conf['comment_spam_max_links'] )
    return $my_action;

  return $action;
}


add_event_handler('user_comment_check', 'user_comment_check',
  EVENT_HANDLER_PRIORITY_NEUTRAL, 2);

/**
 * Tries to insert a user comment in the database and returns one of :
 * validate, moderate, reject
 * @param array comm contains author, content, image_id
 * @param string key secret key sent back to the browser
 * @param array infos out array of messages
 */
function insert_user_comment( &$comm, $key, &$infos )
{
  global $conf, $user;

  $comm = array_merge( $comm,
    array(
      'ip' => $_SERVER['REMOTE_ADDR'],
      'agent' => $_SERVER['HTTP_USER_AGENT']
    )
   );

  $infos = array();
  if (!$conf['comments_validation'] or is_admin())
  {
    $comment_action='validate'; //one of validate, moderate, reject
  }
  else
  {
    $comment_action='moderate'; //one of validate, moderate, reject
  }

  // display author field if the user status is guest or generic
  if (!is_classic_user())
  {
    if ( empty($comm['author']) )
    {
      $comm['author'] = 'guest';
    }
    // if a guest try to use the name of an already existing user, he must be
    // rejected
    if ( $comm['author'] != 'guest' )
    {
      $query = '
SELECT COUNT(*) AS user_exists
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username']." = '".addslashes($comm['author'])."'";
      $row = mysql_fetch_assoc( pwg_query( $query ) );
      if ( $row['user_exists'] == 1 )
      {
        array_push($infos, l10n('comment_user_exists') );
        $comment_action='reject';
      }
    }
  }
  else
  {
    $comm['author'] = $user['username'];
  }
  if ( empty($comm['content']) )
  { // empty comment content
    $comment_action='reject';
  }

  $key = explode( ':', @$key );
  if ( count($key)!=2
        or $key[0]>time()-2 // page must have been retrieved more than 2 sec ago
        or $key[0]<time()-3600 // 60 minutes expiration
        or hash_hmac(
              'md5', $key[0].':'.$comm['image_id'], $conf['secret_key']
            ) != $key[1]
      )
  {
    $comment_action='reject';
  }

  if ($comment_action!='reject' and $conf['anti-flood_time']>0 )
  { // anti-flood system
    $reference_date = time() - $conf['anti-flood_time'];
    $query = '
SELECT id FROM '.COMMENTS_TABLE.'
  WHERE date > FROM_UNIXTIME('.$reference_date.')
    AND author = "'.addslashes($comm['author']).'"';
    if ( mysql_num_rows( pwg_query( $query ) ) > 0 )
    {
      array_push( $infos, l10n('comment_anti-flood') );
      $comment_action='reject';
    }
  }

  // perform more spam check
  $comment_action = trigger_event('user_comment_check',
      $comment_action, $comm
    );

  if ( $comment_action!='reject' )
  {
    $query = '
INSERT INTO '.COMMENTS_TABLE.'
  (author, content, date, validated, validation_date, image_id)
  VALUES (
    "'.addslashes($comm['author']).'",
    "'.addslashes($comm['content']).'",
    NOW(),
    "'.($comment_action=='validate' ? 'true':'false').'",
    '.($comment_action=='validate' ? 'NOW()':'NULL').',
    '.$comm['image_id'].'
  )
';

    pwg_query($query);

    $comm['id'] = mysql_insert_id();

    if
      (
        ($comment_action=='validate' and $conf['email_admin_on_comment'])
        or 
        ($comment_action!='validate' and $conf['email_admin_on_comment_validation'])
      )
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

      $del_url =
          get_absolute_root_url().'comments.php?delete='.$comm['id'];

      $keyargs_content = array
      (
        get_l10n_args('Author: %s', $comm['author']),
        get_l10n_args('Comment: %s', $comm['content']),
        get_l10n_args('', ''),
        get_l10n_args('Delete: %s', $del_url)
      );

      if ($comment_action!='validate')
      {
        $keyargs_content[] =
          get_l10n_args('', '');
        $keyargs_content[] =
          get_l10n_args('Validate: %s',
            get_absolute_root_url().'comments.php?validate='.$comm['id']);
      }

      pwg_mail_notification_admins
      (
        get_l10n_args('Comment by %s', $comm['author']),
        $keyargs_content
      );
    }
  }
  return $comment_action;
}

?>