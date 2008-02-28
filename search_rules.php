<?php
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

/**
 * returns language value 'included' or 'excluded' depending on boolean
 * value. This function is useful only to make duplicate code shorter
 *
 * @param bool is_included
 * @return string
 */
function inc_exc_str($is_included)
{
  return $is_included ? l10n('included') : l10n('excluded');
}

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
check_status(ACCESS_NONE);
include_once( PHPWG_ROOT_PATH.'include/functions_search.inc.php' );

$page['body_id'] = 'thePopuphelpPage';
$title = l10n('PhpWebGallery Help');
$page['page_banner'] = '<h1>'.$title.'</h1>';
$page['meta_robots']=array('noindex'=>1, 'nofollow'=>1);
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('search_rules' => 'search_rules.tpl'));

// +-----------------------------------------------------------------------+
// |                        Textual rules creation                         |
// +-----------------------------------------------------------------------+

// Rules are stored in database, serialized in an array. This array must be
// transformed into a list of textual rules.

$search = get_search_array($_GET['search_id']);

if (isset($search['q']))
{
  $template->append( 'search_words', $search['q'] );
}
else
{
  $template->assign(
    array(
      'INTRODUCTION'
        => 'OR' == $search['mode']
        ? l10n('At least one listed rule must be satisfied.')
        : l10n('Each listed rule must be satisfied.'),
      )
    );
}

if (isset($search['fields']['allwords']))
{
  $template->append( 'search_words',
      sprintf(
        l10n('searched words : %s'),
        join(', ', $search['fields']['allwords']['words'])
        )
      );
}

if (isset($search['fields']['tags']))
{
  $template->assign('SEARCH_TAGS_MODE', $search['fields']['tags']['mode']);
  
  $query = '
SELECT name
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.implode(',', $search['fields']['tags']['words']).')
;';
  $template->assign(
      'search_tags',
      array_from_query($query, 'name')
    );
}

if (isset($search['fields']['author']))
{
  $template->append(
    'search_words',
     sprintf(
          l10n('author(s) : %s'),
          join(', ', $search['fields']['author']['words'])
        )
      );
}

if (isset($search['fields']['cat']))
{
  if ($search['fields']['cat']['sub_inc'])
  {
    // searching all the categories id of sub-categories
    $cat_ids = get_subcat_ids($search['fields']['cat']['words']);
  }
  else
  {
    $cat_ids = $search['fields']['cat']['words'];
  }

  $query = '
SELECT id, uppercats, global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.
    implode(',', $cat_ids).
    ')
;';
  $result = pwg_query($query);

  $categories = array();
  if (!empty($result))
  {
    while ($row = mysql_fetch_array($result))
    {
      array_push($categories, $row);
    }
  }
  usort($categories, 'global_rank_compare');

  foreach ($categories as $category)
  {
    $template->append(
      'search_categories',
      get_cat_display_name_cache(
          $category['uppercats'],
          null,                      // no url on category names
          false                    // no blank replacement
          )
      );
  }
}

foreach (array('date_available', 'date_creation') as $datefield)
{
  if ('date_available' == $datefield)
  {
    $lang_items = array(
      'date'   => 'became available on %s',
      'period' => 'became available between %s (%s) and %s (%s)',
      'after'  => 'became available after %s (%s)',
      'before' => 'became available before %s (%s)',
      );
  }
  elseif ('date_creation' == $datefield)
  {
    $lang_items = array(
      'date'   => 'created on %s',
      'period' => 'created between %s (%s) and %s (%s)',
      'after'  => 'created after %s (%s)',
      'before' => 'created before %s (%s)',
      );
  }

  $keys = array(
    'date'   => $datefield,
    'after'  => $datefield.'-after',
    'before' => $datefield.'-before',
    );

  if (isset($search['fields'][ $keys['date'] ]))
  {
    $template->assign(
      strtoupper($datefield),
      sprintf(
          l10n($lang_items['date']),
          format_date($search['fields'][ $keys['date'] ])
          )
      );
  }
  elseif (isset($search['fields'][ $keys['before'] ])
          and isset($search['fields'][ $keys['after'] ]))
  {
    $template->assign(
      strtoupper($datefield),
      sprintf(
          l10n($lang_items['period']),

          format_date($search['fields'][ $keys['after'] ]['date']),
          inc_exc_str($search['fields'][ $keys['after'] ]['inc']),

          format_date($search['fields'][ $keys['before'] ]['date']),
          inc_exc_str($search['fields'][ $keys['before'] ]['inc'])
          )
      );
  }
  elseif (isset($search['fields'][ $keys['before'] ]))
  {
    $template->assign(
      strtoupper($datefield),
      sprintf(
          l10n($lang_items['before']),

          format_date($search['fields'][ $keys['before'] ]['date']),
          inc_exc_str($search['fields'][ $keys['before'] ]['inc'])
          )
      );
  }
  elseif (isset($search['fields'][ $keys['after'] ]))
  {
    $template->assign(
      strtoupper($datefield),
      sprintf(
          l10n($lang_items['after']),

          format_date($search['fields'][ $keys['after'] ]['date']),
          inc_exc_str($search['fields'][ $keys['after'] ]['inc'])
          )
      );
  }
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->pparse('search_rules');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>