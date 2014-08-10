<?php
defined('ADMINTOOLS_PATH') or die('Hacking attempt!');

/**
 * Add main toolbar to current page
 * @trigger loc_after_page_header
 */
function admintools_add_public_controller()
{
  global $MultiView, $conf, $template, $page, $user, $picture;

  if (script_basename() == 'picture' and empty($picture['current']))
  {
    return;
  }

  $url_root = get_root_url();
  $tpl_vars = array();

  if ($MultiView->is_admin())
  { // full options for admin
    $tpl_vars['U_SITE_ADMIN'] =     $url_root . 'admin.php?page=';
    $tpl_vars['MULTIVIEW'] =        $MultiView->get_data();
    $tpl_vars['USER'] =             $MultiView->get_user();
    $tpl_vars['CURRENT_USERNAME'] = $user['id']==$conf['guest_id'] ? l10n('guest') : $user['username'];
    $tpl_vars['DELETE_CACHE'] =     isset($conf['multiview_invalidate_cache']);

    if (($admin_lang = $MultiView->get_user_language()) !== false)
    {
      include_once(PHPWG_ROOT_PATH . 'include/functions_mail.inc.php');
      switch_lang_to($admin_lang);
    }
  }
  else if ($conf['AdminTools']['public_quick_edit'] and
      script_basename() == 'picture' and $picture['current']['added_by'] == $user['id']
    )
  { // only "edit" button for photo owner
  }
  else
  {
    return;
  }

  $tpl_vars['POSITION'] = $conf['AdminTools']['closed_position'];
  $tpl_vars['DEFAULT_OPEN'] = $conf['AdminTools']['default_open'];
  $tpl_vars['U_SELF'] = $MultiView->get_clean_url(true);

  // photo page
  if (script_basename() == 'picture')
  {
    $url_self = duplicate_picture_url();
    $tpl_vars['IS_PICTURE'] = true;

    // admin can add to caddie and set representattive
    if ($MultiView->is_admin())
    {
      $template->clear_assign(array(
        'U_SET_AS_REPRESENTATIVE',
        'U_PHOTO_ADMIN',
        'U_CADDIE',
        ));

      $template->set_prefilter('picture', 'admintools_remove_privacy');

      $tpl_vars['U_CADDIE'] = add_url_params(
        $url_self,
        array('action'=>'add_to_caddie')
        );

      $query = '
SELECT element_id FROM ' . CADDIE_TABLE . '
  WHERE element_id = ' . $page['image_id'] .'
;';
      $tpl_vars['IS_IN_CADDIE'] = pwg_db_num_rows(pwg_query($query)) > 0;

      if (isset($page['category']))
      {
        $tpl_vars['CATEGORY_ID'] = $page['category']['id'];

        $tpl_vars['U_SET_REPRESENTATIVE'] = add_url_params(
          $url_self,
          array('action'=>'set_as_representative')
          );

        $tpl_vars['IS_REPRESENTATIVE'] = $page['category']['representative_picture_id'] == $page['image_id'];
      }

      $tpl_vars['U_ADMIN_EDIT'] = $url_root . 'admin.php?page=photo-' . $page['image_id']
        .(isset($page['category']) ? '&amp;cat_id=' . $page['category']['id'] : '');
    }

    $tpl_vars['U_DELETE'] = add_url_params(
      $url_self, array(
        'delete'=>'',
        'pwg_token'=>get_pwg_token()
        )
      );

    // gets tags (full available list is loaded in ajax)
    include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');

    $query = '
SELECT id, name
  FROM '.IMAGE_TAG_TABLE.' AS it
    JOIN '.TAGS_TABLE.' AS t ON t.id = it.tag_id
  WHERE image_id = '.$page['image_id'].'
;';
    $tag_selection = get_taglist($query);

    $tpl_vars['QUICK_EDIT'] = array(
      'img' =>                $picture['current']['derivatives']['square']->get_url(),
      'name' =>               $picture['current']['name'],
      'comment' =>            $picture['current']['comment'],
      'author' =>             $picture['current']['author'],
      'level' =>              $picture['current']['level'],
      'date_creation' =>      substr($picture['current']['date_creation'], 0, 10),
      'date_creation_time' => substr($picture['current']['date_creation'], 11, 5),
      'tag_selection' =>      $tag_selection,
      );
  }
  // album page (admin only)
  else if ($MultiView->is_admin() and @$page['section'] == 'categories' and isset($page['category']))
  {
    $url_self = duplicate_index_url();

    $tpl_vars['IS_CATEGORY'] = true;
    $tpl_vars['CATEGORY_ID'] = $page['category']['id'];

    $template->clear_assign(array(
      'U_EDIT',
      'U_CADDIE',
      ));

    $tpl_vars['U_ADMIN_EDIT'] = $url_root . 'admin.php?page=album-' . $page['category']['id'];

    if (!empty($page['items']))
    {
      $tpl_vars['U_CADDIE'] = add_url_params(
        $url_self,
        array('caddie'=>1)
        );
    }

    $tpl_vars['QUICK_EDIT'] = array(
      'img' =>      null,
      'name' =>     $page['category']['name'],
      'comment' =>  $page['category']['comment'],
      );

    if (!empty($page['category']['representative_picture_id']))
    {
      $query = '
SELECT * FROM '.IMAGES_TABLE.'
  WHERE id = '. $page['category']['representative_picture_id'] .'
;';
      $image_infos = pwg_db_fetch_assoc(pwg_query($query));

      $tpl_vars['QUICK_EDIT']['img'] = DerivativeImage::get_one(IMG_SQUARE, $image_infos)->get_url();
    }
  }


  $template->assign(array(
    'ADMINTOOLS_PATH' => './plugins/' . ADMINTOOLS_ID .'/',
    'ato' => $tpl_vars,
  ));

  $template->set_filename('ato_public_controller', realpath(ADMINTOOLS_PATH . 'template/public_controller.tpl'));
  $template->parse('ato_public_controller');

  if ($MultiView->is_admin() && @$admin_lang !== false)
  {
    switch_lang_back();
  }
}

