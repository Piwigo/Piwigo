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

// +-----------------------------------------------------------------------+
// |                             functions                                 |
// +-----------------------------------------------------------------------+

function counter_compare($a, $b)
{
  if ($a['counter'] == $b['counter'])
  {
    return id_compare($a, $b);
  }

  return ($a['counter'] < $b['counter']) ? +1 : -1;
}

function id_compare($a, $b)
{
  return ($a['id'] < $b['id']) ? -1 : 1;
}

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

check_status(ACCESS_GUEST);

// +-----------------------------------------------------------------------+
// |                       page header and options                         |
// +-----------------------------------------------------------------------+

$title= l10n('Tags');
$page['body_id'] = 'theTagsPage';

$template->set_filenames(array('tags'=>'tags.tpl'));

// +-----------------------------------------------------------------------+
// |                        tag cloud construction                         |
// +-----------------------------------------------------------------------+

// find all tags available for the current user
$tags = get_available_tags();

// we want only the first most represented tags, so we sort them by counter
// and take the first tags
usort($tags, 'counter_compare');
$tags = array_slice($tags, 0, $conf['full_tag_cloud_items_number']);

// depending on its counter and the other tags counter, each tag has a level
$tags = add_level_to_tags($tags);

// we want tags diplayed in alphabetic order
usort($tags, 'name_compare');

// display sorted tags
foreach ($tags as $tag)
{
  $template->append(
    'tags',
    array(
      'URL' => make_index_url(
        array(
          'tags' => array($tag),
          )
        ),

      'NAME' => $tag['name'],
      'TITLE' => $tag['counter'],
      'CLASS' => 'tagLevel'.$tag['level'],
      )
    );
}

include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->pparse('tags');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>