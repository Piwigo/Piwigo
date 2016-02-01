<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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
 * @package functions\comment
 */


add_event_handler('user_comment_check', 'user_comment_check');

/**
 * Does basic check on comment and returns action to perform.
 * This method is called by a trigger_change()
 *
 * @param string $action before check
 * @param array $comment
 * @return string validate, moderate, reject
 */
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
  {
    $_POST['cr'][] = 'links';
    return $my_action;
  }
  return $action;
}

/**
 * Tries to insert a user comment and returns action to perform.
 *
 * @param array &$comm
 * @param string $key secret key sent back to the browser
 * @param array &$infos output array of error messages
 * @return string validate, moderate, reject
 */
function insert_user_comment(&$comm, $key, &$infos)
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
      if ($conf['comments_author_mandatory'])
      {
        $infos[] = l10n('Username is mandatory');
        $comment_action='reject';
      }
      $comm['author'] = 'guest';
    }
    $comm['author_id'] = $conf['guest_id'];
    // if a guest try to use the name of an already existing user, he must be
    // rejected
    if ( $comm['author'] != 'guest' )
    {
      $query = '
SELECT COUNT(*) AS user_exists
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username']." = '".addslashes($comm['author'])."'";
      $row = pwg_db_fetch_assoc( pwg_query( $query ) );
      if ( $row['user_exists'] == 1 )
      {
        $infos[] = l10n('This login is already used by another user');
        $comment_action='reject';
      }
    }
  }
  else
  {
    $comm['author'] = addslashes($user['username']);
    $comm['author_id'] = $user['id'];
  }

  if ( empty($comm['content']) )
  { // empty comment content
    $comment_action='reject';
  }

  if ( !verify_ephemeral_key(@$key, $comm['image_id']) )
  {
    $comment_action='reject';
    $_POST['cr'][] = 'key'; // rvelices: I use this outside to see how spam robots work
  }

  // website
  if (!empty($comm['website_url']))
  {
    if (!$conf['comments_enable_website'])
    { // honeypot: if the field is disabled, it should be empty !
      $comment_action='reject';
      $_POST['cr'][] = 'website_url';
    }
    else
    {
      $comm['website_url'] = strip_tags($comm['website_url']);
      if (!preg_match('/^https?/i', $comm['website_url']))
      {
        $comm['website_url'] = 'http://'.$comm['website_url'];
      }
      if (!url_check_format($comm['website_url']))
      {
        $infos[] = l10n('Your website URL is invalid');
        $comment_action='reject';
      }
    }
  }

  // email
  if (empty($comm['email']))
  {
    if (!empty($user['email']))
    {
      $comm['email'] = $user['email'];
    }
    elseif ($conf['comments_email_mandatory'])
    {
      $infos[] = l10n('Email address is missing. Please specify an email address.');
      $comment_action='reject';
    }
  }
  elseif (!email_check_format($comm['email']))
  {
    $infos[] = l10n('mail address must be like xxx@yyy.eee (example : jack@altern.org)');
    $comment_action='reject';
  }

  // anonymous id = ip address
  $ip_components = explode('.', $comm['ip']);
  if (count($ip_components) > 3)
  {
    array_pop($ip_components);
  }
  $anonymous_id = implode('.', $ip_components);

  if ($comment_action!='reject' and $conf['anti-flood_time']>0 and !is_admin())
  { // anti-flood system
    $reference_date = pwg_db_get_flood_period_expression($conf['anti-flood_time']);

    $query = '
SELECT count(1) FROM '.COMMENTS_TABLE.'
  WHERE date > '.$reference_date.'
    AND author_id = '.$comm['author_id'];
    if (!is_classic_user())
    {
      $query.= '
      AND anonymous_id LIKE "'.$anonymous_id.'.%"';
    }
    $query.= '
;';

    list($counter) = pwg_db_fetch_row(pwg_query($query));
    if ( $counter > 0 )
    {
      $infos[] = l10n('Anti-flood system : please wait for a moment before trying to post another comment');
      $comment_action='reject';
      $_POST['cr'][] = 'flood_time';
    }
  }

  // perform more spam check
  $comment_action = trigger_change('user_comment_check',
      $comment_action, $comm
    );

  if ( $comment_action!='reject' )
  {
    $query = '
INSERT INTO '.COMMENTS_TABLE.'
  (author, author_id, anonymous_id, content, date, validated, validation_date, image_id, website_url, email)
  VALUES (
    \''.$comm['author'].'\',
    '.$comm['author_id'].',
    \''.$comm['ip'].'\',
    \''.$comm['content'].'\',
    NOW(),
    \''.($comment_action=='validate' ? 'true':'false').'\',
    '.($comment_action=='validate' ? 'NOW()':'NULL').',
    '.$comm['image_id'].',
    '.(!empty($comm['website_url']) ? '\''.$comm['website_url'].'\'' : 'NULL').',
    '.(!empty($comm['email']) ? '\''.$comm['email'].'\'' : 'NULL').'
  )
