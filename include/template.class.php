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


require_once(PHPWG_ROOT_PATH.'include/smarty/libs/Smarty.class.php');


class Template {

  var $smarty;

  var $output = '';

  // Hash of filenames for each template handle.
  var $files = array();

  // Template extents filenames for each template handle.
  var $extents = array();

  // Templates prefilter from external sources (plugins)
  var $external_filters = array();

  // used by html_head smarty block to add content before </head>
  var $html_head_elements = array();

  var $scriptLoader;
  var $html_footer_raw_script = array();

  function Template($root = ".", $theme= "", $path = "template")
  {
    global $conf, $lang_info;

    $this->scriptLoader = new ScriptLoader;
    $this->smarty = new Smarty;
    $this->smarty->debugging = $conf['debug_template'];
    $this->smarty->compile_check = $conf['template_compile_check'];
    $this->smarty->force_compile = $conf['template_force_compile'];

    if (!isset($conf['local_data_dir_checked']))
    {
      mkgetdir($conf['local_data_dir'], MKGETDIR_DEFAULT&~MKGETDIR_DIE_ON_ERROR);
      if (!is_writable($conf['local_data_dir']))
      {
        load_language('admin.lang');
        fatal_error(
          sprintf(
            l10n('Give write access (chmod 777) to "%s" directory at the root of your Piwigo installation'),
            basename($conf['local_data_dir'])
            ),
          l10n('an error happened'),
          false // show trace
          );
      }
      if (function_exists('pwg_query')) {
        conf_update_param('local_data_dir_checked', 'true');
      }
    }
    
    $compile_dir = $conf['local_data_dir'].'/templates_c';
    mkgetdir( $compile_dir );

    $this->smarty->compile_dir = $compile_dir;

    $this->smarty->assign_by_ref( 'pwg', new PwgTemplateAdapter() );
    $this->smarty->register_modifier( 'translate', array('Template', 'mod_translate') );
    $this->smarty->register_modifier( 'explode', array('Template', 'mod_explode') );
    $this->smarty->register_modifier( 'get_extent', array(&$this, 'get_extent') );
    $this->smarty->register_block('html_head', array(&$this, 'block_html_head') );
    $this->smarty->register_function('combine_script', array(&$this, 'func_combine_script') );
    $this->smarty->register_function('get_combined_scripts', array(&$this, 'func_get_combined_scripts') );
    $this->smarty->register_block('footer_script', array(&$this, 'block_footer_script') );
    $this->smarty->register_function('known_script', array(&$this, 'func_known_script') );
    $this->smarty->register_prefilter( array('Template', 'prefilter_white_space') );
    if ( $conf['compiled_template_cache_language'] )
    {
      $this->smarty->register_prefilter( array('Template', 'prefilter_language') );
    }

    $this->smarty->template_dir = array();
    if ( !empty($theme) )
    {
      $this->set_theme($root, $theme, $path);
      $this->set_prefilter( 'header', array('Template', 'prefilter_local_css') );
    }
    else
      $this->set_template_dir($root);

    $this->smarty->assign('lang_info', $lang_info);

    if (!defined('IN_ADMIN') and isset($conf['extents_for_templates']))
    {
      $tpl_extents = unserialize($conf['extents_for_templates']);
      $this->set_extents($tpl_extents, './template-extension/', true, $theme);
    }
  }

  /**
   * Load theme's parameters.
   */
  function set_theme($root, $theme, $path, $load_css=true, $load_local_head=true)
  {
    $this->set_template_dir($root.'/'.$theme.'/'.$path);

    $themeconf = $this->load_themeconf($root.'/'.$theme);

    if (isset($themeconf['parent']) and $themeconf['parent'] != $theme)
    {
      $this->set_theme(
        $root,
        $themeconf['parent'],
        $path,
        isset($themeconf['load_parent_css']) ? $themeconf['load_parent_css'] : $load_css,
        isset($themeconf['load_parent_local_head']) ? $themeconf['load_parent_local_head'] : $load_local_head
      );
    }

    $tpl_var = array(
      'id' => $theme,
      'load_css' => $load_css,
    );
    if (!empty($themeconf['local_head']) and $load_local_head)
    {
      $tpl_var['local_head'] = realpath($root.'/'.$theme.'/'.$themeconf['local_head'] );
    }
    $themeconf['id'] = $theme;
    $this->smarty->append('themes', $tpl_var);
    $this->smarty->append('themeconf', $themeconf, true);
  }

