<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

define ('PHPWG_ROOT_PATH', './');

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
check_status(ACCESS_FREE);
include_once(PHPWG_ROOT_PATH.'include/ws_core.inc.php');

if ( !$conf['allow_web_services'] )
{
  page_forbidden('Web services are disabled');
}

add_event_handler('ws_add_methods', 'ws_addDefaultMethods');

add_event_handler('ws_invoke_allowed', 'ws_isInvokeAllowed', EVENT_HANDLER_PRIORITY_NEUTRAL, 3);

$requestFormat = 'rest';
$responseFormat = null;

if ( isset($_GET['format']) )
{
  $responseFormat = $_GET['format'];
}

if ( !isset($responseFormat) and isset($requestFormat) )
{
  $responseFormat = $requestFormat;
}

$service = new PwgServer();

if (!is_null($requestFormat))
{
  $handler = null;
  switch ($requestFormat)
  {
    case 'rest':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/rest_handler.php');
      $handler = new PwgRestRequestHandler();
      break;
  }
  $service->setHandler($requestFormat, $handler);
}

if (!is_null($responseFormat))
{
  $encoder = null;
  switch ($responseFormat)
  {
    case 'rest':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/rest_encoder.php');
      $encoder = new PwgRestEncoder();
      break;
    case 'php':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/php_encoder.php');
      $encoder = new PwgSerialPhpEncoder();
      break;
    case 'json':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/json_encoder.php');
      $encoder = new PwgJsonEncoder();
      break;
    case 'xmlrpc':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/xmlrpc_encoder.php');
      $encoder = new PwgXmlRpcEncoder();
      break;
  }
  $service->setEncoder($responseFormat, $encoder);
}

set_make_full_url();
$service->run();


/**
 * event handler that registers standard methods with the web service
 */