';
    pwg_query($query);
    $comm['id'] = pwg_db_insert_id(COMMENTS_TABLE);

    invalidate_user_cache_nb_comments();

    if ( ($conf['email_admin_on_comment'] && 'validate' == $comment_action)
        or ($conf['email_admin_on_comment_validation'] and 'moderate' == $comment_action))
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

      $comment_url = get_absolute_root_url().'comments.php?comment_id='.$comm['id'];

      $keyargs_content = array(
        get_l10n_args('Author: %s', stripslashes($comm['author']) ),
        get_l10n_args('Email: %s', stripslashes($comm['email']) ),
        get_l10n_args('Comment: %s', stripslashes($comm['content']) ),
        get_l10n_args(''),
        get_l10n_args('Manage this user comment: %s', $comment_url),
      );

      if ('moderate' == $comment_action)
      {
        $keyargs_content[] = get_l10n_args('(!) This comment requires validation');
      }

      pwg_mail_notification_admins(
        get_l10n_args('Comment by %s', stripslashes($comm['author']) ),
        $keyargs_content
      );
    }
  }

  return $comment_action;
}

/**
 * Tries to delete a (or more) user comment.
 *    only admin can delete all comments
 *    other users can delete their own comments
 *
 * @param int|int[] $comment_id
 * @return bool false if nothing deleted
 */
function delete_user_comment($comment_id)
{
  $user_where_clause = '';
  if (!is_admin())
  {
    $user_where_clause = '   AND author_id = \''.$GLOBALS['user']['id'].'\'';
  }

  if (is_array($comment_id))
    $where_clause = 'id IN('.implode(',', $comment_id).')';
  else
    $where_clause = 'id = '.$comment_id;

  $query = '
DELETE FROM '.COMMENTS_TABLE.'
  WHERE '.$where_clause.
$user_where_clause.'
;';

  if ( pwg_db_changes(pwg_query($query)) )
  {
    invalidate_user_cache_nb_comments();

    email_admin('delete',
                array('author' => $GLOBALS['user']['username'],
                      'comment_id' => $comment_id
                  ));
    trigger_notify('user_comment_deletion', $comment_id);

    return true;
  }

  return false;
}

/**
 * Tries to update a user comment
 *    only admin can update all comments
 *    users can edit their own comments if admin allow them
 *
 * @param array $comment
 * @param string $post_key secret key sent back to the browser
 * @return string validate, moderate, reject
 */