  /**
   * Add template directory for this Template object.
   * Set compile id if not exists.
   */
  function set_template_dir($dir)
  {
    $this->smarty->template_dir[] = $dir;

    if (!isset($this->smarty->compile_id))
    {
      $real_dir = realpath($dir);
      $compile_id = crc32( $real_dir===false ? $dir : $real_dir);
      $this->smarty->compile_id = base_convert($compile_id, 10, 36 );
    }
  }

  /**
   * Gets the template root directory for this Template object.
   */
  function get_template_dir()
  {
    return $this->smarty->template_dir;
  }

  /**
   * Deletes all compiled templates.
   */
  function delete_compiled_templates()
  {
      $save_compile_id = $this->smarty->compile_id;
      $this->smarty->compile_id = null;
      $this->smarty->clear_compiled_tpl();
      $this->smarty->compile_id = $save_compile_id;
      file_put_contents($this->smarty->compile_dir.'/index.htm', 'Not allowed!');
  }

  function get_themeconf($val)
  {
    $tc = $this->smarty->get_template_vars('themeconf');
    return isset($tc[$val]) ? $tc[$val] : '';
  }

  /**
   * Sets the template filename for handle.
   */
  function set_filename($handle, $filename)
  {
    return $this->set_filenames( array($handle=>$filename) );
  }

  /**
   * Sets the template filenames for handles. $filename_array should be a
   * hash of handle => filename pairs.
   */
  function set_filenames($filename_array)
  {
    if (!is_array($filename_array))
    {
      return false;
    }
    reset($filename_array);
    while(list($handle, $filename) = each($filename_array))
    {
      if (is_null($filename))
      {
        unset($this->files[$handle]);
      }
      else
      {
        $this->files[$handle] = $this->get_extent($filename, $handle);
      }
    }
    return true;
  }

  /**
   * Sets template extention filename for handles.
   */
  function set_extent($filename, $param, $dir='', $overwrite=true, $theme='N/A')
  {
    return $this->set_extents(array($filename => $param), $dir, $overwrite);
  }

  /**
   * Sets template extentions filenames for handles.
   * $filename_array should be an hash of filename => array( handle, param) or filename => handle
   */
  function set_extents($filename_array, $dir='', $overwrite=true, $theme='N/A')
  {
    if (!is_array($filename_array))
    {
      return false;
    }
    foreach ($filename_array as $filename => $value)
    {
      if (is_array($value))
      {
        $handle = $value[0];
        $param = $value[1];
        $thm = $value[2];
      }
      elseif (is_string($value))
      {
        $handle = $value;
        $param = 'N/A';
        $thm = 'N/A';
      }
      else
      {
        return false;
      }

      if ((stripos(implode('',array_keys($_GET)), '/'.$param) !== false or $param == 'N/A')
        and ($thm == $theme or $thm == 'N/A')
        and (!isset($this->extents[$handle]) or $overwrite)
        and file_exists($dir . $filename))
      {
        $this->extents[$handle] = realpath($dir . $filename);
      }
    }
    return true;
  }

  /** return template extension if exists  */
  function get_extent($filename='', $handle='')
  {
    if (isset($this->extents[$handle]))
    {
      $filename = $this->extents[$handle];
    }
    return $filename;
  }

  /** see smarty assign http://www.smarty.net/manual/en/api.assign.php */
  function assign($tpl_var, $value = null)
  {
    $this->smarty->assign( $tpl_var, $value );
  }

