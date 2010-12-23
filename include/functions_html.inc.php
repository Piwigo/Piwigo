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

/**
 * returns the list of categories as a HTML string
 *
 * categories string returned contains categories as given in the input
 * array $cat_informations. $cat_informations array must be an array
 * of array( id=>?, name=>?, permalink=>?). If url input parameter is null,
 * returns only the categories name without links.
 *
 * @param array cat_informations
 * @param string url
 * @param boolean replace_space
 * @return string
 */
function get_cat_display_name($cat_informations,
                              $url = '',
                              $replace_space = true)
{
  global $conf;

  //$output = '<a href="'.get_absolute_root_url().$conf['home_page'].'">'.l10n('Home').'</a>';
  $output = '';
  $is_first=true;

  foreach ($cat_informations as $cat)
  {
    is_array($cat) or trigger_error(
        'get_cat_display_name wrong type for category ', E_USER_WARNING
      );

    $cat['name'] = trigger_event(
      'render_category_name',
      $cat['name'],
      'get_cat_display_name'
      );

    if ($is_first)
    {
      $is_first=false;
    }
    else
    {
      $output.= $conf['level_separator'];
    }

    if ( !isset($url) )
    {
      $output.= $cat['name'];
    }
    elseif ($url == '')
    {
      $output.= '<a href="'
            .make_index_url(
                array(
                  'category' => $cat,
                  )
              )
            .'">';
      $output.= $cat['name'].'</a>';
    }
    else
    {
      $output.= '<a href="'.PHPWG_ROOT_PATH.$url.$cat['id'].'">';
      $output.= $cat['name'].'</a>';
    }
  }
  if ($replace_space)
  {
    return replace_space($output);
  }
  else
  {
    return $output;
  }
}

/**
 * returns the list of categories as a HTML string, with cache of names
 *
 * categories string returned contains categories as given in the input
 * array $cat_informations. $uppercats is the list of category ids to
 * display in the right order. If url input parameter is empty, returns only
 * the categories name without links.
 *
 * @param string uppercats
 * @param string url
 * @param boolean replace_space
 * @return string
 */
function get_cat_display_name_cache($uppercats,
                                    $url = '',
                                    $replace_space = true)
{
  global $cache, $conf;

  if (!isset($cache['cat_names']))
  {
    $query = '
SELECT id, name, permalink
  FROM '.CATEGORIES_TABLE.'
;';
    $cache['cat_names'] = hash_from_query($query, 'id');
  }

  $output = '';
  $is_first = true;
  foreach (explode(',', $uppercats) as $category_id)
  {
    $cat = $cache['cat_names'][$category_id];

    $cat['name'] = trigger_event(
      'render_category_name',
      $cat['name'],
      'get_cat_display_name_cache'
      );

    if ($is_first)
    {
      $is_first = false;
    }
    else
    {
      $output.= $conf['level_separator'];
    }

    if ( !isset($url) )
    {
      $output.= $cat['name'];
    }
    elseif ($url == '')
    {
      $output.= '
<a href="'
      .make_index_url(
          array(
            'category' => $cat,
            )
        )
      .'">'.$cat['name'].'</a>';
    }
    else
    {
      $output.= '
<a href="'.PHPWG_ROOT_PATH.$url.$category_id.'">'.$cat['name'].'</a>';
    }
  }
  if ($replace_space)
  {
    return replace_space($output);
  }
  else
  {
    return $output;
  }
}

/**
 * returns HTMLized comment contents retrieved from database
 *
 * newlines becomes br tags, _word_ becomes underline, /word/ becomes
 * italic, *word* becomes bolded
 *
 * @param string content
 * @return string
 */
function render_comment_content($content)
{
  $content = htmlspecialchars($content);
  $pattern = '/(https?:\/\/\S*)/';
  $replacement = '<a href="$1" rel="nofollow">$1</a>';
  $content = preg_replace($pattern, $replacement, $content);

  $content = nl2br($content);

  // replace _word_ by an underlined word
  $pattern = '/\b_(\S*)_\b/';
  $replacement = '<span style="text-decoration:underline;">$1</span>';
  $content = preg_replace($pattern, $replacement, $content);

  // replace *word* by a bolded word
  $pattern = '/\b\*(\S*)\*\b/';
  $replacement = '<span style="font-weight:bold;">$1</span>';
  $content = preg_replace($pattern, $replacement, $content);

  // replace /word/ by an italic word
  $pattern = "/\/(\S*)\/(\s)/";
  $replacement = '<span style="font-style:italic;">$1$2</span>';
  $content = preg_replace($pattern, $replacement, $content);

  return $content;
}

