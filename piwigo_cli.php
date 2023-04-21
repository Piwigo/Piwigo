#!/usr/bin/php
<?php

define('PHPWG_ROOT_PATH', dirname($argv[0]) . '/');
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');
defined('PWG_LOCAL_DIR') or define('PWG_LOCAL_DIR', 'local/');
define('DEFAULT_PREFIX_TABLE', 'piwigo_');
include(PHPWG_ROOT_PATH.PWG_LOCAL_DIR .'config/database.inc.php');
include(PHPWG_ROOT_PATH . 'include/dblayer/functions_'.$conf['dblayer'].'.inc.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include(PHPWG_ROOT_PATH . 'admin/include/functions_install.inc.php');
include(PHPWG_ROOT_PATH . 'admin/include/functions_upgrade.php');
include(PHPWG_ROOT_PATH . 'include/constants.php');

// all namespaces with options
$namespaces_data = array('db:install' => array('language:'),
                         'user:admin:create' => array('admin_name:', 'admin_pass:', 'admin_mail:', 'language:'),
);

// command line must starts with -c namespace
$namespaces = array_keys($namespaces_data);
if ($argc < 3 || $argv[1] != '-c' || !in_array($argv[2], $namespaces)) {
  $namespaces_str = implode('|', $namespaces);
  exit("Usage: $argv[0]: -c [$namespaces_str]" . PHP_EOL);
}

// load extra parameter for this namespace
$namespace = $argv[2];
$options = getopt('c:', $namespaces_data[$namespace]);

// verify if all parameters are set
$error = false;
$namespace_options = '';
foreach ($namespaces_data[$namespace] as $parameter) {
  if(str_ends_with($parameter, ':')) {
    $parameter = substr($parameter, 0, -1);
    $namespace_options .= " --$parameter <$parameter>";
  } else {
    $namespace_options .= " --$parameter";
  }
  if (!isset($options[$parameter])) {
    $error = true;
  }
}
if ($error) {
  exit("Some arguments are missing.". PHP_EOL . "Usage: $argv[0]: -c $namespace$namespace_options" . PHP_EOL);
}

function get_all_languages()
{
  include(PHPWG_ROOT_PATH . 'admin/include/languages.class.php');
  return new languages('utf-8');
}

function validate_language($language, $languages) {
  $languages_available = array_keys($languages->fs_languages);
  if (!in_array($language, $languages_available))
  {
    exit("Invalid language $language (not in " . implode(', ', $languages_available) . ") ". PHP_EOL . "Usage: $argv[0]: -c $namespace$namespace_options" . PHP_EOL);
  }
}

$errors = array();
install_db_connect($conf['db_host'], $conf['db_user'], $conf['db_password'], $conf['db_base'], $errors);
if ($namespace == 'db:install') {
  $language = $options['language'];
  $languages = get_all_languages();
  validate_language($language, $languages);
  initialize_db($languages, $language, $prefixeTable);
  mark_all_upgrades_as_done();
}
else if ($namespace == 'user:admin:create')
{
  $language = $options['language'];
  $languages = get_all_languages();
  validate_language($language, $languages);
  add_admin($options['admin_name'], $options['admin_pass'], $options['admin_mail'], $options['language']);
}

?>