  /**
   * Inserts the uncompiled code for $handle as the value of $varname in the
   * root-level. This can be used to effectively include a template in the
   * middle of another template.
   * This is equivalent to assign($varname, $this->parse($handle, true))
   */
  function assign_var_from_handle($varname, $handle)
  {
    $this->assign($varname, $this->parse($handle, true));
    return true;
  }

  /** see smarty append http://www.smarty.net/manual/en/api.append.php */
  function append($tpl_var, $value=null, $merge=false)
  {
    $this->smarty->append( $tpl_var, $value, $merge );
  }

  /**
   * Root-level variable concatenation. Appends a  string to an existing
   * variable assignment with the same name.
   */
  function concat($tpl_var, $value)
  {
    $old_val = & $this->smarty->get_template_vars($tpl_var);
    if ( isset($old_val) )
    {
      $old_val .= $value;
    }
    else
    {
      $this->assign($tpl_var, $value);
    }
  }

  /** see smarty append http://www.smarty.net/manual/en/api.clear_assign.php */
  function clear_assign($tpl_var)
  {
    $this->smarty->clear_assign( $tpl_var );
  }

  /** see smarty get_template_vars http://www.smarty.net/manual/en/api.get_template_vars.php */
  function &get_template_vars($name=null)
  {
    return $this->smarty->get_template_vars( $name );
  }


  /**
   * Load the file for the handle, eventually compile the file and run the compiled
   * code. This will add the output to the results or return the result if $return
   * is true.
   */
  function parse($handle, $return=false)
  {
    if ( !isset($this->files[$handle]) )
    {
      fatal_error("Template->parse(): Couldn't load template file for handle $handle");
    }

    $this->smarty->assign( 'ROOT_URL', get_root_url() );
    $this->smarty->assign( 'TAG_INPUT_ENABLED',
      ((is_adviser()) ? 'disabled="disabled" onclick="return false;"' : ''));

    $save_compile_id = $this->smarty->compile_id;
    $this->load_external_filters($handle);

    global $conf, $lang_info;
    if ( $conf['compiled_template_cache_language'] and isset($lang_info['code']) )
    {
      $this->smarty->compile_id .= '.'.$lang_info['code'];
    }

    $v = $this->smarty->fetch($this->files[$handle], null, null, false);

    $this->smarty->compile_id = $save_compile_id;
    $this->unload_external_filters($handle);

    if ($return)
    {
      return $v;
    }
    $this->output .= $v;
  }

  /**
   * Load the file for the handle, eventually compile the file and run the compiled
   * code. This will print out the results of executing the template.
   */
  function pparse($handle)
  {
    $this->parse($handle, false);
    $this->flush();
  }

  function flush()
  {
    if (!$this->scriptLoader->did_head())
    {
      $search = "\n</head>";
      $pos = strpos( $this->output, $search );
      if ($pos !== false)
      {
          $scripts = $this->scriptLoader->get_head_scripts();
          $content = array();
          foreach ($scripts as $id => $script)
          {
              $content[]=
                  '<script type="text/javascript" src="'
                  . Template::make_script_src($script)
                  .'"></script>';
          }

          $this->output = substr_replace( $this->output, "\n".implode( "\n", $content ), $pos, 0 );
      } //else maybe error or warning ?
    }
    
    if ( count($this->html_head_elements) )
    {
      $search = "\n</head>";
      $pos = strpos( $this->output, $search );
      if ($pos !== false)
      {
        $this->output = substr_replace( $this->output, "\n".implode( "\n", $this->html_head_elements ), $pos, 0 );
      } //else maybe error or warning ?
      $this->html_head_elements = array();
    }

    echo $this->output;
    $this->output='';
  }

  /** flushes the output */
  function p()
  {
    $this->flush();

    if ($this->smarty->debugging)
    {
      global $t2;
      $this->smarty->assign(
        array(
        'AAAA_DEBUG_TOTAL_TIME__' => get_elapsed_time($t2, get_moment())
        )
        );
      require_once(SMARTY_CORE_DIR . 'core.display_debug_console.php');
      echo smarty_core_display_debug_console(null, $this->smarty);
    }
  }

