<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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
check_status(ACCESS_FREE);
include_once( PHPWG_ROOT_PATH.'include/functions_search.inc.php' );

$page['body_id'] = 'thePopuphelpPage';
$title = l10n('Piwigo Help');
$page['page_banner'] = '';
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
  $template->append( 'search_words', htmlspecialchars($search['q']) );
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
  $template->append(
      'search_words',
      l10n(
        'searched words : %s',
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
      l10n(
        'author(s) : %s',
        join(', ', array_map('strip_tags', $search['fields']['author']['words']))
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
    while ($row = pwg_db_fetch_assoc($result))
    {
      $categories[] = $row;
    }
  }
  usort($categories, 'global_rank_compare');

  foreach ($categories as $category)
  {
    $template->append(
      'search_categories',
      get_cat_display_name_cache(
          $category['uppercats'],
          null                      // no url on category names
          )
      );
  }
}

foreach (array('date_available', 'date_creation') as $datefield)
{
  if ('date_available' == $datefield)
  {
    $lang_items = array(
      'date'   => l10n('posted on %s'),
      'period' => l10n('posted between %s (%s) and %s (%s)'),
      'after'  => l10n('posted after %s (%s)'),
      'before' => l10n('posted before %s (%s)'),
      );
  }
  elseif ('date_creation' == $datefield)
  {
    $lang_items = array(
      'date'   => l10n('created on %s'),
      'period' => l10n('created between %s (%s) and %s (%s)'),
      'after'  => l10n('created after %s (%s)'),
      'before' => l10n('created before %s (%s)'),
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
          $lang_items['date'],
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
          $lang_items['period'],

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
          $lang_items['before'],

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
          $lang_items['after'],

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