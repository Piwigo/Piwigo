<?php
/*
Version: 1.0
Plugin Name: Validated By
Description: Adds a field of text of validated by whom.
Plugin URI: NULL
Author: adamazm
Author URI: https://github.com/adamazm/
*/

// security
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'validated_by')
{
  add_event_handler('init', 'vb_error');  // vb = validated_by
  function vb_error()
  {
    global $page;
    $page['errors'][] = 'Validated By folder name is incorrect, uninstall the plugin and rename it to "validated_by"';
  }
  return;
}

// plugin constants
define('VB_ID',      basename(dirname(__FILE__)));  // plugin ID
define('VB_PATH',   PHPWG_PLUGINS_PATH . VB_ID . '/');  // plugin path
define('VB_WEB_PATH',   get_root_url() . 'admin.php?page=plugin-' . VB_ID); // plugin admin.php

global $prefixeTable;
define('VB_NAMES', $prefixeTable.'vb_names');  // The database

// creation of database
vb_init();

function vb_init()
{
  /**
   *  Values for the database : 
   *  vb_id(int) : identifier/primary key
   *  image_id(int) : identifier for image associated with the validation
   *  vb_name(varchar) : the value/name of the validator of the image
   */
  $query = '
  CREATE TABLE IF NOT EXISTS '.VB_NAMES.'(
    vb_id int(11) NOT NULL AUTO_INCREMENT, 
    image_id int NOT NULL,
    vb_name varchar(100) ,
    PRIMARY KEY (vb_id)
  ) ENGINE=MyISAM DEFAULT CHARACTER SET utf8
  ;';
	pwg_query($query);
}


// includes
include_once(VB_PATH.'include/modify.php');  // file for modifying a validation in the edit page
include_once(VB_PATH.'include/show_vb.php');  //file for showing the validator in the public section
include_once(VB_PATH.'include/single_mode_modify.php');  //file for modifying the validator in the single mode
?>