  /**
   * translate variable modifier - translates a text to the currently loaded
   * language
   */
  static function mod_translate($text)
  {
    return l10n($text);
  }

  /**
   * explode variable modifier - similar to php explode
   * 'Yes;No'|@explode:';' -> array('Yes', 'No')
   */
  static function mod_explode($text, $delimiter=',')
  {
    return explode($delimiter, $text);
  }

  /**
   * This smarty "html_head" block allows to add content just before
   * </head> element in the output after the head has been parsed. This is
   * handy in order to respect strict standards when <style> and <link>
   * html elements must appear in the <head> element
   */
  function block_html_head($params, $content, &$smarty, &$repeat)
  {
    $content = trim($content);
    if ( !empty($content) )
    { // second call
      $this->html_head_elements[] = $content;
    }
  }

 /**
   * This smarty "known_script" functions allows to insert well known java scripts
   * such as prototype, jquery, etc... only once. Examples:
   * {known_script id="jquery" src="{$ROOT_URL}template-common/lib/jquery.packed.js"}
   */
  function func_known_script($params, &$smarty )
  {
    if (!isset($params['id']))
    {
        $smarty->trigger_error("known_script: missing 'id' parameter");
        return;
    }
    $id = $params['id'];
    if (! isset( $this->known_scripts[$id] ) )
    {
      if (!isset($params['src']))
      {
          $smarty->trigger_error("known_script: missing 'src' parameter");
          return;
      }
      $this->known_scripts[$id] = $params['src'];
      $content = '<script type="text/javascript" src="'.$params['src'].'"></script>';
      if (isset($params['now']) and $params['now'] and empty($this->output) )
      {
        return $content;
      }
      $repeat = false;
      $this->block_html_head(null, $content, $smarty, $repeat);
    }
  }
  
  function func_combine_script($params, &$smarty)
  {
    if (!isset($params['id']))
    {
      $smarty->trigger_error("combine_script: missing 'id' parameter", E_USER_ERROR);
    }
    $load = 0;
    if (isset($params['load']))
    {
      switch ($params['load'])
      {
        case 'header': break;
        case 'footer': $load=1; break;
        case 'async': $load=2; break;
        default: $smarty->trigger_error("combine_script: invalid 'load' parameter", E_USER_ERROR);
      }
    }
    $this->scriptLoader->add( $params['id'], $load, 
      empty($params['require']) ? array() : explode( ',', $params['require'] ), 
      @$params['path'], 
      isset($params['version']) ? $params['version'] : 0 );
  }