function get_cat_display_name_from_id($cat_id,
                                      $url = '',
                                      $replace_space = true)
{
  $cat_info = get_cat_info($cat_id);
  return get_cat_display_name($cat_info['upper_names'], $url, $replace_space);
}

/**
 * Returns an HTML list of tags. It can be a multi select field or a list of
 * checkboxes.
 *
 * @param string HTML field name
 * @param array selected tag ids
 * @return array
 */
function get_html_tag_selection(
  $tags,
  $fieldname,
  $selecteds = array(),
  $forbidden_categories = null
  )
{
  global $conf;

  if (count ($tags) == 0 )
  {
    return '';
  }
  $output = '<ul class="tagSelection">';
  foreach ($tags as $tag)
  {
    $output.=
      '<li>'
      .'<label>'
      .'<input type="checkbox" name="'.$fieldname.'[]"'
      .' value="'.$tag['id'].'"'
      ;

    if (in_array($tag['id'], $selecteds))
    {
      $output.= ' checked="checked"';
    }

    $output.=
      '>'
      .' '. $tag['name']
      .'</label>'
      .'</li>'
      ."\n"
      ;
  }
  $output.= '</ul>';

  return $output;
}

function name_compare($a, $b)
{
  return strcmp(strtolower($a['name']), strtolower($b['name']));
}

function tag_alpha_compare($a, $b)
{
  return strcmp(strtolower($a['url_name']), strtolower($b['url_name']));
}

/**
 * exits the current script (either exit or redirect)
 */
function access_denied()
{
  global $user;

  $login_url =
      get_root_url().'identification.php?redirect='
      .urlencode(urlencode($_SERVER['REQUEST_URI']));

  set_status_header(401);
  if ( isset($user) and !is_a_guest() )
  {
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo '<div style="text-align:center;">'.l10n('You are not authorized to access the requested page').'<br>';
    echo '<a href="'.get_root_url().'identification.php">'.l10n('Identification').'</a>&nbsp;';
    echo '<a href="'.make_index_url().'">'.l10n('Home').'</a></div>';
    echo str_repeat( ' ', 512); //IE6 doesn't error output if below a size
    exit();
  }
  else
  {
    redirect_html($login_url);
  }
}

/**
 * exits the current script with 403 code
 * @param string msg a message to display
 * @param string alternate_url redirect to this url
 */
function page_forbidden($msg, $alternate_url=null)
{
  set_status_header(403);
  if ($alternate_url==null)
    $alternate_url = make_index_url();
  redirect_html( $alternate_url,
    '<div style="text-align:left; margin-left:5em;margin-bottom:5em;">
<h1 style="text-align:left; font-size:36px;">Forbidden</h1><br>'
.$msg.'</div>',
    5 );
}

/**
 * exits the current script with 400 code
 * @param string msg a message to display
 * @param string alternate_url redirect to this url
 */
function bad_request($msg, $alternate_url=null)
{
  set_status_header(400);
  if ($alternate_url==null)
    $alternate_url = make_index_url();
  redirect_html( $alternate_url,
    '<div style="text-align:left; margin-left:5em;margin-bottom:5em;">
<h1 style="text-align:left; font-size:36px;">Bad request</h1><br>'
.$msg.'</div>',
    5 );
}

/**
 * exits the current script with 404 code when a page cannot be found
 * @param string msg a message to display
 * @param string alternate_url redirect to this url
 */
function page_not_found($msg, $alternate_url=null)
{
  set_status_header(404);
  if ($alternate_url==null)
    $alternate_url = make_index_url();
  redirect_html( $alternate_url,
    '<div style="text-align:left; margin-left:5em;margin-bottom:5em;">
<h1 style="text-align:left; font-size:36px;">Page not found</h1><br>'
.$msg.'</div>',
    5 );
}

/**
 * exits the current script with 500 http code
 * this method can be called at any time (does not use template/language/user etc...)
 * @param string msg a message to display
 */
