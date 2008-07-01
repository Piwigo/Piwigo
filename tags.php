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

$page['display_mode'] = $conf['tags_default_display_mode'];
if (isset($_GET['display_mode']))
{
  if (in_array($_GET['display_mode'], array('cloud', 'letters')))
  {
    $page['display_mode'] = $_GET['display_mode'];
  }
}

$template->assign(
  array(
    'U_CLOUD' => get_root_url().'tags.php?display_mode=cloud',
    'U_LETTERS' => get_root_url().'tags.php?display_mode=letters',
    'display_mode' => $page['display_mode'],
    )
  );

// find all tags available for the current user
$tags = get_available_tags();

// +-----------------------------------------------------------------------+
// |                       letter groups construction                      |
// +-----------------------------------------------------------------------+

if ($page['display_mode'] == 'letters') {
  // we want tags diplayed in alphabetic order
  usort($tags, 'tag_alpha_compare');

  $current_letter = null;
  $nb_tags = count($tags);
  $current_column = 1;
  $current_tag_idx = 0;

  $letter = array(
    'tags' => array()
    );

  foreach ($tags as $tag)
  {
    $tag_letter = strtoupper(substr($tag['url_name'], 0, 1));

    if ($current_tag_idx==0) {
      $current_letter = $tag_letter;
      $letter['TITLE'] = $tag_letter;
    }

    //lettre precedente differente de la lettre suivante
    if ($tag_letter !== $current_letter)
    {
      if ($current_column<$conf['tag_letters_column_number']
          and $current_tag_idx > $current_column*$nb_tags/$conf['tag_letters_column_number'] )
      {
        $letter['CHANGE_COLUMN'] = true;
        $current_column++;
      }

      $letter['TITLE'] = $current_letter;

      $template->append(
        'letters',
        $letter
        );

      $current_letter = $tag_letter;
      $letter = array(
        'tags' => array()
        );
    }

    array_push(
      $letter['tags'],
      array_merge(
        $tag,
        array(
          'URL' => make_index_url(
            array(
              'tags' => array($tag),
              )
            ),
          )
        )
      );

    $current_tag_idx++;
  }

  // flush last letter
  if (count($letter['tags']) > 0)
  {
    $letter['CHANGE_COLUMN'] = false;
    $letter['TITLE'] = $current_letter;
    $template->append(
      'letters',
      $letter
      );
  }
}

// +-----------------------------------------------------------------------+
// |                        tag cloud construction                         |
// +-----------------------------------------------------------------------+

// we want only the first most represented tags, so we sort them by counter
// and take the first tags
usort($tags, 'counter_compare');
$tags = array_slice($tags, 0, $conf['full_tag_cloud_items_number']);

// depending on its counter and the other tags counter, each tag has a level
$tags = add_level_to_tags($tags);

// we want tags diplayed in alphabetic order
usort($tags, 'tag_alpha_compare');

// display sorted tags
foreach ($tags as $tag)
{
  $template->append(
    'tags',
    array_merge(
      $tag,
      array(
        'URL' => make_index_url(
          array(
            'tags' => array($tag),
            )
          ),
        )
      )
    );
}

include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->pparse('tags');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>