  function func_get_combined_scripts($params, &$smarty)
  {
    if (!isset($params['load']))
    {
      $smarty->trigger_error("get_combined_scripts: missing 'load' parameter", E_USER_ERROR);
    }
    $load = $params['load']=='header' ? 0 : 1;
    $content = array();
    
    if ($load==0)
    {
      if ($this->scriptLoader->did_head())
        fatal_error('get_combined_scripts several times header');
        
      $scripts = $this->scriptLoader->get_head_scripts();
      foreach ($scripts as $id => $script)
      {
          $content[]=
              '<script type="text/javascript" src="'
              . Template::make_script_src($script)
              .'"></script>';
      }
    }
    else
    {
      if (!$this->scriptLoader->did_head())
        fatal_error('get_combined_scripts didn\'t call header');
      $scripts = $this->scriptLoader->get_footer_scripts();
      foreach ($scripts[0] as $id => $script)
      {
        $content[]=
          '<script type="text/javascript" src="'
          . Template::make_script_src($script)
          .'"></script>';
      }
      if (count($this->html_footer_raw_script))
      {
        $content[]= '<script type="text/javascript">';
        $content = array_merge($content, $this->html_footer_raw_script);
        $content[]= '</script>';
      }

      if (count($scripts[1]))
      {
        $content[]= '<script type="text/javascript">';
        $content[]= '(function() {
  var after = document.getElementsByTagName(\'script\')[document.getElementsByTagName(\'script\').length-1];
  var s;';
        foreach ($scripts[1] as $id => $script)
        {
          $content[]=
            's=document.createElement(\'script\'); s.type = \'text/javascript\'; s.async = true; s.src = \''
            . Template::make_script_src($script)
            .'\';';
          $content[]= 'after = after.parentNode.insertBefore(s, after);';
        }
        $content[]= '})();';
        $content[]= '</script>';
      }
    }
    return implode("\n", $content);
  }


  private static function make_script_src( $script )
  {
    $ret = '';
    if ( url_is_remote($script->path) )
      $ret = $script->path;
    else
    {
      $ret = get_root_url().$script->path;
      if ($script->version!==false)
      {
        $ret.= '?v'. ($script->version ? $script->version : PHPWG_VERSION);
      }
    }
    return $ret;
  }

  function block_footer_script($params, $content, &$smarty, &$repeat)
  {
    $content = trim($content);
    if ( !empty($content) )
    { // second call
      $this->html_footer_raw_script[] = $content;
    }
  }

 /**
   * This function allows to declare a Smarty prefilter from a plugin, thus allowing
   * it to modify template source before compilation and without changing core files
   * They will be processed by weight ascending.
   * http://www.smarty.net/manual/en/advanced.features.prefilters.php
   */
  function set_prefilter($handle, $callback, $weight=50)
  {
    $this->external_filters[$handle][$weight][] = array('prefilter', $callback);
    ksort($this->external_filters[$handle]);
  }

  function set_postfilter($handle, $callback, $weight=50)
  {
    $this->external_filters[$handle][$weight][] = array('postfilter', $callback);
    ksort($this->external_filters[$handle]);
  }

  function set_outputfilter($handle, $callback, $weight=50)
  {
    $this->external_filters[$handle][$weight][] = array('outputfilter', $callback);
    ksort($this->external_filters[$handle]);
  }

 /**
   * This function actually triggers the filters on the tpl files.
   * Called in the parse method.
   * http://www.smarty.net/manual/en/advanced.features.prefilters.php
   */
  function load_external_filters($handle)
  {
    if (isset($this->external_filters[$handle]))
    {
      $compile_id = '';
      foreach ($this->external_filters[$handle] as $filters)
      {
        foreach ($filters as $filter)
        {
          list($type, $callback) = $filter;
          $compile_id .= $type.( is_array($callback) ? implode('', $callback) : $callback );
          call_user_func(array($this->smarty, 'register_'.$type), $callback);
        }
      }
      $this->smarty->compile_id .= '.'.base_convert(crc32($compile_id), 10, 36);
    }
  }

  function unload_external_filters($handle)
  {
    if (isset($this->external_filters[$handle]))
    {
      foreach ($this->external_filters[$handle] as $filters)
      {
        foreach ($filters as $filter)
        {
          list($type, $callback) = $filter;
          call_user_func(array($this->smarty, 'unregister_'.$type), $callback);
        }
      }
    }
  }

  static function prefilter_white_space($source, &$smarty)
  {
    $ld = $smarty->left_delimiter;
    $rd = $smarty->right_delimiter;
    $ldq = preg_quote($ld, '#');
    $rdq = preg_quote($rd, '#');

    $regex = array();
    $tags = array('if', 'foreach', 'section');
    foreach($tags as $tag)
    {
      array_push($regex, "#^[ \t]+($ldq$tag"."[^$ld$rd]*$rdq)\s*$#m");
      array_push($regex, "#^[ \t]+($ldq/$tag$rdq)\s*$#m");
    }
    $tags = array('include', 'else', 'html_head');
    foreach($tags as $tag)
    {
      array_push($regex, "#^[ \t]+($ldq$tag"."[^$ld$rd]*$rdq)\s*$#m");
    }
    $source = preg_replace( $regex, "$1", $source);
    return $source;
  }

  /**
   * Smarty prefilter to allow caching (whenever possible) language strings
   * from templates.
   */
  static function prefilter_language($source, &$smarty)
  {
    global $lang;
    $ldq = preg_quote($smarty->left_delimiter, '~');
    $rdq = preg_quote($smarty->right_delimiter, '~');

    $regex = "~$ldq *\'([^'$]+)\'\|@translate *$rdq~";
    $source = preg_replace( $regex.'e', 'isset($lang[\'$1\']) ? $lang[\'$1\'] : \'$0\'', $source);

    $regex = "~$ldq *\'([^'$]+)\'\|@translate\|~";
    $source = preg_replace( $regex.'e', 'isset($lang[\'$1\']) ? \'{\'.var_export($lang[\'$1\'],true).\'|\' : \'$0\'', $source);

    $regex = "~($ldq *assign +var=.+ +value=)\'([^'$]+)\'\|@translate~e";
    $source = preg_replace( $regex, 'isset($lang[\'$2\']) ? \'$1\'.var_export($lang[\'$2\'],true) : \'$0\'', $source);

    return $source;
  }

  static function prefilter_local_css($source, &$smarty)
  {
    $css = array();

    foreach ($smarty->get_template_vars('themes') as $theme)
    {
      if (file_exists(PHPWG_ROOT_PATH.'local/css/'.$theme['id'].'-rules.css'))
      {
        array_push($css, '<link rel="stylesheet" type="text/css" href="{$ROOT_URL}local/css/'.$theme['id'].'-rules.css">');
      }
    }
    if (file_exists(PHPWG_ROOT_PATH.'local/css/rules.css'))
    {
      array_push($css, '<link rel="stylesheet" type="text/css" href="{$ROOT_URL}local/css/rules.css">');
    }

    if (!empty($css))
    {
      $source = str_replace("\n</head>", "\n".implode( "\n", $css )."\n</head>", $source);
    }

    return $source;
  }

  function load_themeconf($dir)
  {
    global $themeconfs, $conf;

    $dir = realpath($dir);
    if (!isset($themeconfs[$dir]))
    {
      $themeconf = array();
      include($dir.'/themeconf.inc.php');
      // Put themeconf in cache
      $themeconfs[$dir] = $themeconf;
    }
    return $themeconfs[$dir];
  }
}


/**
 * This class contains basic functions that can be called directly from the
 * templates in the form $pwg->l10n('edit')
 */
class PwgTemplateAdapter
{
  function l10n($text)
  {
    return l10n($text);
  }

