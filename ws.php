<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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
define ('IN_WS', true);

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
check_status(ACCESS_FREE);

if ( !$conf['allow_web_services'] )
{
  page_forbidden('Web services are disabled');
}

include_once(PHPWG_ROOT_PATH.'include/ws_core.inc.php');

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
  $ws_functions_root = PHPWG_ROOT_PATH.'include/ws_functions/';
  
  $f_params = array(
    'f_min_rate' => array('default'=>null,
                          'type'=>WS_TYPE_FLOAT),
    'f_max_rate' => array('default'=>null,
                          'type'=>WS_TYPE_FLOAT),
    'f_min_hit' =>  array('default'=>null,
                          'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
    'f_max_hit' =>  array('default'=>null,
                          'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
    'f_min_ratio' => array('default'=>null,
                           'type'=>WS_TYPE_FLOAT|WS_TYPE_POSITIVE),
    'f_max_ratio' => array('default'=>null,
                           'type'=>WS_TYPE_FLOAT|WS_TYPE_POSITIVE),
    'f_max_level' => array('default'=>null,
                           'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
    'f_min_date_available' => array('default'=>null),
    'f_max_date_available' => array('default'=>null),
    'f_min_date_created' =>   array('default'=>null),
    'f_max_date_created' =>   array('default'=>null),
    );
  
  $service->addMethod(
      'pwg.getVersion',
      'ws_getVersion',
      null,
      'Returns the Piwigo version.',
      $ws_functions_root . 'pwg.php'
    );
	  
  $service->addMethod(
      'pwg.getInfos',
      'ws_getInfos',
      null,
      'Returns general informations.',
      $ws_functions_root . 'pwg.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.caddie.add',
      'ws_caddie_add',
      array(
        'image_id'=> array('flags'=>WS_PARAM_FORCE_ARRAY,
                           'type'=>WS_TYPE_ID),
        ),
      'Adds elements to the caddie. Returns the number of elements added.',
      $ws_functions_root . 'pwg.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.categories.getImages',
      'ws_categories_getImages',
      array_merge(array(
        'cat_id' =>     array('default'=>null, 
                              'flags'=>WS_PARAM_FORCE_ARRAY,
                              'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'recursive' =>  array('default'=>false,
                              'type'=>WS_TYPE_BOOL),
        'per_page' =>   array('default'=>100,
                              'maxValue'=>$conf['ws_max_images_per_page'],
                              'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'page' =>       array('default'=>0,
                              'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'order' =>      array('default'=>null,
                              'info'=>'id, file, name, hit, rating_score, date_creation, date_available, random'),
        ), $f_params),
      'Returns elements for the corresponding categories.
<br><b>cat_id</b> can be empty if <b>recursive</b> is true.
<br><b>order</b> comma separated fields for sorting',
      $ws_functions_root . 'pwg.categories.php'
    );

  $service->addMethod(
      'pwg.categories.getList',
      'ws_categories_getList',
      array(
        'cat_id' =>       array('default'=>null,
                                'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE,
                                'info'=>'Parent category. "0" or empty for root.'),
        'recursive' =>    array('default'=>false,
                                'type'=>WS_TYPE_BOOL),
        'public' =>       array('default'=>false,
                                'type'=>WS_TYPE_BOOL),
        'tree_output' =>  array('default'=>false,
                                'type'=>WS_TYPE_BOOL),
        'fullname' =>     array('default'=>false,
                                'type'=>WS_TYPE_BOOL),
        'thumbnail_size' => array(
          'default' => IMG_THUMB,
          'info' => implode(',', array_keys(ImageStdParams::get_defined_type_map()))
          ),
        ),
      'Returns a list of categories.',
      $ws_functions_root . 'pwg.categories.php'
    );

  $service->addMethod(
      'pwg.getMissingDerivatives',
      'ws_getMissingDerivatives',
      array_merge(array(
        'types' =>        array('default'=>null,
                                'flags'=>WS_PARAM_FORCE_ARRAY,
                                'info'=>'square, thumb, 2small, xsmall, small, medium, large, xlarge, xxlarge'),
        'ids' =>          array('default'=>null,
                                'flags'=>WS_PARAM_FORCE_ARRAY,
                                'type'=>WS_TYPE_ID),
        'max_urls' =>     array('default'=>200,
                                'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'prev_page' =>    array('default'=>null,
                                'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        ), $f_params),
      'Returns a list of derivatives to build.',
      $ws_functions_root . 'pwg.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.images.addComment',
      'ws_images_addComment',
      array(
        'image_id' => array('type'=>WS_TYPE_ID),
        'author' =>   array('default'=>is_a_guest()?'guest':$user['username']),
        'content' =>  array(),
        'key' =>      array(),
        ),
      'Adds a comment to an image.',
      $ws_functions_root . 'pwg.images.php',
      array('post_only'=>true)
    );

  $service->addMethod(
      'pwg.images.getInfo',
      'ws_images_getInfo',
      array(
        'image_id' =>           array('type'=>WS_TYPE_ID),
        'comments_page' =>      array('default'=>0,
                                      'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'comments_per_page' =>  array('default'=>$conf['nb_comment_page'],
                                      'maxValue'=>2*$conf['nb_comment_page'],
                                      'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        ),
      'Returns information about an image.',
      $ws_functions_root . 'pwg.images.php'
    );

  $service->addMethod(
      'pwg.images.rate',
      'ws_images_rate',
      array(
        'image_id' => array('type'=>WS_TYPE_ID),
        'rate' =>     array('type'=>WS_TYPE_FLOAT),
      ),
      'Rates an image.',
      $ws_functions_root . 'pwg.images.php'
    );

  $service->addMethod(
      'pwg.images.search',
      'ws_images_search',
      array_merge(array(
        'query' =>        array(),
        'per_page' =>     array('default'=>100,
                                'maxValue'=>$conf['ws_max_images_per_page'],
                                'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'page' =>         array('default'=>0,
                                'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'order' =>        array('default'=>null,
                                'info'=>'id, file, name, hit, rating_score, date_creation, date_available, random'),
        ), $f_params),
      'Returns elements for the corresponding query search.',
      $ws_functions_root . 'pwg.images.php'
    );

  $service->addMethod(
      'pwg.images.setPrivacyLevel',
      'ws_images_setPrivacyLevel',
      array(
        'image_id' => array('flags'=>WS_PARAM_FORCE_ARRAY,
                            'type'=>WS_TYPE_ID),
        'level' =>    array('maxValue'=>max($conf['available_permission_levels']),
                            'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        ),
      'Sets the privacy levels for the images.',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.images.setRank',
      'ws_images_setRank',
      array(
        'image_id'    => array('type'=>WS_TYPE_ID,'flags'=>WS_PARAM_FORCE_ARRAY),
        'category_id' => array('type'=>WS_TYPE_ID),
        'rank'        => array('type'=>WS_TYPE_INT|WS_TYPE_POSITIVE|WS_TYPE_NOTNULL, 'default'=>null)
        ),
      'Sets the rank of a photo for a given album.
<br><br>If you provide a list for image_id:
<ul>
<li>rank becomes useless, only the order of the image_id list matters</li>
<li>you are supposed to provide the list of all image_ids belonging to the album.
</ul>',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.rates.delete',
      'ws_rates_delete',
      array(
        'user_id' =>      array('type'=>WS_TYPE_ID),
        'anonymous_id' => array('default'=>null),
        'image_id' =>     array('flags'=>WS_PARAM_OPTIONAL, 'type'=>WS_TYPE_ID),
        ),
      'Deletes all rates for a user.',
      $ws_functions_root . 'pwg.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.session.getStatus',
      'ws_session_getStatus',
      null,
      'Gets information about the current session. Also provides a token useable with admin methods.',
      $ws_functions_root . 'pwg.php'
    );

  $service->addMethod(
      'pwg.session.login',
      'ws_session_login',
      array('username', 'password'),
      'Tries to login the user.',
      $ws_functions_root . 'pwg.php',
      array('post_only'=>true)
    );

  $service->addMethod(
      'pwg.session.logout',
      'ws_session_logout',
      null,
      'Ends the current session.',
      $ws_functions_root . 'pwg.php'
    );

  $service->addMethod(
      'pwg.tags.getList',
      'ws_tags_getList',
      array(
        'sort_by_counter' => array('default'=>false,
                                   'type'=>WS_TYPE_BOOL),
        ),
      'Retrieves a list of available tags.',
      $ws_functions_root . 'pwg.tags.php'
    );

  $service->addMethod(
      'pwg.tags.getImages',
      'ws_tags_getImages',
      array_merge(array(
        'tag_id' =>       array('default'=>null,
                                'flags'=>WS_PARAM_FORCE_ARRAY,
                                'type'=>WS_TYPE_ID),
        'tag_url_name' => array('default'=>null,
                                'flags'=>WS_PARAM_FORCE_ARRAY),
        'tag_name' =>     array('default'=>null,
                                'flags'=>WS_PARAM_FORCE_ARRAY),
        'tag_mode_and' => array('default'=>false,
                                'type'=>WS_TYPE_BOOL),
        'per_page' =>     array('default'=>100,
                                'maxValue'=>$conf['ws_max_images_per_page'],
                                'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'page' =>         array('default'=>0,
                                'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'order' =>        array('default'=>null,
                                'info'=>'id, file, name, hit, rating_score, date_creation, date_available, random'),
        ), $f_params),
      'Returns elements for the corresponding tags. Fill at least tag_id, tag_url_name or tag_name.',
      $ws_functions_root . 'pwg.tags.php'
    );

  $service->addMethod(
      'pwg.images.addChunk',
      'ws_images_add_chunk',
      array(
        'data' =>         array(),
        'original_sum' => array(),
        'type' =>         array('default'=>'file',
                                'info'=>'Must be "file", for backward compatiblity "high" and "thumb" are allowed.'),
        'position' =>     array()
        ),
      'Add a chunk of a file.',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.images.addFile',
      'ws_images_addFile',
      array(
        'image_id' => array('type'=>WS_TYPE_ID),
        'type' =>     array('default'=>'file',
                            'info'=>'Must be "file", for backward compatiblity "high" and "thumb" are allowed.'),
        'sum' =>      array(),
        ),
      'Add or update a file for an existing photo.
<br>pwg.images.addChunk must have been called before (maybe several times).',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true)
    );


  $service->addMethod(
      'pwg.images.add',
      'ws_images_add',
      array(
        'thumbnail_sum' =>      array('default'=>null),
        'high_sum' =>           array('default'=>null),
        'original_sum' =>       array(),
        'original_filename' =>  array('default'=>null,
                                      'Provide it if "check_uniqueness" is true and $conf["uniqueness_mode"] is "filename".'),
        'name' =>               array('default'=>null),
        'author' =>             array('default'=>null),
        'date_creation' =>      array('default'=>null),
        'comment' =>            array('default'=>null),
        'categories' =>         array('default'=>null,
                                      'info'=>'String list "category_id[,rank];category_id[,rank]".<br>The rank is optional and is equivalent to "auto" if not given.'),
        'tag_ids' =>            array('default'=>null,
                                      'info'=>'Comma separated ids'),
        'level' =>              array('default'=>0,
                                      'maxValue'=>max($conf['available_permission_levels']),
                                      'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'check_uniqueness' =>   array('default'=>true,
                                      'type'=>WS_TYPE_BOOL),
        'image_id' =>           array('default'=>null,
                                      'type'=>WS_TYPE_ID),
        ),
      'Add an image.
<br>pwg.images.addChunk must have been called before (maybe several times).
<br>Don\'t use "thumbnail_sum" and "high_sum", these parameters are here for backward compatibility.',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.images.addSimple',
      'ws_images_addSimple',
      array(
        'category' => array('default'=>null,
                            'flags'=>WS_PARAM_FORCE_ARRAY,
                            'type'=>WS_TYPE_ID),
        'name' =>     array('default'=>null),
        'author' =>   array('default'=>null),
        'comment' =>  array('default'=>null),
        'level' =>    array('default'=>0,
                            'maxValue'=>max($conf['available_permission_levels']),
                            'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'tags' =>     array('default'=>null,
                            'flags'=>WS_PARAM_ACCEPT_ARRAY),
        'image_id' => array('default'=>null,
                            'type'=>WS_TYPE_ID),
        ),
      'Add an image.
<br>Use the <b>$_FILES[image]</b> field for uploading file.
<br>Set the form encoding to "form-data".
<br>You can update an existing photo if you define an existing image_id.',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.images.upload',
      'ws_images_upload',
      array(
        'name' => array('default' => null),
        'category' => array(
          'default'=>null,
          'flags'=>WS_PARAM_FORCE_ARRAY,
          'type'=>WS_TYPE_ID
          ),
        'level' => array(
          'default' => 0,
          'maxValue' => max($conf['available_permission_levels']),
          'type' => WS_TYPE_INT|WS_TYPE_POSITIVE
          ),
        'pwg_token' => array(),
        ),
      'Add an image.
<br>Use the <b>$_FILES[image]</b> field for uploading file.
<br>Set the form encoding to "form-data".',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true, 'post_only'=>true)
    );
  
  $service->addMethod(
      'pwg.images.delete',
      'ws_images_delete',
      array(
        'image_id' =>   array('flags'=>WS_PARAM_ACCEPT_ARRAY),
        'pwg_token' =>  array(),
        ),
      'Deletes image(s).',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.categories.getAdminList',
      'ws_categories_getAdminList',
      null,
      'Get albums list as displayed on admin page.',
      $ws_functions_root . 'pwg.categories.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.categories.add',
      'ws_categories_add',
      array(
        'name' =>         array(),
        'parent' =>       array('default'=>null,
                                'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'comment' =>      array('default'=>null),
        'visible' =>      array('default'=>true,
                                'type'=>WS_TYPE_BOOL),
        'status' =>       array('default'=>null,
                                'info'=>'public, private'),
        'commentable' =>  array('default'=>true,
                                'type'=>WS_TYPE_BOOL),
        ),
      'Adds an album.',
      $ws_functions_root . 'pwg.categories.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.categories.delete',
      'ws_categories_delete',
      array(
        'category_id'=>           array('flags'=>WS_PARAM_ACCEPT_ARRAY),
        'photo_deletion_mode' =>  array('default'=>'delete_orphans'),
        'pwg_token' =>            array(),
        ),
      'Deletes album(s).
<br><b>photo_deletion_mode</b> can be "no_delete" (may create orphan photos), "delete_orphans"
(default mode, only deletes photos linked to no other album) or "force_delete" (delete all photos, even those linked to other albums)',
      $ws_functions_root . 'pwg.categories.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.categories.move',
      'ws_categories_move',
      array(
        'category_id' =>  array('flags'=>WS_PARAM_ACCEPT_ARRAY),
        'parent' =>       array('type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'pwg_token' =>    array(),
        ),
      'Move album(s).
<br>Set parent as 0 to move to gallery root. Only virtual categories can be moved.',
      $ws_functions_root . 'pwg.categories.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.categories.setRepresentative',
      'ws_categories_setRepresentative',
      array(
        'category_id' =>  array('type'=>WS_TYPE_ID),
        'image_id' =>     array('type'=>WS_TYPE_ID),
        ),
      'Sets the representative photo for an album. The photo doesn\'t have to belong to the album.',
      $ws_functions_root . 'pwg.categories.php',
      array('admin_only'=>true, 'post_only'=>true)
    );
  
  $service->addMethod(
      'pwg.categories.deleteRepresentative',
      'ws_categories_deleteRepresentative',
      array(
        'category_id' =>  array('type'=>WS_TYPE_ID),
        ),
      'Deletes the album thumbnail. Only possible if $conf[\'allow_random_representative\']',
      $ws_functions_root . 'pwg.categories.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.categories.refreshRepresentative',
      'ws_categories_refreshRepresentative',
      array(
        'category_id' =>  array('type'=>WS_TYPE_ID),
        ),
      'Find a new album thumbnail.',
      $ws_functions_root . 'pwg.categories.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.tags.getAdminList',
      'ws_tags_getAdminList',
      null,
      '<b>Admin only.</b>',
      $ws_functions_root . 'pwg.tags.php',
      array('admin_only'=>true)
    );

  $service->addMethod( // TODO: create multiple tags
      'pwg.tags.add',
      'ws_tags_add',
      array('name'),
      'Adds a new tag.',
      $ws_functions_root . 'pwg.tags.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.images.exist',
      'ws_images_exist',
      array(
        'md5sum_list' =>    array('default'=>null),
        'filename_list' =>  array('default'=>null),
        ),
      'Checks existence of images.
<br>Give <b>md5sum_list</b> if $conf[uniqueness_mode]==md5sum. Give <b>filename_list</b> if $conf[uniqueness_mode]==filename.',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.images.checkFiles',
      'ws_images_checkFiles',
      array(
        'image_id' =>       array('type'=>WS_TYPE_ID),
        'file_sum' =>       array('default'=>null),
        'thumbnail_sum' =>  array('default'=>null),
        'high_sum' =>       array('default'=>null),
        ),
      'Checks if you have updated version of your files for a given photo, the answer can be "missing", "equals" or "differs".
<br>Don\'t use "thumbnail_sum" and "high_sum", these parameters are here for backward compatibility.',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.images.checkUpload',
      'ws_images_checkUpload',
      null,
      'Checks if Piwigo is ready for upload.',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.images.setInfo',
      'ws_images_setInfo',
      array(
        'image_id' =>       array('type'=>WS_TYPE_ID),
        'file' =>           array('default'=>null),
        'name' =>           array('default'=>null),
        'author' =>         array('default'=>null),
        'date_creation' =>  array('default'=>null),
        'comment' =>        array('default'=>null),
        'categories' =>     array('default'=>null,
                                  'info'=>'String list "category_id[,rank];category_id[,rank]".<br>The rank is optional and is equivalent to "auto" if not given.'),
        'tag_ids' =>        array('default'=>null,
                                  'info'=>'Comma separated ids'),
        'level' =>          array('default'=>null,
                                  'maxValue'=>max($conf['available_permission_levels']),
                                  'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'single_value_mode' =>    array('default'=>'fill_if_empty'),
        'multiple_value_mode' =>  array('default'=>'append'),
        ),
      'Changes properties of an image.
<br><b>single_value_mode</b> can be "fill_if_empty" (only use the input value if the corresponding values is currently empty) or "replace"
(overwrite any existing value) and applies to single values properties like name/author/date_creation/comment.
<br><b>multiple_value_mode</b> can be "append" (no change on existing values, add the new values) or "replace" and applies to multiple values properties like tag_ids/categories.',
      $ws_functions_root . 'pwg.images.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.categories.setInfo',
      'ws_categories_setInfo',
      array(
        'category_id' =>  array('type'=>WS_TYPE_ID),
        'name' =>         array('default'=>null),
        'comment' =>      array('default'=>null),
        'status' =>       array('default'=>null,
                                'info'=>'public, private'),
        ),
      'Changes properties of an album.',
      $ws_functions_root . 'pwg.categories.php',
      array('admin_only'=>true, 'post_only'=>true)
    );
  
  $service->addMethod(
      'pwg.plugins.getList',
      'ws_plugins_getList',
      null,
      'Gets the list of plugins with id, name, version, state and description.',
      $ws_functions_root . 'pwg.extensions.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.plugins.performAction',
      'ws_plugins_performAction',
      array(
        'action'    => array('info'=>'install, activate, deactivate, uninstall, delete'),
        'plugin'    => array(),
        'pwg_token' => array(),
        ),
      null,
      $ws_functions_root . 'pwg.extensions.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.themes.performAction',
      'ws_themes_performAction',
      array(
        'action'    => array('info'=>'activate, deactivate, delete, set_default'),
        'theme'     => array(),
        'pwg_token' => array(),
        ),
      null,
      $ws_functions_root . 'pwg.extensions.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.extensions.update',
      'ws_extensions_update',
      array(
        'type' => array('info'=>'plugins, languages, themes'),
        'id' => array(),
        'revision' => array(),
        'pwg_token' => array(),
        ),
      '<b>Webmaster only.</b>',
      $ws_functions_root . 'pwg.extensions.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.extensions.ignoreUpdate',
      'ws_extensions_ignoreupdate',
      array(
        'type' =>       array('default'=>null,
                              'info'=>'plugins, languages, themes'),
        'id' =>         array('default'=>null),
        'reset' =>      array('default'=>false,
                              'type'=>WS_TYPE_BOOL,
                              'info'=>'If true, all ignored extensions will be reinitilized.'),
        'pwg_token' =>  array(),
      ),
      '<b>Webmaster only.</b> Ignores an extension if it needs update.',
      $ws_functions_root . 'pwg.extensions.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.extensions.checkUpdates',
      'ws_extensions_checkupdates',
      null,
      'Checks if piwigo or extensions are up to date.',
      $ws_functions_root . 'pwg.extensions.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.groups.getList',
      'ws_groups_getList',
      array(
        'group_id' => array('flags'=>WS_PARAM_OPTIONAL|WS_PARAM_FORCE_ARRAY,
                            'type'=>WS_TYPE_ID),
        'name' =>     array('flags'=>WS_PARAM_OPTIONAL,
                            'info'=>'Use "%" as wildcard.'),
        'per_page' => array('default'=>100,
                            'maxValue'=>$conf['ws_max_users_per_page'],
                            'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'page' =>     array('default'=>0,
                            'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'order' =>    array('default'=>'name',
                            'info'=>'id, name, nb_users, is_default'),
        ),
      'Retrieves a list of all groups. The list can be filtered.',
      $ws_functions_root . 'pwg.groups.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.groups.add',
      'ws_groups_add',
      array(
        'name' =>       array(),
        'is_default' => array('default'=>false,
                              'type'=>WS_TYPE_BOOL),
        ),
      'Creates a group and returns the new group record.',
      $ws_functions_root . 'pwg.groups.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.groups.delete',
      'ws_groups_delete',
      array(
        'group_id' => array('flags'=>WS_PARAM_FORCE_ARRAY,
                            'type'=>WS_TYPE_ID),
        'pwg_token' =>  array(),
        ),
      'Deletes a or more groups. Users and photos are not deleted.',
      $ws_functions_root . 'pwg.groups.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.groups.setInfo',
      'ws_groups_setInfo',
      array(
        'group_id' =>   array('type'=>WS_TYPE_ID),
        'name' =>       array('flags'=>WS_PARAM_OPTIONAL),
        'is_default' => array('flags'=>WS_PARAM_OPTIONAL,
                              'type'=>WS_TYPE_BOOL),
        'pwg_token' => array(),
        ),
      'Updates a group. Leave a field blank to keep the current value.',
      $ws_functions_root . 'pwg.groups.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.groups.addUser',
      'ws_groups_addUser',
      array(
        'group_id' => array('type'=>WS_TYPE_ID),
        'user_id' =>  array('flags'=>WS_PARAM_FORCE_ARRAY,
                            'type'=>WS_TYPE_ID),
        'pwg_token' => array(),
        ),
      'Adds one or more users to a group.',
      $ws_functions_root . 'pwg.groups.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.groups.deleteUser',
      'ws_groups_deleteUser',
      array(
        'group_id' => array('type'=>WS_TYPE_ID),
        'user_id' =>  array('flags'=>WS_PARAM_FORCE_ARRAY,
                            'type'=>WS_TYPE_ID),
        'pwg_token' => array(),
        ),
      'Removes one or more users from a group.',
      $ws_functions_root . 'pwg.groups.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.users.getList',
      'ws_users_getList',
      array(
        'user_id' =>    array('flags'=>WS_PARAM_OPTIONAL|WS_PARAM_FORCE_ARRAY,
                              'type'=>WS_TYPE_ID),
        'username' =>   array('flags'=>WS_PARAM_OPTIONAL,
                              'info'=>'Use "%" as wildcard.'),
        'status' =>     array('flags'=>WS_PARAM_OPTIONAL|WS_PARAM_FORCE_ARRAY,
                              'info'=>'guest,generic,normal,admin,webmaster'),
        'min_level' =>  array('default'=>0,
                              'maxValue'=>max($conf['available_permission_levels']),
                              'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'group_id' =>   array('flags'=>WS_PARAM_OPTIONAL|WS_PARAM_FORCE_ARRAY,
                              'type'=>WS_TYPE_ID),
        'per_page' =>   array('default'=>100,
                              'maxValue'=>$conf['ws_max_users_per_page'],
                              'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'page' =>       array('default'=>0,
                              'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'order' =>      array('default'=>'id',
                              'info'=>'id, username, level, email'),
        'display' =>    array('default'=>'basics',
                              'info'=>'Comma saparated list (see method description)'),
        ),
      'Retrieves a list of all the users.<br>
<br>
<b>display</b> controls which data are returned, possible values are:<br>
all, basics, none,<br>
username, email, status, level, groups,<br>
language, theme, nb_image_page, recent_period, expand, show_nb_comments, show_nb_hits,<br>
enabled_high, registration_date, registration_date_string, registration_date_since, last_visit, last_visit_string, last_visit_since<br>
<b>basics</b> stands for "username,email,status,level,groups"',
      $ws_functions_root . 'pwg.users.php',
      array('admin_only'=>true)
    );

  $service->addMethod(
      'pwg.users.add',
      'ws_users_add',
      array(
        'username' => array(),
        'password' => array('default'=>null),
        'password_confirm' => array('flags'=>WS_PARAM_OPTIONAL),
        'email' =>    array('default'=>null),
        'send_password_by_mail' => array('default'=>false, 'type'=>WS_TYPE_BOOL),
        'pwg_token' => array(),
        ),
      'Registers a new user.',
      $ws_functions_root . 'pwg.users.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.users.delete',
      'ws_users_delete',
      array(
        'user_id' =>  array('flags'=>WS_PARAM_FORCE_ARRAY,
                            'type'=>WS_TYPE_ID),
        'pwg_token' =>  array(),
        ),
      'Deletes on or more users. Photos owned by this user are not deleted.',
      $ws_functions_root . 'pwg.users.php',
      array('admin_only'=>true, 'post_only'=>true)
    );

  $service->addMethod(
      'pwg.users.setInfo',
      'ws_users_setInfo',
      array(
        'user_id' =>          array('flags'=>WS_PARAM_FORCE_ARRAY,
                                    'type'=>WS_TYPE_ID),
        'username' =>         array('flags'=>WS_PARAM_OPTIONAL),
        'password' =>         array('flags'=>WS_PARAM_OPTIONAL),
        'email' =>            array('flags'=>WS_PARAM_OPTIONAL),
        'status' =>           array('flags'=>WS_PARAM_OPTIONAL,
                                    'info'=>'guest,generic,normal,admin,webmaster'),
        'level'=>             array('flags'=>WS_PARAM_OPTIONAL,
                                    'maxValue'=>max($conf['available_permission_levels']),
                                    'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'language' =>         array('flags'=>WS_PARAM_OPTIONAL),
        'theme' =>            array('flags'=>WS_PARAM_OPTIONAL),
        'group_id' => array('flags'=>WS_PARAM_OPTIONAL|WS_PARAM_FORCE_ARRAY, 'type'=>WS_TYPE_INT),
        // bellow are parameters removed in a future version
        'nb_image_page' =>    array('flags'=>WS_PARAM_OPTIONAL,
                                    'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE|WS_TYPE_NOTNULL),
        'recent_period' =>    array('flags'=>WS_PARAM_OPTIONAL,
                                    'type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
        'expand' =>           array('flags'=>WS_PARAM_OPTIONAL,
                                    'type'=>WS_TYPE_BOOL),
        'show_nb_comments' => array('flags'=>WS_PARAM_OPTIONAL,
                                    'type'=>WS_TYPE_BOOL),
        'show_nb_hits' =>     array('flags'=>WS_PARAM_OPTIONAL,
                                    'type'=>WS_TYPE_BOOL),
        'enabled_high' =>     array('flags'=>WS_PARAM_OPTIONAL,
                                    'type'=>WS_TYPE_BOOL),
        'pwg_token' => array(),
        ),
      'Updates a user. Leave a field blank to keep the current value.
<br>"username", "password" and "email" are ignored if "user_id" is an array.
<br>set "group_id" to -1 if you want to dissociate users from all groups',
      $ws_functions_root . 'pwg.users.php',
      array('admin_only'=>true, 'post_only'=>true)
    );
    
  $service->addMethod(
      'pwg.permissions.getList',
      'ws_permissions_getList',
      array(
        'cat_id' =>     array('flags'=>WS_PARAM_FORCE_ARRAY|WS_PARAM_OPTIONAL,
                              'type'=>WS_TYPE_ID),
        'group_id' =>   array('flags'=>WS_PARAM_FORCE_ARRAY|WS_PARAM_OPTIONAL,
                              'type'=>WS_TYPE_ID),
        'user_id' =>    array('flags'=>WS_PARAM_FORCE_ARRAY|WS_PARAM_OPTIONAL,
                              'type'=>WS_TYPE_ID),
        ),
      'Returns permissions: user ids and group ids having access to each album ; this list can be filtered.
<br>Provide only one parameter!',
      $ws_functions_root . 'pwg.permissions.php',
      array('admin_only'=>true)
    );
    
  $service->addMethod(
      'pwg.permissions.add',
      'ws_permissions_add',
      array(
        'cat_id' =>     array('flags'=>WS_PARAM_FORCE_ARRAY,
                              'type'=>WS_TYPE_ID),
        'group_id' =>   array('flags'=>WS_PARAM_FORCE_ARRAY|WS_PARAM_OPTIONAL,
                              'type'=>WS_TYPE_ID),
        'user_id' =>    array('flags'=>WS_PARAM_FORCE_ARRAY|WS_PARAM_OPTIONAL,
                              'type'=>WS_TYPE_ID),
        'recursive' =>  array('default'=>false,
                              'type'=>WS_TYPE_BOOL),
        'pwg_token' => array(),
        ),
      'Adds permissions to an album.',
      $ws_functions_root . 'pwg.permissions.php',
      array('admin_only'=>true, 'post_only'=>true)
    );
    
  $service->addMethod(
      'pwg.permissions.remove',
      'ws_permissions_remove',
      array(
        'cat_id' =>   array('flags'=>WS_PARAM_FORCE_ARRAY,
                            'type'=>WS_TYPE_ID),
        'group_id' => array('flags'=>WS_PARAM_FORCE_ARRAY|WS_PARAM_OPTIONAL,
                            'type'=>WS_TYPE_ID),
        'user_id' =>  array('flags'=>WS_PARAM_FORCE_ARRAY|WS_PARAM_OPTIONAL,
                            'type'=>WS_TYPE_ID),
        'pwg_token' => array(),
        ),
      'Removes permissions from an album.',
      $ws_functions_root . 'pwg.permissions.php',
      array('admin_only'=>true, 'post_only'=>true)
    );
}

?>