function update_user_comment($comment, $post_key)
{
  global $conf, $page;

  $comment_action = 'validate';

  if ( !verify_ephemeral_key($post_key, $comment['image_id']) )
  {
    $comment_action='reject';
  }
  elseif (!$conf['comments_validation'] or is_admin()) // should the updated comment must be validated
  {
    $comment_action='validate'; //one of validate, moderate, reject
  }
  else
  {
    $comment_action='moderate'; //one of validate, moderate, reject
  }

  // perform more spam check
  $comment_action =
    trigger_change('user_comment_check',
		  $comment_action,
		  array_merge($comment,
			      array('author' => $GLOBALS['user']['username'])
			      )
		  );

  // website
  if (!empty($comment['website_url']))
  {
    $comm['website_url'] = strip_tags($comm['website_url']);
    if (!preg_match('/^https?/i', $comment['website_url']))
    {
      $comment['website_url'] = 'http://'.$comment['website_url'];
    }
    if (!url_check_format($comment['website_url']))
    {
      $page['errors'][] = l10n('Your website URL is invalid');
      $comment_action='reject';
    }
  }

  if ( $comment_action!='reject' )
  {
    $user_where_clause = '';
    if (!is_admin())
    {
      $user_where_clause = '   AND author_id = \''.
	$GLOBALS['user']['id'].'\'';
    }

    $query = '
UPDATE '.COMMENTS_TABLE.'
  SET content = \''.$comment['content'].'\',
      website_url = '.(!empty($comment['website_url']) ? '\''.$comment['website_url'].'\'' : 'NULL').',
      validated = \''.($comment_action=='validate' ? 'true':'false').'\',
      validation_date = '.($comment_action=='validate' ? 'NOW()':'NULL').'
  WHERE id = '.$comment['comment_id'].
$user_where_clause.'
;';
    $result = pwg_query($query);

    // mail admin and ask to validate the comment
    if ($result and $conf['email_admin_on_comment_validation'] and 'moderate' == $comment_action)
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

      $comment_url = get_absolute_root_url().'comments.php?comment_id='.$comment['comment_id'];

      $keyargs_content = array(
        get_l10n_args('Author: %s', stripslashes($GLOBALS['user']['username']) ),
        get_l10n_args('Comment: %s', stripslashes($comment['content']) ),
        get_l10n_args(''),
        get_l10n_args('Manage this user comment: %s', $comment_url),
        get_l10n_args('(!) This comment requires validation'),
      );

      pwg_mail_notification_admins(
        get_l10n_args('Comment by %s', stripslashes($GLOBALS['user']['username']) ),
        $keyargs_content
      );
    }
    // just mail admin
    elseif ($result)
    {
      email_admin('edit', array('author' => $GLOBALS['user']['username'],
				'content' => stripslashes($comment['content'])) );
    }
  }

  return $comment_action;
}

/**
 * Notifies admins about updated or deleted comment.
 * Only used when no validation is needed, otherwise pwg_mail_notification_admins() is used.
 *
 * @param string $action edit, delete
 * @param array $comment
 */
function email_admin($action, $comment)
{
  global $conf;

  if (!in_array($action, array('edit', 'delete'))
      or (($action=='edit') and !$conf['email_admin_on_comment_edition'])
      or (($action=='delete') and !$conf['email_admin_on_comment_deletion']))
  {
    return;
  }

  include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

  $keyargs_content = array(
    get_l10n_args('Author: %s', $comment['author']),
    );

  if ($action=='delete')
  {
    $keyargs_content[] = get_l10n_args('This author removed the comment with id %d', $comment['comment_id']);
  }
  else
  {
    $keyargs_content[] = get_l10n_args('This author modified following comment:');
    $keyargs_content[] = get_l10n_args('Comment: %s', $comment['content']);
  }

  pwg_mail_notification_admins(
    get_l10n_args('Comment by %s', $comment['author']),
    $keyargs_content
    );
}

/**
 * Returns the author id of a comment
 *
 * @param int $comment_id
 * @param bool $die_on_error
 * @return int
 */
function get_comment_author_id($comment_id, $die_on_error=true)
{
  $query = '
SELECT
    author_id
  FROM '.COMMENTS_TABLE.'
  WHERE id = '.$comment_id.'
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) == 0)
  {
    if ($die_on_error)
    {
      fatal_error('Unknown comment identifier');
    }
    else
    {
      return false;
    }
  }

  list($author_id) = pwg_db_fetch_row($result);

  return $author_id;
}

/**
 * Tries to validate a user comment.
 *
 * @param int|int[] $comment_id
 */
function validate_user_comment($comment_id)
{
  if (is_array($comment_id))
    $where_clause = 'id IN('.implode(',', $comment_id).')';
  else
    $where_clause = 'id = '.$comment_id;

  $query = '
UPDATE '.COMMENTS_TABLE.'
  SET validated = \'true\'
    , validation_date = NOW()
  WHERE '.$where_clause.'
;';
  pwg_query($query);

  invalidate_user_cache_nb_comments();
  trigger_notify('user_comment_validation', $comment_id);
}

/**
 * Clears cache of nb comments for all users
 */
function invalidate_user_cache_nb_comments()
{
  global $user;

  unset($user['nb_available_comments']);

  $query = '
UPDATE '.USER_CACHE_TABLE.'
  SET nb_available_comments = NULL
;';
  pwg_query($query);
}

?>