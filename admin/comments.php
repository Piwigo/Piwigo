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
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_waiting.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

if (isset($_POST))
{
  $to_validate = array();
  $to_reject = array();

  if (isset($_POST['submit']) and !is_adviser())
  {
    foreach (explode(',', $_POST['list']) as $comment_id)
    {
      if (isset($_POST['action-'.$comment_id]))
      {
        switch ($_POST['action-'.$comment_id])
        {
          case 'reject' :
          {
            array_push($to_reject, $comment_id);
            break;
          }
          case 'validate' :
          {
            array_push($to_validate, $comment_id);
            break;
          }
        }
      }
    }
  }
  else if (isset($_POST['validate-all']) and !empty($_POST['list']) and !is_adviser())
  {
    $to_validate = explode(',', $_POST['list']);
  }
  else if (isset($_POST['reject-all']) and !empty($_POST['list']) and !is_adviser())
  {
    $to_reject = explode(',', $_POST['list']);
  }

  if (count($to_validate) > 0)
  {
    $query = '
UPDATE '.COMMENTS_TABLE.'
  SET validated = \'true\'
    , validation_date = NOW()
  WHERE id IN ('.implode(',', $to_validate).')
;';
    pwg_query($query);

    array_push(
      $page['infos'],
      l10n_dec(
        '%d user comment validated', '%d user comments validated',
        count($to_validate)
        )
      );
  }

  if (count($to_reject) > 0)
  {
    $query = '
DELETE
  FROM '.COMMENTS_TABLE.'
  WHERE id IN ('.implode(',', $to_reject).')
;';
    pwg_query($query);

    array_push(
      $page['infos'],
      l10n_dec(
        '%d user comment rejected', '%d user comments rejected',
        count($to_reject)
        )
      );
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('comments'=>'admin/comments.tpl'));

// TabSheet initialization
waiting_tabsheet();

$template->assign(
  array(
    'F_ACTION' => get_root_url().'admin.php?page=comments'
    )
  );

// +-----------------------------------------------------------------------+
// |                           comments display                            |
// +-----------------------------------------------------------------------+

$list = array();

$query = '
SELECT c.id, c.image_id, c.date, c.author, c.content, i.path, i.tn_ext
  FROM '.COMMENTS_TABLE.' AS c
    INNER JOIN '.IMAGES_TABLE.' AS i
      ON i.id = c.image_id
  WHERE validated = \'false\'
  ORDER BY c.date DESC  
;';
$result = pwg_query($query);
while ($row = mysql_fetch_assoc($result))
{
  $thumb = get_thumbnail_url(
      array(
        'id'=>$row['image_id'],
        'path'=>$row['path'],
        'tn_ext'=>@$row['tn_ext']
        )
     );
  $template->append(
    'comments',
    array(
      'U_PICTURE' =>
          PHPWG_ROOT_PATH.'admin.php?page=picture_modify'.
          '&amp;image_id='.$row['image_id'],
      'ID' => $row['id'],
      'TN_SRC' => $thumb,
      'AUTHOR' => trigger_event('render_comment_author', $row['author']),
      'DATE' => format_date($row['date'],'mysql_datetime',true),
      'CONTENT' => trigger_event('render_comment_content',$row['content'])
      )
    );

  array_push($list, $row['id']);
}

$template->assign('LIST', implode(',', $list) );

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'comments');

?>