  function l10n_dec($s, $p, $v)
  {
    return l10n_dec($s, $p, $v);
  }

  function sprintf()
  {
    $args = func_get_args();
    return call_user_func_array('sprintf',  $args );
  }
}


final class Script
{
  public $load_mode;
  public $precedents = array();
  public $path;
  public $version;
  public $extra = array();

  function Script($load_mode, $precedents, $path, $version)
  {
    $this->load_mode = $load_mode;
    $this->precedents = $precedents;
    $this->path = $path;
    $this->version = $version;
  }

  function set_path($path)
  {
    if (!empty($path))
      $this->path = $path;
  }
}


/** Manage a list of required scripts for a page, by optimizing their loading location (head, bottom, async)
and later on by combining them in a unique file respecting at the same time dependencies.*/
class ScriptLoader
{
  private $registered_scripts;
  private $did_head;
  private static $known_paths = array(
      'core.scripts' => 'themes/default/js/scripts.js',
      'jquery' => 'themes/default/js/jquery.min.js',
      'jquery.ui' => 'themes/default/js/ui/packed/ui.core.packed.js'
    );

  function __construct()
  {
    $this->clear();
  }

  function clear()
  {
    $this->registered_scripts = array();
    $this->did_head = false;
  }

  function add($id, $load_mode, $require, $path, $version=0)
  {
    if ($this->did_head && $load_mode==0 )
    {
      trigger_error("Attempt to add a new script $id but the head has been written", E_USER_WARNING);
    }
    if (! isset( $this->registered_scripts[$id] ) )
    {
      $script = new Script($load_mode, $require, $path, $version);
      self::fill_well_known($id, $script);
      $this->registered_scripts[$id] = $script;
    }
    else
    {
      $script = & $this->registered_scripts[$id];
      if (count($require))
      {
        $script->precedents = array_unique( array_merge($script->precedents, $require) );
      }
      $script->set_path($path);
      if ($version && version_compare($script->version, $version)<0 )
        $script->version = $version;
      if ($load_mode < $script->load_mode)
        $script->load_mode = $load_mode;
    }
  }

