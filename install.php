<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');

// Guess an initial language ... 
function guess_lang()
{
  return 'en_UK.iso-8859-1';

//   $languages = array();
//   $i = 0;
//   if ($opendir = opendir(PHPWG_ROOT_PATH.'language/'))
//   {
//     while ( $file = readdir ( $opendir ) )
//     {
//       if ( is_dir ( PHPWG_ROOT_PATH.'language/'.$file )&& !substr_count($file,'.'))
//       {
//         $languages[$i++] =$file;
//       }
//     }
//   }

//   if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
//   {
//     $accept_lang_ary = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
//     for ($i = 0; $i < sizeof($accept_lang_ary); $i++)
//     {
//       for ($j=0; $j<sizeof($languages); $j++)
//       {
//         if (preg_match('#' . substr($languages[$j],0,2) . '#i', substr(trim($accept_lang_ary[$i]),0,2)))
//         {
//           if (file_exists(PHPWG_ROOT_PATH . 'language/' . $languages[$j].'/install.lang.php'))
// 		  {
//                     return $languages[$j];
// 		  }
//         }
//       }
//     }
//   }
//   return 'en_EN';
}

/**
 * loads an sql file and executes all queries
 *
 * Before executing a query, $replaced is... replaced by $replacing. This is
 * useful when the SQL file contains generic words. Drop table queries are
 * not executed.
 *
 * @param string filepath
 * @param string replaced
 * @param string replacing
 * @return void
 */
function execute_sqlfile($filepath, $replaced, $replacing)
{
  $sql_lines = file($filepath);
  $query = '';
  foreach ($sql_lines as $sql_line)
  {
    $sql_line = trim($sql_line);
    if (preg_match('/(^--|^$)/', $sql_line))
    {
      continue;
    }
    $query.= ' '.$sql_line;
    // if we reached the end of query, we execute it and reinitialize the
    // variable "query"
    if (preg_match('/;$/', $sql_line))
    {
      $query = trim($query);
      $query = str_replace($replaced, $replacing, $query);
      // we don't execute "DROP TABLE" queries
      if (!preg_match('/^DROP TABLE/i', $query))
      {
        mysql_query($query);
      }
      $query = '';
    }
  }
}

set_magic_quotes_runtime(0); // Disable magic_quotes_runtime
//
// addslashes to vars if magic_quotes_gpc is off this is a security
// precaution to prevent someone trying to break out of a SQL statement.
//
if( !get_magic_quotes_gpc() )
{
  if( is_array($_POST) )
  {
    while( list($k, $v) = each($_POST) )
    {
      if( is_array($_POST[$k]) )
      {
        while( list($k2, $v2) = each($_POST[$k]) )
        {
          $_POST[$k][$k2] = addslashes($v2);
        }
        @reset($_POST[$k]);
      }
      else
      {
        $_POST[$k] = addslashes($v);
      }
    }
    @reset($_POST);
  }

  if( is_array($_COOKIE) )
  {
    while( list($k, $v) = each($_COOKIE) )
    {
      if( is_array($_COOKIE[$k]) )
      {
        while( list($k2, $v2) = each($_COOKIE[$k]) )
        {
          $_COOKIE[$k][$k2] = addslashes($v2);
        }
        @reset($_COOKIE[$k]);
      }
      else
      {
        $_COOKIE[$k] = addslashes($v);
      }
    }
    @reset($_COOKIE);
  }
}

//----------------------------------------------------- variable initialization
$install_style = 'default';

// Obtain various vars
$dbhost = (!empty($_POST['dbhost'])) ? $_POST['dbhost'] : 'localhost';
$dbuser = (!empty($_POST['dbuser'])) ? $_POST['dbuser'] : '';
$dbpasswd = (!empty($_POST['dbpasswd'])) ? $_POST['dbpasswd'] : '';
$dbname = (!empty($_POST['dbname'])) ? $_POST['dbname'] : '';

$table_prefix = (!empty($_POST['prefix'])) ? $_POST['prefix'] : 'phpwebgallery_';

$admin_name = (!empty($_POST['admin_name'])) ? $_POST['admin_name'] : '';
$admin_pass1 = (!empty($_POST['admin_pass1'])) ? $_POST['admin_pass1'] : '';
$admin_pass2 = (!empty($_POST['admin_pass2'])) ? $_POST['admin_pass2'] : '';
$admin_mail = (!empty($_POST['admin_mail'])) ? $_POST['admin_mail'] : '';