/**
 * Add main toolbar to current page
 * @trigger loc_after_page_header
 */
function admintools_add_admin_controller()
{
  global $MultiView, $conf, $template, $page, $user;

  $url_root = get_root_url();
  $tpl_vars = array();

  $tpl_vars['MULTIVIEW'] =     $MultiView->get_data();
  $tpl_vars['DELETE_CACHE'] =  isset($conf['multiview_invalidate_cache']);
  $tpl_vars['U_SELF'] =        $MultiView->get_clean_admin_url(true);
  
  if (($admin_lang = $MultiView->get_user_language()) !== false)
  {
    include_once(PHPWG_ROOT_PATH . 'include/functions_mail.inc.php');
    switch_lang_to($admin_lang);
  }

  $template->assign(array(
    'ADMINTOOLS_PATH' => './plugins/' . ADMINTOOLS_ID .'/',
    'ato' => $tpl_vars,
  ));

  $template->set_filename('ato_admin_controller', realpath(ADMINTOOLS_PATH . 'template/admin_controller.tpl'));
  $template->parse('ato_admin_controller');

  if ($MultiView->is_admin() && @$admin_lang !== false)
  {
    switch_lang_back();
  }
}

function admintools_add_admin_controller_setprefilter()
{
  global $template;
  $template->set_prefilter('header', 'admintools_admin_prefilter');
}

function admintools_admin_prefilter($content)
{
  $search = '<a class="icon-brush tiptip" href="{$U_CHANGE_THEME}" title="{\'Switch to clear or dark colors for administration\'|translate}">{\'Change Admin Colors\'|translate}</a>';
  $replace = '<span id="ato_container"><a class="icon-cog-alt" href="#">{\'Tools\'|translate}</a></span>';
  return str_replace($search, $replace, $content);
}

/**
 * Disable privacy level switchbox
 */
function admintools_remove_privacy($content)
{
  $search = '{if $display_info.privacy_level and isset($available_permission_levels)}';
  $replace = '{if false}';
  return str_replace($search, $replace, $content);
}

/**
 * Save picture form
 * @trigger loc_begin_picture
 */
function admintools_save_picture()
{
  global $page, $conf, $MultiView, $user, $picture;

  if (!isset($_GET['delete']) and !isset($_POST['action']) and @$_POST['action'] != 'quick_edit')
  {
    return;
  }

  $query = 'SELECT added_by FROM '. IMAGES_TABLE .' WHERE id = '. $page['image_id'] .';';
  list($added_by) = pwg_db_fetch_row(pwg_query($query));

  if (!$MultiView->is_admin() and $user['id'] != $added_by)
  {
    return;
  }

  if (isset($_GET['delete']) and get_pwg_token()==@$_GET['pwg_token'])
  {
    include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');

    delete_elements(array($page['image_id']), true);
    invalidate_user_cache();

    if (isset($page['rank_of'][ $page['image_id'] ]))
    {
      redirect(
        duplicate_index_url(
          array(
            'start' =>
              floor($page['rank_of'][ $page['image_id'] ] / $page['nb_image_page'])
              * $page['nb_image_page']
            )
          )
        );
    }
    else
    {
      redirect(make_index_url());
    }
  }

  if ($_POST['action'] == 'quick_edit')
  {
    include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');

    $data = array(
      'name' =>   $_POST['name'],
      'author' => $_POST['author'],
      );

    if ($MultiView->is_admin())
    {
      $data['level'] = $_POST['level'];
    }

    if ($conf['allow_html_descriptions'])
    {
      $data['comment'] = @$_POST['comment'];
    }
    else
    {
      $data['comment'] = strip_tags(@$_POST['comment']);
    }

    if (!empty($_POST['date_creation']) and strtotime($_POST['date_creation']) !== false)
    {
      $data['date_creation'] = $_POST['date_creation'] .' '. $_POST['date_creation_time'];
    }

    single_update(
      IMAGES_TABLE,
      $data,
      array('id' => $page['image_id'])
      );

    $tag_ids = array();
    if (!empty($_POST['tags']))
    {
      $tag_ids = get_tag_ids($_POST['tags']);
    }
    set_tags($tag_ids, $page['image_id']);
  }
}

/**
 * Save category form
 * @trigger loc_begin_index
 */
function admintools_save_category()
{
  global $page, $conf, $MultiView;

  if (!$MultiView->is_admin())
  {
    return;
  }

  if (@$_POST['action'] == 'quick_edit')
  {
    $data = array(
      'name' => $_POST['name'],
      );

    if ($conf['allow_html_descriptions'])
    {
      $data['comment'] = @$_POST['comment'];
    }
    else
    {
      $data['comment'] = strip_tags(@$_POST['comment']);
    }

    single_update(
      CATEGORIES_TABLE,
      $data,
      array('id' => $page['category']['id'])
      );

    redirect(duplicate_index_url());
  }
}