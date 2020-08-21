<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('tail'=>'footer.tpl'));

trigger_notify('loc_begin_page_tail');

$template->assign(
  array(
    'VERSION' => $conf['show_version'] ? PHPWG_VERSION : '',
    'PHPWG_URL' => defined('PHPWG_URL') ? str_replace('http:', 'https:', PHPWG_URL) : '',
    ));

//--------------------------------------------------------------------- contact

if (!is_a_guest())
{
  $template->assign(
    'CONTACT_MAIL', get_webmaster_mail_address()
    );
}

//--------------------------------------------------------- update notification
if ($conf['update_notify_check_period'] > 0)
{
  $check_for_updates = false;
  if (isset($conf['update_notify_last_check']))
  {
    if (strtotime($conf['update_notify_last_check']) < strtotime($conf['update_notify_check_period'].' seconds ago'))
    {
      $check_for_updates = true;
    }
  }
  else
  {
    $check_for_updates = true;
  }

  if ($check_for_updates)
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    include_once(PHPWG_ROOT_PATH.'admin/include/updates.class.php');
    $updates = new updates();
    $updates->notify_piwigo_new_versions();
  }
}

//------------------------------------------------------------- generation time
$debug_vars = array();

if ($conf['show_queries'])
{
  $debug_vars = array_merge($debug_vars, array('QUERIES_LIST' => $debug) );
}

if ($conf['show_gt'])
{
  if (!isset($page['count_queries']))
  {
    $page['count_queries'] = 0;
    $page['queries_time'] = 0;
  }
  $time = get_elapsed_time($t2, get_moment());

  $debug_vars = array_merge($debug_vars,
    array('TIME' => $time,
          'NB_QUERIES' => $page['count_queries'],
          'SQL_TIME' => number_format($page['queries_time'],3,'.',' ').' s')
          );
}

$template->assign('debug', $debug_vars );

//------------------------------------------------------------- mobile version
if ( !empty($conf['mobile_theme']) && (get_device() != 'desktop' || mobile_theme()))
{
  $template->assign('TOGGLE_MOBILE_THEME_URL',
      add_url_params(
        htmlspecialchars($_SERVER['REQUEST_URI']),
        array('mobile' => mobile_theme() ? 'false' : 'true')
      )
    );
}

trigger_notify('loc_end_page_tail');
//
// Generate the page
//
$template->parse('tail');
$template->p();
?>