function fatal_error($msg, $title=null, $show_trace=true)
{
  if (empty($title))
  {
    $title = 'Piwigo encountered a non recoverable error';
  }

  $btrace_msg = '';
  if ($show_trace and function_exists('debug_backtrace'))
  {
    $bt = debug_backtrace();
    for ($i=1; $i<count($bt); $i++)
    {
      $class = isset($bt[$i]['class']) ? (@$bt[$i]['class'].'::') : '';
      $btrace_msg .= "#$i\t".$class.@$bt[$i]['function'].' '.@$bt[$i]['file']."(".@$bt[$i]['line'].")\n";
    }
    $btrace_msg = trim($btrace_msg);
    $msg .= "\n";
  }

  $display = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<h1>$title</h1>
<pre style='font-size:larger;background:white;color:red;padding:1em;margin:0;clear:both;display:block;width:auto;height:auto;overflow:auto'>
<b>$msg</b>
$btrace_msg
</pre>\n";

  @set_status_header(500);
  echo $display.str_repeat( ' ', 300); //IE6 doesn't error output if below a size

  if ( function_exists('ini_set') )
  {// if possible turn off error display (we display it)
    ini_set('display_errors', false);
  }
  error_reporting( E_ALL );
  trigger_error( strip_tags($msg).$btrace_msg, E_USER_ERROR );
  die(0); // just in case
}

/* returns the title to be displayed above thumbnails on tag page
 */
function get_tags_content_title()
{
  global $page;
  $title = count($page['tags']) > 1 ? l10n('Tag') : l10n('Tag');
  $title.= ' ';

  for ($i=0; $i<count($page['tags']); $i++)
  {
    $title.= $i>0 ? ' + ' : '';

    $title.=
      '<a href="'
      .make_index_url(
        array(
          'tags' => array( $page['tags'][$i] )
          )
        )
      .'" title="'
      .l10n('See images linked to this tag only')
      .'">'
      .$page['tags'][$i]['name']
      .'</a>';

    $remove_url = null;
    if (count($page['tags']) == 1)
    {
      $remove_url = get_root_url().'tags.php';
    }
    else
    {
      $other_tags = $page['tags'];
      unset($other_tags[$i]);
      $remove_url = make_index_url(
        array(
          'tags' => $other_tags
          )
        );
    }

    $title.=
      '<a href="'.$remove_url.'" style="border:none;" title="'
      .l10n('remove this tag from the list')
      .'"><img src="'
        .get_root_url().get_themeconf('icon_dir').'/remove_s.png'
      .'" alt="x" style="vertical-align:bottom;" class="button">'
      .'</a>';
  }
  return $title;
}

/**
  Sets the http status header (200,401,...)
 */
function set_status_header($code, $text='')
{
  if (empty($text))
  {
    switch ($code)
    {
      case 200: $text='OK';break;
      case 301: $text='Moved permanently';break;
      case 302: $text='Moved temporarily';break;
      case 304: $text='Not modified';break;
      case 400: $text='Bad request';break;
      case 401: $text='Authorization required';break;
      case 403: $text='Forbidden';break;
      case 404: $text='Not found';break;
      case 500: $text='Server error';break;
      case 501: $text='Not implemented';break;
      case 503: $text='Service unavailable';break;
    }
  }
  $protocol = $_SERVER["SERVER_PROTOCOL"];
  if ( ('HTTP/1.1' != $protocol) && ('HTTP/1.0' != $protocol) )
    $protocol = 'HTTP/1.0';

  header( "$protocol $code $text", true, $code );
  trigger_action('set_status_header', $code, $text);
}

/** returns the category comment for rendering in html textual mode (subcatify)
 * this is an event handler. don't call directly
 */
function render_category_literal_description($desc)
{
  return strip_tags($desc, '<span><p><a><br><b><i><small><big><strong><em>');
}

/*event handler for menu*/
function register_default_menubar_blocks( $menu_ref_arr )
{
  $menu = & $menu_ref_arr[0];
  if ($menu->get_id() != 'menubar')
    return;
  $menu->register_block( new RegisteredBlock( 'mbLinks', 'Links', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbCategories', 'Albums', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbTags', 'Related tags', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbSpecials', 'Specials', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbMenu', 'Menu', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbIdentification', 'Identification', 'piwigo') );
}

?>