$infos = array();
$errors = array();

// Open config.php ... if it exists
$config_file = PHPWG_ROOT_PATH.'include/mysql.inc.php';
if (@file_exists($config_file))
{
  include($config_file);
  // Is PhpWebGallery already installed ?
  if (defined("PHPWG_INSTALLED"))
  {
    die('PhpWebGallery is already installed');
  }
}

$prefixeTable = $table_prefix;
include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include(PHPWG_ROOT_PATH . 'include/template.php');

if ( isset( $_POST['language'] ))
{
  $language = strip_tags($_POST['language']);
}
else 
{
  $language = guess_lang();
}

if (!file_exists(PHPWG_ROOT_PATH.'language/'.$language.'/install.lang.php'))
{
  $language = 'en_UK.iso-8859-1';
}

include( './language/'.$language.'/common.lang.php' );
include( './language/'.$language.'/admin.lang.php' );
include( './language/'.$language.'/install.lang.php' );
//----------------------------------------------------- template initialization
$template=setup_style($install_style);
$template->set_filenames( array('install'=>'install.tpl') );
$step = 1;
//---------------------------------------------------------------- form analyze
if ( isset( $_POST['install'] ))
{
  if ( @mysql_connect( $_POST['dbhost'],
                       $_POST['dbuser'],
                       $_POST['dbpasswd'] ) )
  {
    if ( @mysql_select_db($_POST['dbname'] ) )
    {
      array_push( $infos, $lang['step1_confirmation'] );
    }
    else
    {
      array_push( $errors, $lang['step1_err_db'] );
    }
  }
  else
  {
    array_push( $errors, $lang['step1_err_server'] );
  }
  
  $webmaster = trim(preg_replace( '/\s{2,}/', ' ', $admin_name ));
  if ( empty($webmaster))
    array_push( $errors, $lang['step2_err_login1'] );
  else if ( preg_match( '/[\'"]/', $webmaster ) )
    array_push( $errors, $lang['step2_err_login3'] );
  if ( $admin_pass1 != $admin_pass2 || empty($admin_pass1) )
    array_push( $errors, $lang['step2_err_pass'] );
  if ( empty($admin_mail))
    array_push( $errors, $lang['reg_err_mail_address'] );
  else 
  {
    $error_mail_address = validate_mail_address($admin_mail);
    if (!empty($error_mail_address))
      array_push( $errors, $error_mail_address );
  }
  
  if ( count( $errors ) == 0 )
  {
    $step = 2;
    $file_content = "<?php";
    $file_content.= "\n\$cfgBase = '".     $dbname."';";
    $file_content.= "\n\$cfgUser = '".     $dbuser."';";
    $file_content.= "\n\$cfgPassword = '". $dbpasswd."';";
    $file_content.= "\n\$cfgHote = '".     $dbhost."';";
    $file_content.= "\n";
    $file_content.= "\n\$prefixeTable = '".$table_prefix."';";
    $file_content.= "\n";
    $file_content.= "\ndefine('PHPWG_INSTALLED', true);";
    $file_content.= "\n?".">";
    
    @umask(0111);
    // writing the configuration file
    if ( !($fp = @fopen( $config_file, 'w' )))
    {
      $html_content = htmlentities( $file_content, ENT_QUOTES );
      $html_content = nl2br( $html_content );
      $template->assign_block_vars('error_copy',
                                   array('FILE_CONTENT'=>$html_content));
    }
    @fputs($fp, $file_content, strlen($file_content));
    @fclose($fp);
    
    // tables creation, based on phpwebgallery_structure.sql
    execute_sqlfile( PHPWG_ROOT_PATH.'install/phpwebgallery_structure.sql'
                     , 'phpwebgallery_'
                     , $table_prefix );
    // We fill the tables with basic informations
    execute_sqlfile( PHPWG_ROOT_PATH.'install/config.sql'
                     , 'phpwebgallery_'
                     , $table_prefix );

    $query = 'UPDATE '.CONFIG_TABLE;
    $query.= " SET value = '".$admin_mail."'";
    $query.= " WHERE param = 'mail_webmaster'";
    $query.= ';';
    mysql_query( $query );
	
    $query = 'UPDATE '.CONFIG_TABLE;
    $query.= " SET value = '".$language."'";
    $query.= " WHERE param = 'default_language'";
    $query.= ';';
    mysql_query( $query );
    
    $query = 'INSERT INTO '.SITES_TABLE;
    $query.= " (id,galleries_url) VALUES (1, '".PHPWG_ROOT_PATH."galleries/');";
    mysql_query( $query );
    
    // webmaster admin user
    $query = 'INSERT INTO '.USERS_TABLE;
    $query.= ' (id,username,password,status,language,mail_address) VALUES ';
    $query.= "(1,'".$admin_name."','".md5( $admin_pass1 )."'";
    $query.= ",'admin','".$language."'";
    $query.= ",'".$admin_mail."');";
    mysql_query($query);
    
    // guest user
    $query = 'INSERT INTO '.USERS_TABLE;
    $query.= '(id,username,password,status,language) VALUES ';
    $query.= "(2,'guest','','guest','".$language."')";
    $query.= ';';
    mysql_query( $query );
  }
}

