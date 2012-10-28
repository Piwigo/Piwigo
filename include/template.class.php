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
  private $html_style = '';

  const COMBINED_SCRIPTS_TAG = '<!-- COMBINED_SCRIPTS -->';
  var $scriptLoader;

  const COMBINED_CSS_TAG = '<!-- COMBINED_CSS -->';
  var $css_by_priority = array();
  
  var $picture_buttons = array();
  var $index_buttons = array();

  function Template($root = ".", $theme= "", $path = "template")
  {
    global $conf, $lang_info;

    $this->scriptLoader = new ScriptLoader;
    $this->smarty = new Smarty;
    $this->smarty->debugging = $conf['debug_template'];
    $this->smarty->compile_check = $conf['template_compile_check'];
    $this->smarty->force_compile = $conf['template_force_compile'];

    if (!isset($conf['data_dir_checked']))
    {
      $dir = PHPWG_ROOT_PATH.$conf['data_location'];
      mkgetdir($dir, MKGETDIR_DEFAULT&~MKGETDIR_DIE_ON_ERROR);
      if (!is_writable($dir))
      {
        load_language('admin.lang');
        fatal_error(
          sprintf(
            l10n('Give write access (chmod 777) to "%s" directory at the root of your Piwigo installation'),
            $conf['data_location']
            ),
          l10n('an error happened'),
          false // show trace
          );
      }
      if (function_exists('pwg_query')) {
        conf_update_param('data_dir_checked', 1);
      }
    }

    $compile_dir = PHPWG_ROOT_PATH.$conf['data_location'].'templates_c';
    mkgetdir( $compile_dir );

    $this->smarty->compile_dir = $compile_dir;

    $this->smarty->assign_by_ref( 'pwg', new PwgTemplateAdapter() );
    $this->smarty->register_modifier( 'translate', array('Template', 'mod_translate') );
    $this->smarty->register_modifier( 'explode', array('Template', 'mod_explode') );
    $this->smarty->register_modifier( 'get_extent', array(&$this, 'get_extent') );
    $this->smarty->register_block('html_head', array(&$this, 'block_html_head') );
    $this->smarty->register_block('html_style', array(&$this, 'block_html_style') );
    $this->smarty->register_function('combine_script', array(&$this, 'func_combine_script') );
    $this->smarty->register_function('get_combined_scripts', array(&$this, 'func_get_combined_scripts') );
    $this->smarty->register_function('combine_css', array(&$this, 'func_combine_css') );
    $this->smarty->register_function('define_derivative', array(&$this, 'func_define_derivative') );
    $this->smarty->register_compiler_function('get_combined_css', array(&$this, 'func_get_combined_css') );
    $this->smarty->register_block('footer_script', array(&$this, 'block_footer_script') );
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
      $pos = strpos( $this->output, self::COMBINED_SCRIPTS_TAG );
      if ($pos !== false)
      {
          $scripts = $this->scriptLoader->get_head_scripts();
          $content = array();
          foreach ($scripts as $script)
          {
              $content[]=
                  '<script type="text/javascript" src="'
                  . self::make_script_src($script)
                  .'"></script>';
          }

          $this->output = substr_replace( $this->output, "\n".implode( "\n", $content ), $pos, strlen(self::COMBINED_SCRIPTS_TAG) );
      } //else maybe error or warning ?
    }

    if(!empty($this->css_by_priority))
    {
      ksort($this->css_by_priority);

      global $conf;
      $css = array();
      if ($conf['template_combine_files'])
      {
        $combiner = new FileCombiner('css');
        foreach ($this->css_by_priority as $files)
        {
          foreach ($files as $file_ver)
            $combiner->add( $file_ver[0], $file_ver[1] );
        }
        if ( $combiner->combine( $out_file, $out_version) )
          $css[] = array($out_file, $out_version);
      }
      else
      {
        foreach ($this->css_by_priority as $files)
          $css = array_merge($css, $files);
      }

      $content = array();
      foreach( $css as $file_ver )
      {
        $href = embellish_url(get_root_url().$file_ver[0]);
        if ($file_ver[1] !== false)
          $href .= '?v' . ($file_ver[1] ? $file_ver[1] : PHPWG_VERSION);
        // trigger the event for eventual use of a cdn
        $href = trigger_event('combined_css', $href, $file_ver[0], $file_ver[1]);
        $content[] = '<link rel="stylesheet" type="text/css" href="'.$href.'">';
      }
      $this->output = str_replace(self::COMBINED_CSS_TAG,
          implode( "\n", $content ),
          $this->output );
			$this->css_by_priority = array();
    }

    if ( count($this->html_head_elements) || strlen($this->html_style) )
    {
      $search = "\n</head>";
      $pos = strpos( $this->output, $search );
      if ($pos !== false)
      {
        $rep = "\n".implode( "\n", $this->html_head_elements );
        if (strlen($this->html_style))
        {
          $rep.='<style type="text/css">'.$this->html_style.'</style>';
        }
        $this->output = substr_replace( $this->output, $rep, $pos, 0 );
      } //else maybe error or warning ?
      $this->html_head_elements = array();
      $this->html_style = '';
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
  function block_html_head($params, $content)
  {
    $content = trim($content);
    if ( !empty($content) )
    { // second call
      $this->html_head_elements[] = $content;
    }
  }

  function block_html_style($params, $content)
  {
    $content = trim($content);
    if ( !empty($content) )
    { // second call
      $this->html_style .= $content;
    }
  }

  function func_define_derivative($params)
  {
    !empty($params['name']) or fatal_error('define_derivative missing name');
    if (isset($params['type']))
    {
      $derivative = ImageStdParams::get_by_type($params['type']);
      $this->smarty->assign( $params['name'], $derivative);
      return;
    }
    !empty($params['width']) or fatal_error('define_derivative missing width');
    !empty($params['height']) or fatal_error('define_derivative missing height');

    $w = intval($params['width']);
    $h = intval($params['height']);
    $crop = 0;
    $minw=null;
    $minh=null;

    if (isset($params['crop']))
    {
      if (is_bool($params['crop']))
      {
        $crop = $params['crop'] ? 1:0;
      }
      else
      {
        $crop = round($params['crop']/100, 2);
      }

      if ($crop)
      {
        $minw = empty($params['min_width']) ? $w : intval($params['min_width']);
        $minw <= $w or fatal_error('define_derivative invalid min_width');
        $minh = empty($params['min_height']) ? $h : intval($params['min_height']);
        $minh <= $h or fatal_error('define_derivative invalid min_height');
      }
    }

    $this->smarty->assign( $params['name'], ImageStdParams::get_custom($w, $h, $crop, $minw, $minh) );
  }

   /**
    * combine_script smarty function allows inclusion of a javascript file in the current page.
    * The engine will combine several js files into a single one in order to reduce the number of
    * required http requests.
    * param id - required
    * param path - required - the path to js file RELATIVE to piwigo root dir
    * param load - optional - header|footer|async, default header
    * param require - optional - comma separated list of script ids required to be loaded and executed
        before this one
    * param version - optional - plugins could use this and change it in order to force a
        browser refresh
    */
  function func_combine_script($params)
  {
    if (!isset($params['id']))
    {
      $this->smarty->trigger_error("combine_script: missing 'id' parameter", E_USER_ERROR);
    }
    $load = 0;
    if (isset($params['load']))
    {
      switch ($params['load'])
      {
        case 'header': break;
        case 'footer': $load=1; break;
        case 'async': $load=2; break;
        default: $this->smarty->trigger_error("combine_script: invalid 'load' parameter", E_USER_ERROR);
      }
    }

    // TEMP in 2.5 for backward compatibility
    if(!empty($params['require']))
    {
      $params['require'] = str_replace('jquery.effects.', 'jquery.ui.effect-', $params['require'] );
      $params['require'] = str_replace('jquery.effects', 'jquery.ui.effect', $params['require'] );
    }

    $this->scriptLoader->add( $params['id'], $load,
      empty($params['require']) ? array() : explode( ',', $params['require'] ),
      @$params['path'],
      isset($params['version']) ? $params['version'] : 0 );
  }


  function func_get_combined_scripts($params)
  {
    if (!isset($params['load']))
    {
      $this->smarty->trigger_error("get_combined_scripts: missing 'load' parameter", E_USER_ERROR);
    }
    $load = $params['load']=='header' ? 0 : 1;
    $content = array();

    if ($load==0)
    {
      return self::COMBINED_SCRIPTS_TAG;
    }
    else
    {
      $scripts = $this->scriptLoader->get_footer_scripts();
      foreach ($scripts[0] as $script)
      {
        $content[]=
          '<script type="text/javascript" src="'
          . self::make_script_src($script)
          .'"></script>';
      }
      if (count($this->scriptLoader->inline_scripts))
      {
        $content[]= '<script type="text/javascript">//<![CDATA[
';
        $content = array_merge($content, $this->scriptLoader->inline_scripts);
        $content[]= '//]]></script>';
      }

      if (count($scripts[1]))
      {
        $content[]= '<script type="text/javascript">';
        $content[]= '(function() {
var s,after = document.getElementsByTagName(\'script\')[document.getElementsByTagName(\'script\').length-1];';
        foreach ($scripts[1] as $id => $script)
        {
          $content[]=
            's=document.createElement(\'script\'); s.type=\'text/javascript\'; s.async=true; s.src=\''
            . self::make_script_src($script)
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
    if ( $script->is_remote() )
      $ret = $script->path;
    else
    {
      $ret = get_root_url().$script->path;
      if ($script->version!==false)
      {
        $ret.= '?v'. ($script->version ? $script->version : PHPWG_VERSION);
      }
    }
    // trigger the event for eventual use of a cdn
    $ret = trigger_event('combined_script', $ret, $script);
    return embellish_url($ret);
  }

  function block_footer_script($params, $content)
  {
    $content = trim($content);
    if ( !empty($content) )
    { // second call

      // TEMP in 2.5 for backward compatibility
      if(!empty($params['require']))
      {
        $params['require'] = str_replace('jquery.effects.', 'jquery.ui.effect-', $params['require'] );
        $params['require'] = str_replace('jquery.effects', 'jquery.ui.effect', $params['require'] );
      }

      $this->scriptLoader->add_inline(
        $content,
        empty($params['require']) ? array() : explode(',', $params['require'])
      );
    }
  }

  /**
    * combine_css smarty function allows inclusion of a css stylesheet file in the current page.
    * The engine will combine several css files into a single one in order to reduce the number of
    * required http requests.
    * param path - required - the path to css file RELATIVE to piwigo root dir
    * param version - optional - plugins could use this and change it in order to force a
        browser refresh
    */
  function func_combine_css($params)
  {
    !empty($params['path']) || fatal_error('combine_css missing path');
    $order = (int)@$params['order'];
    $version = isset($params['version']) ? $params['version'] : 0;
    $this->css_by_priority[$order][] = array( $params['path'], $version);
  }

  function func_get_combined_css($params)
  {
    return 'echo '.var_export(self::COMBINED_CSS_TAG,true);
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
    $tags = array('if','foreach','section','footer_script');
    foreach($tags as $tag)
    {
      array_push($regex, "#^[ \t]+($ldq$tag"."[^$ld$rd]*$rdq)\s*$#m");
      array_push($regex, "#^[ \t]+($ldq/$tag$rdq)\s*$#m");
    }
    $tags = array('include','else','combine_script','html_head');
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
    $source = preg_replace_callback( $regex, create_function('$m', 'global $lang; return isset($lang[$m[1]]) ? $lang[$m[1]] : $m[0];'), $source);

    $regex = "~$ldq *\'([^'$]+)\'\|@translate\|~";
    $source = preg_replace_callback( $regex, create_function('$m', 'global $lang; return isset($lang[$m[1]]) ? \'{\'.var_export($lang[$m[1]],true).\'|\' : $m[0];'), $source);

    $regex = "~($ldq *assign +var=.+ +value=)\'([^'$]+)\'\|@translate~";
    $source = preg_replace_callback( $regex, create_function('$m', 'global $lang; return isset($lang[$m[2]]) ? $m[1].var_export($lang[$m[2]],true) : $m[0];'), $source);

    return $source;
  }

  static function prefilter_local_css($source, &$smarty)
  {
    $css = array();
    foreach ($smarty->get_template_vars('themes') as $theme)
    {
      $f = PWG_LOCAL_DIR.'css/'.$theme['id'].'-rules.css';
      if (file_exists(PHPWG_ROOT_PATH.$f))
      {
        array_push($css, "{combine_css path='$f' order=10}");
      }
    }
    $f = PWG_LOCAL_DIR.'css/rules.css';
    if (file_exists(PHPWG_ROOT_PATH.$f))
    {
      array_push($css, "{combine_css path='$f' order=10}");
    }

    if (!empty($css))
    {
      $source = str_replace("\n{get_combined_css}", "\n".implode( "\n", $css )."\n{get_combined_css}", $source);
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
  
  function add_picture_button($content, $rank)
  {
    $this->picture_buttons[$rank][] = $content;
  }
  
  function add_index_button($content, $rank)
  {
    $this->index_buttons[$rank][] = $content;
  }
  
  function parse_picture_buttons()
  {
    if (!empty($this->picture_buttons))
    {
      ksort($this->picture_buttons);
      foreach ($this->picture_buttons as $ranked)
        foreach ($ranked as $content)
          $this->concat('PLUGIN_PICTURE_ACTIONS', $content);
    }
  }
    
  function parse_index_buttons()
  {
    if (!empty($this->index_buttons))
    {
      ksort($this->index_buttons);
      foreach ($this->index_buttons as $ranked)
        foreach ($ranked as $content)
          $this->concat('PLUGIN_INDEX_ACTIONS', $content);
    }
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

  function derivative($type, $img)
  {
    return new DerivativeImage($type, $img);
  }

  function derivative_url($type, $img)
  {
    return DerivativeImage::url($type, $img);
  }
}


final class Script
{
  public $id;
  public $load_mode;
  public $precedents = array();
  public $path;
  public $version;
  public $extra = array();

  function Script($load_mode, $id, $path, $version, $precedents)
  {
    $this->id = $id;
    $this->load_mode = $load_mode;
    $this->id = $id;
    $this->set_path($path);
    $this->version = $version;
    $this->precedents = $precedents;
  }

  function set_path($path)
  {
    if (!empty($path))
      $this->path = $path;
  }

  function is_remote()
  {
    return url_is_remote( $this->path );
  }
}


/** Manage a list of required scripts for a page, by optimizing their loading location (head, bottom, async)
and later on by combining them in a unique file respecting at the same time dependencies.*/
class ScriptLoader
{
  private $registered_scripts;
  public $inline_scripts;

  private $did_head;
  private $head_done_scripts;
  private $did_footer;

  private static $known_paths = array(
      'core.scripts' => 'themes/default/js/scripts.js',
      'jquery' => 'themes/default/js/jquery.min.js',
      'jquery.ui' => 'themes/default/js/ui/minified/jquery.ui.core.min.js',
      'jquery.ui.effect' => 'themes/default/js/ui/minified/jquery.ui.effect.min.js',
    );

  private static $ui_core_dependencies = array(
      'jquery.ui.widget' => array('jquery'),
      'jquery.ui.position' => array('jquery'),
      'jquery.ui.mouse' => array('jquery', 'jquery.ui', 'jquery.ui.widget'),
    );

  function __construct()
  {
    $this->clear();
  }

  function clear()
  {
    $this->registered_scripts = array();
    $this->inline_scripts = array();
    $this->head_done_scripts = array();
    $this->did_head = $this->did_footer = false;
  }

  function get_all()
  {
    return $this->registered_scripts;
  }

  function add_inline($code, $require)
  {
    !$this->did_footer || trigger_error("Attempt to add inline script but the footer has been written", E_USER_WARNING);
    if(!empty($require))
    {
      foreach ($require as $id)
      {
        if(!isset($this->registered_scripts[$id]))
          $this->load_known_required_script($id, 1) or fatal_error("inline script not found require $id");
        $s = $this->registered_scripts[$id];
        if($s->load_mode==2)
          $s->load_mode=1; // until now the implementation does not allow executing inline script depending on another async script
      }
    }
    $this->inline_scripts[] = $code;
  }

  function add($id, $load_mode, $require, $path, $version=0)
  {
    if ($this->did_head && $load_mode==0)
    {
      trigger_error("Attempt to add script $id but the head has been written", E_USER_WARNING);
    }
    elseif ($this->did_footer)
    {
      trigger_error("Attempt to add script $id but the footer has been written", E_USER_WARNING);
    }
    if (! isset( $this->registered_scripts[$id] ) )
    {
      $script = new Script($load_mode, $id, $path, $version, $require);
      self::fill_well_known($id, $script);
      $this->registered_scripts[$id] = $script;

      // Load or modify all UI core files
      if ($id == 'jquery.ui' and $script->path == self::$known_paths['jquery.ui'])
      {
        foreach (self::$ui_core_dependencies as $script_id => $required_ids)
          $this->add($script_id, $load_mode, $required_ids, null, $version);
      }

      // Try to load undefined required script
      foreach ($script->precedents as $script_id)
      {
        if (! isset( $this->registered_scripts[$script_id] ) )
          $this->load_known_required_script($script_id, $load_mode);
      }
    }
    else
    {
      $script = $this->registered_scripts[$id];
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

  function get_head_scripts()
  {
    self::check_load_dep($this->registered_scripts);
    foreach( array_keys($this->registered_scripts) as $id )
    {
      $this->compute_script_topological_order($id);
    }

    uasort($this->registered_scripts, array('ScriptLoader', 'cmp_by_mode_and_order'));

    foreach( $this->registered_scripts as $id => $script)
    {
      if ($script->load_mode > 0)
        break;
      if ( !empty($script->path) )
        $this->head_done_scripts[$id] = $script;
      else
        trigger_error("Script $id has an undefined path", E_USER_WARNING);
    }
    $this->did_head = true;
    return self::do_combine($this->head_done_scripts, 0);
  }

  function get_footer_scripts()
  {
    if (!$this->did_head)
    {
      self::check_load_dep($this->registered_scripts);
    }
    $this->did_footer = true;
    $todo = array();
    foreach( $this->registered_scripts as $id => $script)
    {
      if (!isset($this->head_done_scripts[$id]))
      {
        $todo[$id] = $script;
      }
    }

    foreach( array_keys($todo) as $id )
    {
      $this->compute_script_topological_order($id);
    }

    uasort($todo, array('ScriptLoader', 'cmp_by_mode_and_order'));

    $result = array( array(), array() );
    foreach( $todo as $id => $script)
    {
      $result[$script->load_mode-1][$id] = $script;
    }
    return array( self::do_combine($result[0],1), self::do_combine($result[1],2) );
  }

  private static function do_combine($scripts, $load_mode)
  {
    global $conf;
    if (count($scripts)<2 or !$conf['template_combine_files'])
      return $scripts;
    $combiner = new FileCombiner('js');
    $result = array();
    foreach ($scripts as $script)
    {
      if ($script->is_remote())
      {
        if ( $combiner->combine( $out_file, $out_version) )
        {
          $results[] = new Script($load_mode, 'combi', $out_file, $out_version, array() );
        }
        $results[] = $script;
      }
      else
        $combiner->add( $script->path, $script->version );
    }
    if ( $combiner->combine( $out_file, $out_version) )
    {
      $results[] = new Script($load_mode, 'combi', $out_file, $out_version, array() );
    }
    return $results;
  }

  // checks that if B depends on A, then B->load_mode >= A->load_mode in order to respect execution order
  private static function check_load_dep($scripts)
  {
    global $conf;
    do
    {
      $changed = false;
      foreach( $scripts as $id => $script)
      {
        $load = $script->load_mode;
        foreach( $script->precedents as $precedent)
        {
          if ( !isset($scripts[$precedent] ) )
            continue;
          if ( $scripts[$precedent]->load_mode > $load )
          {
            $scripts[$precedent]->load_mode = $load;
            $changed = true;
          }
          if ($load==2 && $scripts[$precedent]->load_mode==2 && ($scripts[$precedent]->is_remote() or !$conf['template_combine_files']) )
          {// we are async -> a predecessor cannot be async unlesss it can be merged; otherwise script execution order is not guaranteed
            $scripts[$precedent]->load_mode = 1;
            $changed = true;
          }
        }
      }
    }
    while ($changed);
  }


  private static function fill_well_known($id, $script)
  {
    if ( empty($script->path) && isset(self::$known_paths[$id]))
    {
      $script->path = self::$known_paths[$id];
    }
    if ( strncmp($id, 'jquery.', 7)==0 )
    {
      $required_ids = array('jquery');

      if ( strncmp($id, 'jquery.ui.effect-', 17)==0 )
      {
        $required_ids = array('jquery', 'jquery.ui.effect');

        if ( empty($script->path) )
          $script->path = dirname(self::$known_paths['jquery.ui.effect'])."/$id.min.js";
      }
      elseif ( strncmp($id, 'jquery.ui.', 10)==0 )
      {
        if ( !isset(self::$ui_core_dependencies[$id]) )
          $required_ids = array_merge(array('jquery', 'jquery.ui'), array_keys(self::$ui_core_dependencies));

        if ( empty($script->path) )
          $script->path = dirname(self::$known_paths['jquery.ui'])."/$id.min.js";
      }

      foreach ($required_ids as $required_id)
      {
        if ( !in_array($required_id, $script->precedents ) )
          $script->precedents[] = $required_id;
      }
    }
  }

  private function load_known_required_script($id, $load_mode)
  {
    if ( isset(self::$known_paths[$id]) or strncmp($id, 'jquery.ui.', 10)==0  )
    {
      $this->add($id, $load_mode, array(), null);
      return true;
    }
    return false;
  }

  private function compute_script_topological_order($script_id, $recursion_limiter=0)
  {
    if (!isset($this->registered_scripts[$script_id]))
    {
      trigger_error("Undefined script $script_id is required by someone", E_USER_WARNING);
      return 0;
    }
    $recursion_limiter<5 or fatal_error("combined script circular dependency");
    $script = $this->registered_scripts[$script_id];
    if (isset($script->extra['order']))
      return $script->extra['order'];
    if (count($script->precedents) == 0)
      return ($script->extra['order'] = 0);
    $max = 0;
    foreach( $script->precedents as $precedent)
      $max = max($max, $this->compute_script_topological_order($precedent, $recursion_limiter+1) );
    $max++;
    return ($script->extra['order'] = $max);
  }

  private static function cmp_by_mode_and_order($s1, $s2)
  {
    $ret = $s1->load_mode - $s2->load_mode;
    if ($ret) return $ret;

    $ret = $s1->extra['order'] - $s2->extra['order'];
    if ($ret) return $ret;

    if ($s1->extra['order']==0 and ($s1->is_remote() xor $s2->is_remote()) )
    {
      return $s1->is_remote() ? -1 : 1;
    }
    return strcmp($s1->id,$s2->id);
  }
}


/*Allows merging of javascript and css files into a single one.*/
final class FileCombiner
{
  private $type; // js or css
  private $files = array();
  private $versions = array();

  function FileCombiner($type)
  {
    $this->type = $type;
  }

  static function clear_combined_files()
  {
    $dir = opendir(PHPWG_ROOT_PATH.PWG_COMBINED_DIR);
    while ($file = readdir($dir))
    {
      if ( get_extension($file)=='js' || get_extension($file)=='css')
        unlink(PHPWG_ROOT_PATH.PWG_COMBINED_DIR.$file);
    }
    closedir($dir);
  }

  function add($file, $version)
  {
    $this->files[] = $file;
    $this->versions[] = $version;
  }

  function clear()
  {
    $this->files = array();
    $this->versions = array();
  }

  function combine(&$out_file, &$out_version)
  {
    if (count($this->files) == 0)
    {
      return false;
    }
    if (count($this->files) == 1)
    {
      $out_file = $this->files[0];
      $out_version = $this->versions[0];
      $this->clear();
      return 1;
    }

    $is_css = $this->type == "css";
    global $conf;
    $key = array();
    if ($is_css)
      $key[] = get_absolute_root_url(false);//because we modify bg url
    for ($i=0; $i<count($this->files); $i++)
    {
      $key[] = $this->files[$i];
      $key[] = $this->versions[$i];
      if ($conf['template_compile_check']) $key[] = filemtime( PHPWG_ROOT_PATH . $this->files[$i] );
    }
    $key = join('>', $key);

    $file = base_convert(crc32($key),10,36);
    $file = PWG_COMBINED_DIR . $file . '.' . $this->type;

    $exists = file_exists( PHPWG_ROOT_PATH . $file );
    if ($exists)
    {
      $is_reload =
        (isset($_SERVER['HTTP_CACHE_CONTROL']) && strpos($_SERVER['HTTP_CACHE_CONTROL'], 'max-age=0') !== false)
        || (isset($_SERVER['HTTP_PRAGMA']) && strpos($_SERVER['HTTP_PRAGMA'], 'no-cache'));
      if (is_admin() && $is_reload)
      {// the user pressed F5 in the browser
        if ($is_css || $conf['template_compile_check']==false)
          $exists = false; // we foce regeneration of css because @import sub-files are never checked for modification
      }
    }

    if ($exists)
    {
      $out_file = $file;
      $out_version = false;
      $this->clear();
      return 2;
    }

    $output = '';
    foreach ($this->files as $input_file)
    {
      $output .= "/*BEGIN $input_file */\n";
      if ($is_css)
        $output .= self::process_css($input_file);
      else
        $output .= self::process_js($input_file);
      $output .= "\n";
    }

    mkgetdir( dirname(PHPWG_ROOT_PATH.$file) );
    file_put_contents( PHPWG_ROOT_PATH.$file,  $output );
    @chmod(PHPWG_ROOT_PATH.$file, 0644);
    $out_file = $file;
    $out_version = false;
    $this->clear();
    return 2;
  }

  private static function process_js($file)
  {
    $js = file_get_contents(PHPWG_ROOT_PATH . $file);
    if (strpos($file, '.min')===false and strpos($file, '.packed')===false )
    {
      require_once(PHPWG_ROOT_PATH.'include/jsmin.class.php');
      try { $js = JSMin::minify($js); } catch(Exception $e) {}
    }
    return trim($js, " \t\r\n;").";\n";
  }

  private static function process_css($file)
  {
    $css = self::process_css_rec($file);
    if (version_compare(PHP_VERSION, '5.2.4', '>='))
    {
      require_once(PHPWG_ROOT_PATH.'include/cssmin.class.php');
      $css = CssMin::minify($css, array('Variables'=>false));
    }
    $css = trigger_event('combined_css_postfilter', $css);
    return $css;
  }

  private static function process_css_rec($file)
  {
    static $PATTERN = "#url\(\s*['|\"]{0,1}(.*?)['|\"]{0,1}\s*\)#";
    $css = file_get_contents(PHPWG_ROOT_PATH . $file);
    if (preg_match_all($PATTERN, $css, $matches, PREG_SET_ORDER))
    {
      $search = $replace = array();
      foreach ($matches as $match)
      {
        if ( !url_is_remote($match[1]) && $match[1][0] != '/')
        {
          $relative = dirname($file) . "/$match[1]";
          $search[] = $match[0];
          $replace[] = 'url('.embellish_url(get_absolute_root_url(false).$relative).')';
        }
      }
      $css = str_replace($search, $replace, $css);
    }

    $imports = preg_match_all("#@import\s*['|\"]{0,1}(.*?)['|\"]{0,1};#", $css, $matches, PREG_SET_ORDER);
    if ($imports)
    {
      $search = $replace = array();
      foreach ($matches as $match)
      {
        $search[] = $match[0];
        $replace[] = self::process_css_rec(dirname($file) . "/$match[1]");
      }
      $css = str_replace($search, $replace, $css);
    }
    return $css;
  }
}

?>