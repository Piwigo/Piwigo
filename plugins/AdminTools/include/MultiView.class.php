<?php
defined('ADMINTOOLS_PATH') or die('Hacking attempt!');

/**
 * Class managing multi views system
 */
class MultiView
{
  /** @var bool $is_admin */
  private $is_admin = false;

  /** @var array $data */
  private $data = array();
  private $data_url_params = array();

  /** @var array $user */
  private $user = array();

  /**
   * Constructor, load $data from session
   */
  function __construct()
  {
    global $conf;

    $this->data = array_merge(
      array(
        'view_as' => 0,
        'theme' => '',
        'lang' => '',
        'show_queries' => $conf['show_queries'],
        'debug_l10n' => $conf['debug_l10n'],
        'debug_template' => $conf['debug_template'],
        'template_combine_files' => $conf['template_combine_files'],
        'no_history' => false,
        ),
      pwg_get_session_var('multiview', array())
      );

    $this->data_url_params = array_keys($this->data);
    $this->data_url_params = array_map(create_function('$d', 'return "ato_".$d;'), $this->data_url_params);
  }

  /**
   * @return bool
   */
  public function is_admin()
  {
    return $this->is_admin;
  }

  /**
   * @return array
   */
  public function get_data()
  {
    return $this->data;
  }

  /**
   * @return array
   */
  public function get_user()
  {
    return $this->user;
  }

  /**
   * Save $data in session
   */
  private function save()
  {
    pwg_set_session_var('multiview', $this->data);
  }

  /**
   * Returns the current url minus MultiView params
   *
   * @param bool $with_amp - adds ? or & at the end of the url
   * @return string
   */
  public function get_clean_url($with_amp=false)
  {
    if (script_basename() == 'picture')
    {
      $url = duplicate_picture_url(array(), $this->data_url_params);
    }
    else if (script_basename() == 'index')
    {
      $url = duplicate_index_url(array(), $this->data_url_params);
    }
    else
    {
      $url = get_query_string_diff($this->data_url_params);
    }

    if ($with_amp)
    {
      $url.= strpos($url, '?')!==false ? '&' : '?';
    }

    return $url;
  }
  
  /**
   * Returns the current url minus MultiView params
   *
   * @param bool $with_amp - adds ? or & at the end of the url
   * @return string
   */
  public function get_clean_admin_url($with_amp=false)
  {
    $url = PHPWG_ROOT_PATH.'admin.php';
    
    $get = $_GET;
    unset($get['page'], $get['section'], $get['tag']);
    if (count($get) == 0 and !empty($_SERVER['QUERY_STRING']))
    {
      $url.= '?' . str_replace('&', '&amp;', $_SERVER['QUERY_STRING']);
    }
    
    if ($with_amp)
    {
      $url.= strpos($url, '?')!==false ? '&' : '?';
    }
    
    return $url;
  }