function ws_addDefaultMethods( $arr )
{
  global $conf, $user;
  $service = &$arr[0];
  
  include_once(PHPWG_ROOT_PATH.'include/ws_functions.inc.php');
  
  $service->addMethod('pwg.getVersion', 'ws_getVersion',
      null,
      'retrieves the PWG version'
    );
	  
  $service->addMethod('pwg.getInfos', 'ws_getInfos',
      null,
      'retrieves general informations'
    );

  $service->addMethod('pwg.caddie.add', 'ws_caddie_add',
      array(
        'image_id'=> array('flags'=>WS_PARAM_FORCE_ARRAY),
        ),
      'adds the elements to the caddie'
    );

  $service->addMethod('pwg.categories.getImages', 'ws_categories_getImages',
      array(
        'cat_id' =>     array('default'=>0, 
                              'flags'=>WS_PARAM_FORCE_ARRAY),
        'recursive' =>  array('default'=>false ),
        'per_page' =>   array('default'=>100, 
                              'maxValue'=>$conf['ws_max_images_per_page']),
        'page' =>       array('default'=>0),
        'order' =>      array('default'=>null),
        'f_min_rate' => array('default'=>null),
        'f_max_rate' => array('default'=>null),
        'f_min_hit' =>  array('default'=>null),
        'f_max_hit' =>  array('default'=>null),
        'f_min_date_available' => array('default'=>null),
        'f_max_date_available' => array('default'=>null),
        'f_min_date_created' =>   array('default'=>null),
        'f_max_date_created' =>   array('default'=>null),
        'f_min_ratio' => array('default'=>null),
        'f_max_ratio' => array('default'=>null),
        'f_max_level' => array('default'=>null),
        ),
      'Returns elements for the corresponding categories.
<br><b>cat_id</b> can be empty if <b>recursive</b> is true. Can be sent as an array.
<br><b>order</b> comma separated fields for sorting (file,id, rating_score,...)'
    );

  $service->addMethod('pwg.categories.getList', 'ws_categories_getList',
      array(
        'cat_id' =>       array('default'=>0),
        'recursive' =>    array('default'=>false),
        'public' =>       array('default'=>false),
        'tree_output' =>  array('default'=>false),
        'fullname' =>     array('default'=>false),
        ),
      'retrieves a list of categories (tree_output option only compatible with json/php output format'
    );

  $service->addMethod('pwg.getMissingDerivatives', 'ws_getMissingDerivatives',
      array(
        'types' =>      array('default'=>array(),
                              'flags'=>WS_PARAM_FORCE_ARRAY),
        'ids' =>        array('default'=>array(),
                              'flags'=>WS_PARAM_FORCE_ARRAY),
        'max_urls' =>   array('default'=>200),
        'prev_page' =>  array('default'=>null),
        'f_min_rate' => array('default'=>null),
        'f_max_rate' => array('default'=>null),
        'f_min_hit' =>  array('default'=>null),
        'f_max_hit' =>  array('default'=>null),
        'f_min_date_available' => array('default'=>null),
        'f_max_date_available' => array('default'=>null),
        'f_min_date_created' =>   array('default'=>null),
        'f_max_date_created' =>   array('default'=>null),
        'f_min_ratio' => array('default'=>null),
        'f_max_ratio' => array('default'=>null),
        'f_max_level' => array('default'=>null),
        ),
      'retrieves a list of derivatives to build'
    );

  $service->addMethod('pwg.images.addComment', 'ws_images_addComment',
      array(
        'image_id' => array(),
        'author' =>   array('default'=>is_a_guest()?'guest':$user['username']),
        'content' =>  array(),
        'key' =>      array(),
        ),
      'add a comment to an image'
    );

  $service->addMethod('pwg.images.getInfo', 'ws_images_getInfo',
      array(
        'image_id' =>           array(),
        'comments_page' =>      array('default'=>0 ),
        'comments_per_page' =>  array('default' =>  $conf['nb_comment_page'],
                                      'maxValue' => 2*$conf['nb_comment_page']),
        ),
      'retrieves information about the given photo'
    );

  $service->addMethod('pwg.images.rate', 'ws_images_rate',
      array('image_id', 'rate'),
      'rate the image'
    );

  $service->addMethod('pwg.images.search', 'ws_images_search',
      array(
        'query' =>      array(),
        'per_page' =>   array('default'=>100, 
                              'maxValue'=>$conf['ws_max_images_per_page']),
        'page' =>       array('default'=>0),
        'order' =>      array('default'=>null),
        'f_min_rate' => array('default'=>null),
        'f_max_rate' => array('default'=>null),
        'f_min_hit' =>  array('default'=>null),
        'f_max_hit' =>  array('default'=>null),
        'f_min_date_available' => array('default'=>null),
        'f_max_date_available' => array('default'=>null),
        'f_min_date_created' =>   array('default'=>null),
        'f_max_date_created' =>   array('default'=>null),
        'f_min_ratio' => array('default'=>null),
        'f_max_ratio' => array('default'=>null),
        'f_max_level' => array('default'=>null),
        ),
      'Returns elements for the corresponding query search.'
    );

  $service->addMethod('pwg.images.setPrivacyLevel', 'ws_images_setPrivacyLevel',
      array(
        'image_id' => array('flags'=>WS_PARAM_FORCE_ARRAY),
        'level' =>    array('maxValue'=>$conf['available_permission_levels']),
        ),
      'sets the privacy levels for the images (POST method only)'
    );

  $service->addMethod('pwg.images.setRank', 'ws_images_setRank',
      array('image_id', 'category_id', 'rank'),
      'sets the rank of a photo for a given album (POST method only, for admins)'
    );

  $service->addMethod('pwg.rates.delete', 'ws_rates_delete',
      array(
        'user_id' =>      array(),
        'anonymous_id' => array('default'=>null),
        ),
      'deletes all rates for a user (POST method only, admins only)'
    );

  $service->addMethod('pwg.session.getStatus', 'ws_session_getStatus', null, '');

  $service->addMethod('pwg.session.login', 'ws_session_login',
      array('username', 'password'),
      'POST method only'
    );

  $service->addMethod('pwg.session.logout', 'ws_session_logout', null, '');

  $service->addMethod('pwg.tags.getList', 'ws_tags_getList',
      array(
        'sort_by_counter' => array('default' =>false),
        ),
      'retrieves a list of available tags'
    );

  $service->addMethod('pwg.tags.getImages', 'ws_tags_getImages',
      array(
        'tag_id' =>       array('default'=>null,
                                'flags'=>WS_PARAM_FORCE_ARRAY),
        'tag_url_name' => array('default'=>null,
                                'flags'=>WS_PARAM_FORCE_ARRAY),
        'tag_name' =>     array('default'=>null,
                                'flags'=>WS_PARAM_FORCE_ARRAY),
        'tag_mode_and' => array('default'=>false),
        'per_page' =>     array('default'=>100,
                                'maxValue'=>$conf['ws_max_images_per_page']),
        'page' =>         array('default'=>0),
        'order' =>        array('default'=>null),
        'f_min_rate' =>   array('default'=>null),
        'f_max_rate' =>   array('default'=>null),
        'f_min_hit' =>    array('default'=>null),
        'f_max_hit' =>    array('default'=>null),
        'f_min_date_available' => array('default'=>null),
        'f_max_date_available' => array('default'=>null),
        'f_min_date_created' =>   array('default'=>null),
        'f_max_date_created' =>   array('default'=>null),
        'f_min_ratio' => array('default'=>null),
        'f_max_ratio' => array('default'=>null),
        'f_max_level' => array('default'=>null),
        ),
      'Returns elements for the corresponding tags. Note that tag_id, tag_url_name, tag_name an be arrays. Fill at least one of them. '
    );

  $service->addMethod('pwg.images.addChunk', 'ws_images_add_chunk',
      array('data', 'original_sum', 'type', 'position'),
      'POST method only. For admin only.'
    );

  $service->addMethod('pwg.images.addFile', 'ws_images_addFile',
      array('image_id', 'type', 'sum'),
      'Add or update a file for an existing photo. pwg.images.addChunk must have been called  before (maybe several times)'
    );


  $service->addMethod('pwg.images.add', 'ws_images_add',
      array(
        'file_sum' =>           array(),
        'thumbnail_sum' =>      array('default'=>null),
        'high_sum' =>           array('default'=>null),
        'original_sum' =>       array(),
        'original_filename' =>  array('default'=>null),
        'name' =>               array('default'=>null),
        'author' =>             array('default'=>null),
        'date_creation' =>      array('default'=>null),
        'comment' =>            array('default'=>null),
        'categories' =>         array('default'=>null),
        'tag_ids' =>            array('default'=>null),
        'level' =>              array('default'=>0,
                                      'maxValue'=>$conf['available_permission_levels']),
        'check_uniqueness' =>   array('default'=>true),
        'image_id' =>           array('default'=>null),
        ),
      'POST method only.
<br><b>categories</b> is a string list "category_id[,rank];category_id[,rank]"
The rank is optional and is equivalent to "auto" if not given.'
    );

  $service->addMethod('pwg.images.addSimple', 'ws_images_addSimple',
      array(
        'category' => array('default'=>null),
        'name' =>     array('default'=>null),
        'author' =>   array('default'=>null),
        'comment' =>  array('default'=>null),
        'level' =>    array('default'=>0,
                            'maxValue'=>$conf['available_permission_levels']),
        'tags' =>     array('default'=>null,
                            'flags'=>WS_PARAM_ACCEPT_ARRAY),
        'image_id' => array('default'=>null),
        ),
      'POST method only.<br>Use the <b>image</b> field for uploading file.
<br>Set the form encoding to "form-data"<br><b>category</b> is the numeric identifier of the destination category.
<br>You can update an existing photo if you define an existing image_id.'
    );

  $service->addMethod('pwg.images.delete', 'ws_images_delete',
      array(
        'image_id' =>   array('default'=>0),
        'pwg_token' =>  array(),
        ),
      'Delete photos. You can give several image_ids, comma separated'
    );

  $service->addMethod('pwg.categories.getAdminList', 'ws_categories_getAdminList',
      null,
      'administration method only'
    );

  $service->addMethod('pwg.categories.add', 'ws_categories_add',
      array(
        'name' =>         array(),
        'parent' =>       array('default'=>null),
        'comment' =>      array('default'=>null),
        'visible' =>      array('default'=>null),
        'status' =>       array('default'=>null),
        'commentable' =>  array('default'=>'true'),
        ),
      'administration method only'
    );

  $service->addMethod('pwg.categories.delete', 'ws_categories_delete',
      array(
        'category_id'=>           array('default'=>0),
        'pwg_token' =>            array(),
        'photo_deletion_mode' =>  array('default'=>'delete_orphans'),
        ),
      'Delete categories. You can give several category_ids, comma separated.
<br><b>photo_deletion_mode</b> can be "no_delete" (may create orphan photos), "delete_orphans"
(default mode, only deletes photos linked to no other album) or "force_delete" (delete all photos, even those linked to other albums)'
    );

  $service->addMethod('pwg.categories.move', 'ws_categories_move',
      array(
        'category_id' =>  array('default'=>0),
        'parent' =>       array('default'=>0),
        'pwg_token' =>    array(),
        ),
      'Move categories. You can give several category_ids, comma separated. Set parent as 0 to move to gallery root. Only virtual categories can be moved.'
    );

  $service->addMethod('pwg.categories.setRepresentative', 'ws_categories_setRepresentative',
      array(
        'category_id' =>  array('default'=>0),
        'image_id' =>     array('default'=>0),
        ),
      'Set the representative photo for an album. The photo doesn\'t have to belong to the album. POST method only. Administration method only.'
    );

  $service->addMethod('pwg.tags.getAdminList', 'ws_tags_getAdminList',
      null,
      'administration method only'
    );

  $service->addMethod('pwg.tags.add', 'ws_tags_add',
      array('name'),
      'administration method only'
    );

  $service->addMethod('pwg.images.exist', 'ws_images_exist',
      array(
        'md5sum_list' =>    array('default'=>null),
        'filename_list' =>  array('default'=>null),
        ),
      'check existence of a photo list'
    );

  $service->addMethod('pwg.images.checkFiles', 'ws_images_checkFiles',
      array(
        'image_id' =>       array(),
        'thumbnail_sum' =>  array('default'=>null),
        'file_sum' =>       array('default'=>null),
        'high_sum' =>       array('default'=>null),
        ),
      'check if you have updated version of your files for a given photo, for each requested file type, the answer can be "missing", "equals" or "differs"'
    );

  $service->addMethod('pwg.images.checkUpload', 'ws_images_checkUpload',
      null,
      'check if Piwigo is ready for upload'
    );

  $service->addMethod('pwg.images.setInfo', 'ws_images_setInfo',
      array(
        'image_id' =>       array(),
        'file' =>           array('default'=>null),
        'name' =>           array('default'=>null),
        'author' =>         array('default'=>null),
        'date_creation' =>  array('default'=>null),
        'comment' =>        array('default'=>null),
        'categories' =>     array('default'=>null),
        'tag_ids' =>        array('default'=>null),
        'level' =>          array('default'=>null,
                                  'maxValue'=>$conf['available_permission_levels']),
        'single_value_mode' =>    array('default'=>'fill_if_empty'),
        'multiple_value_mode' =>  array('default'=>'append'),
        ),
      'POST method only. Admin only
<br><b>categories</b> is a string list "category_id[,rank];category_id[,rank]" The rank is optional and is equivalent to "auto" if not given.
<br><b>single_value_mode</b> can be "fill_if_empty" (only use the input value if the corresponding values is currently empty) or "replace"
(overwrite any existing value) and applies to single values properties like name/author/date_creation/comment
<br><b>multiple_value_mode</b> can be "append" (no change on existing values, add the new values) or "replace" and applies to multiple values properties like tag_ids/categories'
    );

  $service->addMethod('pwg.categories.setInfo', 'ws_categories_setInfo',
      array(
        'category_id' =>  array(),
        'name' =>         array('default'=>null),
        'comment' =>      array('default'=>null),
        ),
      'POST method only.'
    );
  
  $service->addMethod('pwg.plugins.getList', 'ws_plugins_getList',
      null,
      'Admin only
<br>get the list of plugin with id, name, version, state and description'
    );

  $service->addMethod('pwg.plugins.performAction', 'ws_plugins_performAction',
      array('action', 'plugin', 'pwg_token'),
      'Admin only
<br>install/activate/deactivate/uninstall/delete a plugin'
    );

  $service->addMethod('pwg.themes.performAction', 'ws_themes_performAction',
      array('action', 'theme', 'pwg_token'),
      'activate/deactivate/delete/set_default a theme<br>administration status required'
    );

  $service->addMethod('pwg.extensions.update', 'ws_extensions_update',
      array('type', 'id', 'revision', 'pwg_token'),
      'Update an extension. Webmaster only.
<br>Parameter type must be "plugins", "languages" or "themes".'
  );

  $service->addMethod('pwg.extensions.ignoreUpdate', 'ws_extensions_ignoreupdate',
      array(
        'type' =>       array('default'=>null),
        'id' =>         array('default'=>null),
        'reset' =>      array('default'=>null),
        'pwg_token' =>  array(),
      ),
      'Ignore an extension if it need update.
<br>Parameter type must be "plugins", "languages" or "themes".
<br>If reset parameter is true, all ignored extensions will be reinitilized.'
  );

  $service->addMethod('pwg.extensions.checkUpdates', 'ws_extensions_checkupdates',
      null,
      'Check if piwigo or extensions are up to date.'
  );
}

?>
