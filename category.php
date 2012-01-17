<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

/*
This file contains now only code to ensure backward url compatibility with
versions before 1.6
*/

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

$url_params=array();
if ( isset($_GET['cat']) )
{
  if ( is_numeric($_GET['cat']) )
  {
    $url_params['section'] = 'categories';
    $result = get_cat_info( $_GET['cat'] );
    if ( !empty($result) )
    {
      $url_params['category'] = $result;
    }
  }
  elseif ( in_array($_GET['cat'],
              array('best_rated','most_visited','recent_pics','recent_cats')
                  )
         )
  {
    $url_params['section'] = $_GET['cat'];
  }
  else
  {
    page_not_found('');
  }
}

$url = make_index_url($url_params);
if (!headers_sent())
{
  set_status_header(301);
  redirect_http( $url );
}
redirect ( $url );

?>
