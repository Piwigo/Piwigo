<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description = 'add config parameters to display by default or not "related tags" [Aborted] ';

//The contents of the database upgrade have been emptied 
//due to the fact that we decicded to not use this configuration
//in the end for the related tags display
//We need to keep this database ugrapde to avoid errors 
//for those that have already passed the "180"th upgrade.
//This database upgrade now does nothing.
?>