  /**
   * Triggered on "user_init", change current view depending of URL params.
   */
  public function user_init()
  {
    global $user, $conf;

    $this->is_admin = is_admin();

    $this->user = array(
      'id' => $user['id'],
      'username' => $user['username'],
      'language' => $user['language'],
      'theme' => $user['theme'],
      );

    // inactive on ws.php to allow AJAX admin tasks
    if ($this->is_admin && script_basename() != 'ws')
    {
      // show_queries
      if (isset($_GET['ato_show_queries']))
      {
        $this->data['show_queries'] = (bool)$_GET['ato_show_queries'];
      }
      $conf['show_queries'] = $this->data['show_queries'];

      if ($this->data['view_as'] == 0)
      {
        $this->data['view_as'] = $user['id'];
      }
      if (empty($this->data['lang']))
      {
        $this->data['lang'] = $user['language'];
      }
      if (empty($this->data['theme']))
      {
        $this->data['theme'] = $user['theme'];
      }

      // view_as
      if (!defined('IN_ADMIN'))
      {
        if (isset($_GET['ato_view_as']))
        {
          $this->data['view_as'] = (int)$_GET['ato_view_as'];
        }
        if ($this->data['view_as'] != $user['id'])
        {
          $user = build_user($this->data['view_as'], true);
          if (isset($_GET['ato_view_as']))
          {
            $this->data['theme'] = $user['theme'];
            $this->data['lang'] = $user['language'];
          }
        }
      }

      // theme
      if (isset($_GET['ato_theme']))
      {
        $this->data['theme'] = $_GET['ato_theme'];
      }
      $user['theme'] = $this->data['theme'];

      // lang
      if (isset($_GET['ato_lang']))
      {
        $this->data['lang'] = $_GET['ato_lang'];
      }
      $user['language'] = $this->data['lang'];

      // debug_l10n
      if (isset($_GET['ato_debug_l10n']))
      {
        $this->data['debug_l10n'] = (bool)$_GET['ato_debug_l10n'];
      }
      $conf['debug_l10n'] = $this->data['debug_l10n'];

      // debug_template
      if (isset($_GET['ato_debug_template']))
      {
        $this->data['debug_template'] = (bool)$_GET['ato_debug_template'];
      }
      $conf['debug_template'] = $this->data['debug_template'];

      // template_combine_files
      if (isset($_GET['ato_template_combine_files']))
      {
        $this->data['template_combine_files'] = (bool)$_GET['ato_template_combine_files'];
      }
      $conf['template_combine_files'] = $this->data['template_combine_files'];

      // no_history
      if (isset($_GET['ato_no_history']))
      {
        $this->data['no_history'] = (bool)$_GET['ato_no_history'];
      }
      if ($this->data['no_history'])
      {
        add_event_handler('pwg_log_allowed', create_function('', 'return false;'));
      }

      $this->save();
    }
  }

  /**
   * Returns the language of the current user if different from the current language
   * false otherwise
   */
  function get_user_language()
  {
    if (isset($this->user['language']) && isset($this->data['lang'])
        && $this->user['language'] != $this->data['lang']
      )
    {
      return $this->user['language'];
    }
    return false;
  }

  /**
   * Triggered on "init", in order to clean template files (not initialized on "user_init")
   */
  public function init()
  {
    if ($this->is_admin)
    {
      if (isset($_GET['ato_purge_template']))
      {
        global $template;
        $template->delete_compiled_templates();
        FileCombiner::clear_combined_files();
      }
    }
  }

  /**
   * Mark browser session cache for deletion
   */
  public static function invalidate_cache()
  {
    global $conf;
    conf_update_param('multiview_invalidate_cache', true, true);
  }

  /**
   * Register custom API methods
   */
  public static function register_ws($arr)
  {
    $service = &$arr[0];

    $service->addMethod(
      'multiView.getData',
      array('MultiView', 'ws_get_data'),
      array(),
      'AdminTools private method.',
      null,
      array('admin_only' => true, 'hidden' => true)
      );
  }

  /**
   * API method
   * Return full list of users, themes and languages
   */
  public static function ws_get_data($params)
  {
    global $conf;

    // get users
    $query = '
SELECT
  '.$conf['user_fields']['id'].' AS id,
  '.$conf['user_fields']['username'].' AS username,
  status
FROM '.USERS_TABLE.' AS u
  INNER JOIN '.USER_INFOS_TABLE.' AS i
    ON '.$conf['user_fields']['id'].' = user_id
  ORDER BY CONVERT('.$conf['user_fields']['username'].', CHAR)
;';
    $out['users'] = array_from_query($query);

    // get themes
    include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
    $themes = new themes();
    foreach (array_keys($themes->db_themes_by_id) as $theme)
    {
      if (!empty($theme))
      {
        $out['themes'][] = $theme;
      }
    }

    // get languages
    foreach (get_languages() as $code => $name)
    {
      $out['languages'][] = array(
        'id' => $code,
        'name' => $name,
        );
    }

    conf_delete_param('multiview_invalidate_cache');

    return $out;
  }
}