  function did_head()
  {
    return $this->did_head;
  }

  private static function fill_well_known($id, $script)
  {
    if ( empty($script->path) && isset(self::$known_paths[$id]))
    {
      $script->path = self::$known_paths[$id];
    }
    if ( strncmp($id, 'jquery.', 7)==0 )
    {
      if ( !in_array('jquery', $script->precedents ) )
        $script->precedents[] = 'jquery';
      if ( strncmp($id, 'jquery.ui.', 10)==0 && !in_array('jquery.ui', $script->precedents ) )
        $script->precedents[] = 'jquery.ui';
    }
  }

  function get_head_scripts()
  {
    do
    {
      $changed = false;
      foreach( $this->registered_scripts as $id => $script)
      {
        $load = $script->load_mode;
        if ($load==0)
          continue;
        if ($load==2)
          $load=1; // we are async -> a predecessor cannot be async because the script execution order is not guaranteed
        foreach( $script->precedents as $precedent)
        {
          if ( !isset($this->registered_scripts[$precedent] ) )
          {
            trigger_error("Script $id requires undefined script $precedent", E_USER_WARNING);
            continue;
          }
          if ( $this->registered_scripts[$precedent]->load_mode > $load )
          {
            $this->registered_scripts[$precedent]->load_mode = $load;
            $changed = true;
          }
        }
      }
    }
    while ($changed);

    foreach( array_keys($this->registered_scripts) as $id )
    {
      $this->compute_script_topological_order($id);
    }

    uasort($this->registered_scripts, array('ScriptLoader', 'cmp_by_mode_and_order'));

    $result = array();
    foreach( $this->registered_scripts as $id => $script)
    {
      if ($script->load_mode > 0)
        break;
      if ( !empty($script->path) )
        $result[$id] = $script;
      else
        trigger_error("Script $id has an undefined path", E_USER_WARNING);
    }
    $this->did_head = true;
    return $result;
  }

  function get_footer_scripts()
  {
    if (!$this->did_head)
    {
      trigger_error("Attempt to write footer scripts without header scripts", E_USER_WARNING);
    }
    $result = array( array(), array() );
    foreach( $this->registered_scripts as $id => $script)
    {
      if ($script->load_mode > 0)
      {
        if ( !empty( $script->path ) )
        {
          $result[$script->load_mode-1][$id] = $script;
        }
        else
          trigger_error("Script $id has an undefined path", E_USER_WARNING);
      }
    }
    return $result;
  }

  private function compute_script_topological_order($script_id)
  {
    if (!isset($this->registered_scripts[$script_id]))
    {
      trigger_error("Undefined script $script_id is required by someone", E_USER_WARNING);
      return 0;
    }
    $script = & $this->registered_scripts[$script_id];
    if (isset($script->extra['order']))
      return $script->extra['order'];
    if (count($script->precedents) == 0)
      return ($script->extra['order'] = 0);
    $max = 0;
    foreach( $script->precedents as $precedent)
      $max = max($max, $this->compute_script_topological_order($precedent) );
    $max++;
    return ($script->extra['order'] = $max);
  }

  private static function cmp_by_mode_and_order($s1, $s2)
  {
    $ret = $s1->load_mode - $s2->load_mode;
    if (!$ret)
      $ret = $s1->extra['order'] - $s2->extra['order'];
    return $ret;
  }
}

?>
