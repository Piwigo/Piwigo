<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
check_status(ACCESS_ADMINISTRATOR);

if (!empty($_POST))
{
  check_pwg_token();
  check_input_parameter('tags', $_POST, true, PATTERN_ID);
  check_input_parameter('selectAction', $_POST, false, '/^[a-zA-Z0-9_-]+$/');
  check_input_parameter('edit_list', $_POST, false, '/^\d+(,\d+)*$/');
  check_input_parameter('merge_list', $_POST, false, '/^\d+(,\d+)*$/');
  check_input_parameter('destination_tag', $_POST, false, PATTERN_ID);
}

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$my_base_url = get_root_url().'admin.php?page=';

$tabsheet = new tabsheet();
$tabsheet->set_id('tags');
$tabsheet->select('');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                                edit tags                              |
// +-----------------------------------------------------------------------+

if (isset($_POST['edit_submit']))
{
  $query = '
SELECT name
  FROM '.TAGS_TABLE.'
;';
  $existing_names = array_from_query($query, 'name');


  $current_name_of = array();
  $query = '
SELECT id, name
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.$_POST['edit_list'].')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $current_name_of[ $row['id'] ] = $row['name'];
  }

  $updates = array();
  // we must not rename tag with an already existing name
  foreach (explode(',', $_POST['edit_list']) as $tag_id)
  {
    $tag_name = stripslashes($_POST['tag_name-'.$tag_id]);

    if ($tag_name != $current_name_of[$tag_id])
    {
      if (in_array($tag_name, $existing_names))
      {
        $page['errors'][] = l10n('Tag "%s" already exists', $tag_name);
      }
      else if (!empty($tag_name))
      {
        $updates[] = array(
          'id' => $tag_id,
          'name' => addslashes($tag_name),
          'url_name' => trigger_change('render_tag_url', $tag_name),
          );
      }
    }
  }
  mass_updates(
    TAGS_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('name', 'url_name'),
      ),
    $updates
    );

  pwg_activity('tag', explode(',', $_POST['edit_list']), 'edit');
}
// +-----------------------------------------------------------------------+
// |                            dulicate tags                              |
// +-----------------------------------------------------------------------+

if (isset($_POST['duplic_submit']))
{
  $query = '
SELECT name
  FROM '.TAGS_TABLE.'
;';
  $existing_names = array_from_query($query, 'name');


  $current_name_of = array();
  $query = '
SELECT id, name
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.$_POST['edit_list'].')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $current_name_of[ $row['id'] ] = $row['name'];
  }

  $updates = array();
  // we must not rename tag with an already existing name
  foreach (explode(',', $_POST['edit_list']) as $tag_id)
  {
    $tag_name = stripslashes($_POST['tag_name-'.$tag_id]);

    if ($tag_name != $current_name_of[$tag_id])
    {
      if (in_array($tag_name, $existing_names))
      {
        $page['errors'][] = l10n('Tag "%s" already exists', $tag_name);
      }
      else if (!empty($tag_name))
      {
        single_insert(
          TAGS_TABLE,
          array(
            'name' => $tag_name,
            'url_name' => trigger_change('render_tag_url', $tag_name),
            )
          );
        $destination_tag_id = pwg_db_insert_id(TAGS_TABLE);

        pwg_activity('tag', $destination_tag_id, 'add', array('action'=>'duplicate', 'source_tag'=>$tag_id));

        $query = '
        SELECT
            image_id
          FROM '.IMAGE_TAG_TABLE.'
          WHERE tag_id = '.$tag_id.'
        ;';
        $destination_tag_image_ids = array_from_query($query, 'image_id');

        $inserts = array();
        foreach ($destination_tag_image_ids as $image_id)
        {
          $inserts[] = array(
            'tag_id' => $destination_tag_id,
            'image_id' => $image_id
            );
        }

        if (count($inserts) > 0)
        {
          mass_inserts(
            IMAGE_TAG_TABLE,
            array_keys($inserts[0]),
            $inserts
            );
        }
        
        $page['infos'][] = l10n(
          'Tag "%s" is now a duplicate of "%s"',
          stripslashes($tag_name),
          $current_name_of[$tag_id]
          );
      }
    }
  }

  mass_updates(
    TAGS_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('name', 'url_name'),
      ),
    $updates
    );
}

// +-----------------------------------------------------------------------+
// |                               merge tags                              |
// +-----------------------------------------------------------------------+

if (isset($_POST['merge_submit']))
{
  if (!isset($_POST['destination_tag']))
  {
    $page['errors'][] = l10n('No destination tag selected');
  }
  else
  {
    $destination_tag_id = $_POST['destination_tag'];
    $tag_ids = explode(',', $_POST['merge_list']);

    if (is_array($tag_ids) and count($tag_ids) > 1)
    {
      $name_of_tag = array();
      $query = '
SELECT
    id,
    name
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.implode(',', $tag_ids).')
;';
      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      {
        $name_of_tag[ $row['id'] ] = trigger_change('render_tag_name', $row['name'], $row);
      }

      $tag_ids_to_delete = array_diff(
        $tag_ids,
        array($destination_tag_id)
        );

      $query = '
SELECT
    DISTINCT(image_id)
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',', $tag_ids_to_delete).')
;';
      $image_ids = array_from_query($query, 'image_id');

      delete_tags($tag_ids_to_delete);

      $query = '
SELECT
    image_id
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id = '.$destination_tag_id.'
;';
      $destination_tag_image_ids = array_from_query($query, 'image_id');

      $image_ids_to_link = array_diff(
        $image_ids,
        $destination_tag_image_ids
        );

      $inserts = array();
      foreach ($image_ids_to_link as $image_id)
      {
        $inserts[] = array(
          'tag_id' => $destination_tag_id,
          'image_id' => $image_id
          );
      }

      if (count($inserts) > 0)
      {
        mass_inserts(
          IMAGE_TAG_TABLE,
          array_keys($inserts[0]),
          $inserts
          );
      }

      $tags_deleted = array();
      foreach ($tag_ids_to_delete as $tag_id)
      {
        $tags_deleted[] = $name_of_tag[$tag_id];
      }

      $page['infos'][] = l10n(
        'Tags <em>%s</em> merged into tag <em>%s</em>',
        implode(', ', $tags_deleted),
        $name_of_tag[$destination_tag_id]
        );
    }
  }
}


