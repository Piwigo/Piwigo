<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class PwgInstall
{
  /** @var array values of the form, with the slashes added to $_POST */
  public $params = array();

  /** @var string path of database.inc.php */
  private $config_file;

  /** @var array|null result of check_requirements() */
  private $requirements = null;

  /** @var array field name => message */
  public $errors = array();

  /**
   * @param array $params dbhost, dbuser, dbpasswd, dbname, prefix,
   *   admin_name, admin_pass1, admin_pass2, admin_mail, language
   */
  public function __construct($params)
  {
    $defaults = array(
      'dbhost' => 'localhost',
      'dbuser' => '',
      'dbpasswd' => '',
      'dbname' => '',
      'prefix' => 'piwigo_',
      'admin_name' => '',
      'admin_pass1' => '',
      'admin_pass2' => '',
      'admin_mail' => '',
      'language' => 'en_UK',
      );

    foreach ($defaults as $name => $default)
    {
      $this->params[$name] = (isset($params[$name]) && is_string($params[$name])) ? $params[$name] : $default;
    }

    foreach (array('dbhost', 'dbuser', 'dbname', 'admin_name', 'admin_mail') as $name)
    {
      $this->params[$name] = trim($this->params[$name]);
    }
    $this->params['admin_name'] = preg_replace('/\s{2,}/', ' ', $this->params['admin_name']);

    $this->config_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR.'config/database.inc.php';
  }

  /**
   * Checks the server, before any connection to the database.
   *
   * @return array each line holds status (ok, warn, fail), label, detail
   */
  public function check_requirements()
  {
    if (null !== $this->requirements)
    {
      return $this->requirements;
    }

    include_once(PHPWG_ROOT_PATH.'admin/include/image.class.php');

    $requirements = array();

    $is_valid = version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '>=');
    $requirements[] = array(
      'status' => $is_valid ? 'ok' : 'fail',
      'label' => 'PHP '.PHP_VERSION,
      'detail' => $is_valid ? l10n('compatible') : l10n('PHP version %s required (you are running on PHP %s)', REQUIRED_PHP_VERSION, PHP_VERSION),
      );

    $is_valid = extension_loaded('mysqli');
    $requirements[] = array(
      'status' => $is_valid ? 'ok' : 'fail',
      'label' => 'mysqli',
      'detail' => $is_valid ? l10n('PHP extension enabled') : l10n('PHP extension "%s" is not loaded', 'mysqli'),
      );

    $libraries = array();
    if (pwg_image::is_gd())
    {
      $libraries[] = 'GD';
    }
    if (pwg_image::is_imagick())
    {
      $libraries[] = 'Imagick';
    }
    if (pwg_image::is_ext_imagick())
    {
      $libraries[] = 'ImageMagick';
    }
    $requirements[] = array(
      'status' => count($libraries) > 0 ? 'ok' : 'warn',
      'label' => l10n('Graphics Library'),
      'detail' => count($libraries) > 0 ? implode(', ', $libraries) : l10n('GD or ImageMagick is needed to resize photos'),
      );

    $config_dir = PWG_LOCAL_DIR.'config';
    $is_valid = is_writable(dirname($this->config_file));
    $requirements[] = array(
      'status' => $is_valid ? 'ok' : 'fail',
      'label' => $config_dir,
      'detail' => $is_valid ? l10n('directory is writable') : l10n('Add write access to the "%s" directory', $config_dir),
      );

    $this->requirements = $requirements;

    return $requirements;
  }

  /**
   * @return bool false when a requirement fails
   */
  public function can_install()
  {
    return !in_array('fail', array_column($this->check_requirements(), 'status'));
  }

  /**
   * Validates every parameter, writes nothing.
   *
   * @return bool
   */
  public function check()
  {
    if (!$this->can_install())
    {
      $this->errors['requirements'] = true;
    }

    foreach (array('dbhost', 'dbuser', 'dbname', 'admin_name', 'admin_pass1', 'admin_mail') as $field)
    {
      if ('' === $this->params[$field])
      {
        $this->errors[$field] = l10n('must not be empty');
      }
    }

    $prefix = $this->params['prefix'];
    if (strlen($prefix) > 20 || preg_match('/^\d/', $prefix) || !preg_match('/^[a-zA-Z0-9_$]*$/', $prefix))
    {
      $this->errors['prefix'] = l10n('Letters, digits, _ and $ only, 20 characters at most, not starting with a digit');
    }

    if (preg_match('/[\'"]/', $this->params['admin_name']))
    {
      $this->errors['admin_name'] = l10n('webmaster login can\'t contain characters \' or "');
    }

    if ($this->params['admin_name'] != strip_tags($this->params['admin_name']))
    {
      $this->errors['admin_name'] = l10n('html tags are not allowed in login');
    }

    if ('' !== $this->params['admin_pass1'] && $this->params['admin_pass1'] !== $this->params['admin_pass2'])
    {
      $this->errors['admin_pass2'] = l10n('please enter your password again');
    }

    if ('' !== $this->params['admin_mail'] && !email_check_format($this->params['admin_mail']))
    {
      $this->errors['admin_mail'] = l10n('mail address must be like xxx@yyy.eee (example : jack@altern.org)');
    }

    if (!preg_match('/^[a-z]{2,3}_[A-Z]{2}$/', $this->params['language']) || !is_dir(PHPWG_ROOT_PATH.'language/'.$this->params['language']))
    {
      $this->errors['language'] = l10n('Invalid language');
    }

    return count($this->errors) == 0;
  }

  /**
   * Connects to the database, checks its version and sets the charset.
   *
   * @return bool
   */
  public function connect()
  {
    if (!extension_loaded('mysqli'))
    {
      $this->errors['dbhost'] = l10n('PHP extension "%s" is not loaded', 'mysqli');
      return false;
    }

    include_once(PHPWG_ROOT_PATH.'include/dblayer/functions_mysqli.inc.php');

    try
    {
      @pwg_db_connect($this->params['dbhost'], $this->params['dbuser'], $this->params['dbpasswd'], $this->params['dbname']);
    }
    catch (Exception $e)
    {
      $this->set_connection_error($e);
      return false;
    }

    if (version_compare(pwg_get_db_version(), REQUIRED_MYSQL_VERSION, '<'))
    {
      $this->errors['dbhost'] = l10n('MySQL %s required (this server runs %s)', REQUIRED_MYSQL_VERSION, pwg_get_db_version());
      return false;
    }

    pwg_db_check_charset();

    if ($this->is_prefix_used())
    {
      $this->errors['prefix'] = l10n('A Piwigo is already installed in this database with this prefix');
      return false;
    }

    return true;
  }

  /**
   * Files a failed connection under the field to fix.
   *
   * @param Exception $e
   */
  private function set_connection_error($e)
  {
    global $mysqli;

    if ($e instanceof mysqli_sql_exception)
    {
      $errno = $e->getCode();
      $detail = $e->getMessage();
    }
    else
    {
      $errno = mysqli_connect_errno();
      $detail = mysqli_connect_error();
      if (0 == $errno && $mysqli instanceof mysqli)
      {
        $errno = $mysqli->errno;
        $detail = $mysqli->error;
      }
    }

    $field = 'dbhost';
    $message = l10n('Can\'t connect to server');
    if (1045 == $errno)
    {
      $field = 'dbuser';
    }
    else if (in_array($errno, array(1044, 1049)))
    {
      $field = 'dbname';
      $message = l10n('Connection to server succeed, but it was impossible to connect to database');
    }

    $this->errors[$field] = empty($detail) ? $message : $message.' ('.$detail.')';
  }

  /**
   * @return bool true when the database already holds a table with this prefix
   */
  private function is_prefix_used()
  {
    $query = '
SHOW TABLES LIKE \''.$this->params['prefix'].'%\'
;';

    return pwg_db_num_rows(pwg_query($query)) > 0;
  }

  /**
   * @return string content of database.inc.php
   */
  public function get_config_content()
  {
    return '<?php
$conf[\'dblayer\'] = \'mysqli\';
$conf[\'db_base\'] = '.var_export($this->params['dbname'], true).';
$conf[\'db_user\'] = '.var_export($this->params['dbuser'], true).';
$conf[\'db_password\'] = '.var_export($this->params['dbpasswd'], true).';
$conf[\'db_host\'] = '.var_export($this->params['dbhost'], true).';

$prefixeTable = '.var_export($this->params['prefix'], true).';

define(\'PHPWG_INSTALLED\', true);
define(\'PWG_CHARSET\', \'utf-8\');
define(\'DB_CHARSET\', \'utf8\');
define(\'DB_COLLATE\', \'\');

?'.'>';
  }

  /**
   * @return bool false when database.inc.php can not be written
   */
  public function write_config()
  {
    @umask(0111);
    if (false === @file_put_contents($this->config_file, $this->get_config_content()))
    {
      $this->errors['config'] = l10n('Creation of config file local/config/database.inc.php failed.');
      return false;
    }

    return true;
  }

  /**
   * Creates the tables, fills the configuration, activates the language
   * and the core themes.
   */
  public function create_database()
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    include_once(PHPWG_ROOT_PATH.'admin/include/functions_install.inc.php');
    include_once(PHPWG_ROOT_PATH.'admin/include/languages.class.php');

    $prefixeTable = $this->params['prefix'];

    try
    {
      // tables creation, based on piwigo_structure.sql
      execute_sqlfile(
        PHPWG_ROOT_PATH.'install/piwigo_structure-mysql.sql',
        DEFAULT_PREFIX_TABLE,
        $prefixeTable,
        'mysql'
        );
      // We fill the tables with basic informations
      execute_sqlfile(
        PHPWG_ROOT_PATH.'install/config.sql',
        DEFAULT_PREFIX_TABLE,
        $prefixeTable,
        'mysql'
        );

      $query = '
INSERT INTO '.$prefixeTable.'config (param,value,comment)
   VALUES (\'secret_key\',\''.sha1(random_bytes(1000)).'\',
   \'a secret key specific to the gallery for internal use\');';
      pwg_query($query);

      conf_update_param('piwigo_db_version', get_branch_from_version(PHPWG_VERSION));
      conf_update_param('gallery_title', pwg_db_real_escape_string(l10n('Just another Piwigo gallery')));

      conf_update_param(
        'page_banner',
        '<h1>%gallery_title%</h1>'."\n\n<p>".pwg_db_real_escape_string(l10n('Welcome to my photo gallery')).'</p>'
        );

      // fill languages table, only activate the current language
      $languages = new languages('utf-8');
      $languages->perform_action('activate', $this->params['language']);

      // fill $conf global array
      load_conf_from_db();

      // PWG_CHARSET is required for building the fs_themes array in the
      // themes class
      if (!defined('PWG_CHARSET'))
      {
        define('PWG_CHARSET', 'utf-8');
      }
      activate_core_themes();
      activate_core_plugins();
    }
    catch (mysqli_sql_exception $e)
    {
      $this->errors['database'] = $e->getMessage();
      return false;
    }

    return true;
  }

  /**
   * Creates the local site, the webmaster and the guest.
   */
  public function create_users()
  {
    global $conf;

    try
    {
      $insert = array(
        'id' => 1,
        'galleries_url' => PHPWG_ROOT_PATH.'galleries/',
        );
      mass_inserts(SITES_TABLE, array_keys($insert), array($insert));

      // webmaster admin user
      $inserts = array(
        array(
          'id' => 1, // must be the same value as webmaster_id in config.sql
          'username' => $this->params['admin_name'],
          'password' => $conf['password_hash']($this->params['admin_pass1']),
          'mail_address' => $this->params['admin_mail'],
          ),
        array(
          'id' => 2,
          'username' => 'guest',
          ),
        );
      mass_inserts(USERS_TABLE, array_keys($inserts[0]), $inserts);

      create_user_infos(array(1,2), array('language' => $this->params['language']));
    }
    catch (mysqli_sql_exception $e)
    {
      $this->errors['database'] = $e->getMessage();
      return false;
    }

    return true;
  }

  /**
   * Marks every available upgrade as already applied.
   */
  public function mark_upgrades_applied()
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions_upgrade.php');

    try
    {
      // Available upgrades must be ignored after a fresh installation. To
      // make PWG avoid upgrading, we must tell it upgrades have already been
      // made.
      list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

      $datas = array();
      foreach (get_available_upgrade_ids() as $upgrade_id)
      {
        $datas[] = array(
          'id' => $upgrade_id,
          'applied' => $dbnow,
          'description' => 'upgrade included in installation',
          );
      }
      mass_inserts(
        UPGRADE_TABLE,
        array_keys($datas[0]),
        $datas
        );
    }
    catch (mysqli_sql_exception $e)
    {
      $this->errors['database'] = $e->getMessage();
      return false;
    }

    return true;
  }
}
