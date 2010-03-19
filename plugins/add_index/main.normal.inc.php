<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

class NormalAddIndex extends AddIndex
{
  function get_popup_help_content($popup_help_content, $page)
  {
    if (in_array($page, array('advanced_feature', 'site_manager')))
    {
      $help_content =
        load_language('help/'.$page.'.html', $this->path, array('return'=>true) );
    }
    else
    {
      $help_content = false;
    }

    if ($help_content == false)
    {
      return $popup_help_content;
    }
    else
    {
      return $popup_help_content.$help_content;
    }
  }
}

// Create object
$add_index = new NormalAddIndex();

// Add events
add_event_handler('get_popup_help_content', array(&$add_index, 'get_popup_help_content'), EVENT_HANDLER_PRIORITY_NEUTRAL, 2);

?>