// +-----------------------------------------------------------------------+
// |                               delete tags                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['delete']) and isset($_POST['tags']))
{
  if (!isset($_POST['confirm_deletion']))
  {
    $page['errors'][] = l10n('You need to confirm deletion');
  }
  else
  {
    $query = '
SELECT name
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.implode(',', $_POST['tags']).')
;';
    $tag_names = array_from_query($query, 'name');

    delete_tags($_POST['tags']);

    $page['infos'][] = l10n_dec(
      'The following tag was deleted', 'The %d following tags were deleted',
      count($tag_names)
      )
      .' : '.implode(', ', $tag_names);
  }
}

// +-----------------------------------------------------------------------+
// |                           delete orphan tags                          |
// +-----------------------------------------------------------------------+

if (isset($_GET['action']) and 'delete_orphans' == $_GET['action'])
{
  check_pwg_token();

  delete_orphan_tags();
  $_SESSION['page_infos'] = array(l10n('Orphan tags deleted'));
  redirect(get_root_url().'admin.php?page=tags');
}

// +-----------------------------------------------------------------------+
// |                               add a tag                               |
// +-----------------------------------------------------------------------+

if (isset($_POST['add']) and !empty($_POST['add_tag']))
{
  $ret = create_tag($_POST['add_tag']);
  
  if (isset($ret['error']))
  {
    $page['errors'][] = $ret['error'];
  }
  else
  {
    $page['infos'][] = $ret['info'];
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('tags' => 'tags.tpl'));

$template->assign(
  array(
    'F_ACTION' => PHPWG_ROOT_PATH.'admin.php?page=tags',
    'PWG_TOKEN' => get_pwg_token(),
    )
  );

// +-----------------------------------------------------------------------+
// |                              orphan tags                              |
// +-----------------------------------------------------------------------+

$orphan_tags = get_orphan_tags();

$orphan_tag_names = array();
foreach ($orphan_tags as $tag)
{
  $orphan_tag_names[] = trigger_change('render_tag_name', $tag['name'], $tag);
}

if (count($orphan_tag_names) > 0)
{
  $page['warnings'][] = sprintf(
    l10n('You have %d orphan tags: %s.').' <a href="%s" class="icon-trash">'.l10n('Delete orphan tags').'</a>',
    count($orphan_tag_names),
    implode(', ', $orphan_tag_names),
    get_root_url().'admin.php?page=tags&amp;action=delete_orphans&amp;pwg_token='.get_pwg_token()
    );
}

// +-----------------------------------------------------------------------+
// |                             form creation                             |
// +-----------------------------------------------------------------------+


// tag counters
$query = '
SELECT tag_id, COUNT(image_id) AS counter
  FROM '.IMAGE_TAG_TABLE.'
  GROUP BY tag_id';
$tag_counters = simple_hash_from_query($query, 'tag_id', 'counter');

// all tags
$query = '
SELECT *
  FROM '.TAGS_TABLE.'
;';
$result = pwg_query($query);
$all_tags = array();
while ($tag = pwg_db_fetch_assoc($result))
{
  $raw_name = $tag['name'];
  $tag['name'] = trigger_change('render_tag_name', $raw_name, $tag);
  $tag['counter'] = intval(@$tag_counters[ $tag['id'] ]);
  $tag['U_VIEW'] = make_index_url(array('tags'=>array($tag)));
  $tag['U_EDIT'] = 'admin.php?page=batch_manager&amp;filter=tag-'.$tag['id'];

  $alt_names = trigger_change('get_tag_alt_names', array(), $raw_name);
  $alt_names = array_diff( array_unique($alt_names), array($tag['name']) );
  if (count($alt_names))
  {
    $tag['alt_names'] = implode(', ', $alt_names);
  }
  $all_tags[] = $tag;
}
usort($all_tags, 'tag_alpha_compare');



$template->assign(
  array(
    'all_tags' => $all_tags,
    )
  );

if ((isset($_POST['edit']) or isset($_POST['duplicate']) or isset($_POST['merge'])) and isset($_POST['tags']))
{
  $list_name = 'EDIT_TAGS_LIST';
  if (isset($_POST['duplicate']))
  {
    $list_name = 'DUPLIC_TAGS_LIST';
  }
  elseif (isset($_POST['merge']))
  {
    $list_name = 'MERGE_TAGS_LIST';
  }

  $template->assign($list_name, implode(',', $_POST['tags']));

  $query = '
SELECT id, name
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.implode(',', $_POST['tags']).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $template->append(
      'tags',
      array(
        'ID' => $row['id'],
        'NAME' => $row['name'],
        )
      );
  }
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'tags');

?>