$template->assign_vars(
  array(
    'RELEASE'=>PHPWG_VERSION,
  
    'L_BASE_TITLE'=>$lang['Initial_config'],
    'L_LANG_TITLE'=>$lang['Default_lang'],
    'L_DB_TITLE'=>$lang['step1_title'],
    'L_DB_HOST'=>$lang['step1_host'],
    'L_DB_HOST_INFO'=>$lang['step1_host_info'],
    'L_DB_USER'=>$lang['step1_user'],
    'L_DB_USER_INFO'=>$lang['step1_user_info'],
    'L_DB_PASS'=>$lang['step1_pass'],
    'L_DB_PASS_INFO'=>$lang['step1_pass_info'],
    'L_DB_NAME'=>$lang['step1_database'],
    'L_DB_NAME_INFO'=>$lang['step1_database_info'],
    'L_DB_PREFIX'=>$lang['step1_prefix'],
    'L_DB_PREFIX_INFO'=>$lang['step1_prefix_info'],
    'L_ADMIN_TITLE'=>$lang['step2_title'],
    'L_ADMIN'=>$lang['install_webmaster'],
    'L_ADMIN_INFO'=>$lang['install_webmaster_info'],
    'L_ADMIN_PASSWORD'=>$lang['step2_pwd'],
    'L_ADMIN_PASSWORD_INFO'=>$lang['step2_pwd_info'],
    'L_ADMIN_CONFIRM_PASSWORD'=>$lang['step2_pwd_conf'],
    'L_ADMIN_CONFIRM_PASSWORD_INFO'=>$lang['step2_pwd_conf_info'],
    'L_ADMIN_EMAIL'=>$lang['conf_mail_webmaster'],
    'L_ADMIN_EMAIL_INFO'=>$lang['conf_mail_webmaster_info'],
    'L_SUBMIT'=>$lang['Start_Install'],
    'L_HELP'=>$lang['install_help'],
    'L_ERR_COPY'=>$lang['step1_err_copy'],
    'L_END_TITLE'=>$lang['install_end_title'],
    'L_END_MESSAGE'=>$lang['install_end_message'],
    
    'F_ACTION'=>add_session_id( 'install.php' ),
    'F_DB_HOST'=>$dbhost,
    'F_DB_USER'=>$dbuser,
    'F_DB_NAME'=>$dbname,
    'F_DB_PREFIX'=>$table_prefix,
    'F_ADMIN'=>$admin_name,
    'F_ADMIN_EMAIL'=>$admin_mail,
    'F_LANG_SELECT'=>language_select($language),
    
    'T_CONTENT_ENCODING' => $lang_info['charset']
    ));

//------------------------------------------------------ errors & infos display
if ( sizeof( $errors ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $errors ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$errors[$i]));
  }
}

if ( sizeof( $infos ) != 0 )
{
  $template->assign_block_vars('infos',array());
  for ( $i = 0; $i < sizeof( $infos ); $i++ )
  {
    $template->assign_block_vars('infos.info',array('INFO'=>$infos[$i]));
  }
}

if ($step ==1)
{
  $template->assign_block_vars('install',array());
}
else
{
  $template->assign_block_vars('install_end',array());
}

//----------------------------------------------------------- html code display
$template->pparse('install');
?>
