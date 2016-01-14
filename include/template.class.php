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

/**
 * @package template
 */

require_once( PHPWG_ROOT_PATH .'include/smarty/libs/Smarty.class.php');


/** default rank for buttons */
define('BUTTONS_RANK_NEUTRAL', 50);

/**
 * This a wrapper arround Smarty classes proving various custom mechanisms for templates.
 */
class Template
{
  /** @var Smarty */
  var $smarty;
  /** @var string */
  var $output = '';

  /** @var string[] - Hash of filenames for each template handle. */
  var $files = array();
  /** @var string[] - Template extents filenames for each template handle. */
  var $extents = array();
  /** @var array - Templates prefilter from external sources (plugins) */
  var $external_filters = array();

  /** @var string - Content to add before </head> tag */
  var $html_head_elements = array();
  /** @var string - Runtime CSS rules */
  private $html_style = '';

  /** @const string */
  const COMBINED_SCRIPTS_TAG = '<!-- COMBINED_SCRIPTS -->';
  /** @var ScriptLoader */
  var $scriptLoader;

  /** @const string */
  const COMBINED_CSS_TAG = '<!-- COMBINED_CSS -->';
  /** @var CssLoader */
  var $cssLoader;

  /** @var array - Runtime buttons on picture page */
  var $picture_buttons = array();
  /** @var array - Runtime buttons on index page */
  var $index_buttons = array();


  /**
   * @var string $root
   * @var string $theme
   * @var string $path
   */
  function __construct($root=".", $theme="", $path="template")
  {
    global $conf, $lang_info;

    SmartyException::$escape = false;

    $this->scriptLoader = new ScriptLoader;
    $this->cssLoader = new CssLoader;
    $this->smarty = new Smarty;
    $this->smarty->debugging = $conf['debug_template'];
    if (!$this->smarty->debugging)
    {
      $this->smarty->error_reporting = error_reporting() & ~E_NOTICE;
    }
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
          l10n(
            'Give write access (chmod 777) to "%s" directory at the root of your Piwigo installation',
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

    $this->smarty->setCompileDir($compile_dir);

    $this->smarty->assign( 'pwg', new PwgTemplateAdapter() );
    $this->smarty->registerPlugin('modifiercompiler', 'translate', array('Template', 'modcompiler_translate') );
    $this->smarty->registerPlugin('modifiercompiler', 'translate_dec', array('Template', 'modcompiler_translate_dec') );
    $this->smarty->registerPlugin('modifier', 'explode', array('Template', 'mod_explode') );
    $this->smarty->registerPlugin('modifier', 'ternary', array('Template', 'mod_ternary') );
    $this->smarty->registerPlugin('modifier', 'get_extent', array($this, 'get_extent') );
    $this->smarty->registerPlugin('block', 'html_head', array($this, 'block_html_head') );
    $this->smarty->registerPlugin('block', 'html_style', array($this, 'block_html_style') );
    $this->smarty->registerPlugin('function', 'combine_script', array($this, 'func_combine_script') );
    $this->smarty->registerPlugin('function', 'get_combined_scripts', array($this, 'func_get_combined_scripts') );
    $this->smarty->registerPlugin('function', 'combine_css', array($this, 'func_combine_css') );
    $this->smarty->registerPlugin('function', 'define_derivative', array($this, 'func_define_derivative') );
    $this->smarty->registerPlugin('compiler', 'get_combined_css', array($this, 'func_get_combined_css') );
    $this->smarty->registerPlugin('block', 'footer_script', array($this, 'block_footer_script') );
    $this->smarty->registerFilter('pre', array('Template', 'prefilter_white_space') );
    if ( $conf['compiled_template_cache_language'] )
    {
      $this->smarty->registerFilter('post', array('Template', 'postfilter_language') );
    }

    $this->smarty->setTemplateDir(array());
    if ( !empty($theme) )
    {
      $this->set_theme($root, $theme, $path);
      if (!defined('IN_ADMIN'))
      {
        $this->set_prefilter( 'header', array('Template', 'prefilter_local_css') );
      }
    }
    else
      $this->set_template_dir($root);

    if (isset($lang_info['code']) and !isset($lang_info['jquery_code']))
    {
      $lang_info['jquery_code'] = $lang_info['code'];
    }

    if (isset($lang_info['jquery_code']) and !isset($lang_info['plupload_code']))
    {
      $lang_info['plupload_code'] = str_replace('-', '_', $lang_info['jquery_code']);
    }
    
    $this->smarty->assign('lang_info', $lang_info);

    if (!defined('IN_ADMIN') and isset($conf['extents_for_templates']))
    {
      $tpl_extents = unserialize($conf['extents_for_templates']);
      $this->set_extents($tpl_extents, './template-extension/', true, $theme);
    }
  }

  /**
   * Loads theme's parameters.
   *
   * @param string $root
   * @param string $theme
   * @param string $path
   * @param bool $load_css
   * @param bool $load_local_head
   */
  function set_theme($root, $theme, $path, $load_css=true, $load_local_head=true, $colorscheme='dark')
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

    if (!isset($themeconf['colorscheme']))
    {
      $themeconf['colorscheme'] = $colorscheme;
    }
    
    $this->smarty->append('themes', $tpl_var);
    $this->smarty->append('themeconf', $themeconf, true);
  }

  /**
   * Adds template directory for this Template object.
   * Also set compile id if not exists.
   *
   * @param string $dir
   */
  function set_template_dir($dir)
  {
    $this->smarty->addTemplateDir($dir);

    if (!isset($this->smarty->compile_id))
    {
      $compile_id = "1";
      $compile_id .= ($real_dir = realpath($dir))===false ? $dir : $real_dir;
      $this->smarty->compile_id = base_convert(crc32($compile_id), 10, 36 );
    }
  }

  /**
   * Gets the template root directory for this Template object.
   *
   * @return string
   */
  function get_template_dir()
  {
    return $this->smarty->getTemplateDir();
  }

  /**
   * Deletes all compiled templates.
   */
  function delete_compiled_templates()
  {
      $save_compile_id = $this->smarty->compile_id;
      $this->smarty->compile_id = null;
      $this->smarty->clearCompiledTemplate();
      $this->smarty->compile_id = $save_compile_id;
      file_put_contents($this->smarty->getCompileDir().'/index.htm', 'Not allowed!');
  }

  /**
   * Returns theme's parameter.
   *
   * @param string $val
   * @return mixed
   */
  function get_themeconf($val)
  {
    $tc = $this->smarty->getTemplateVars('themeconf');
    return isset($tc[$val]) ? $tc[$val] : '';
  }

  /**
   * Sets the template filename for handle.
   *
   * @param string $handle
   * @param string $filename
   * @return bool
   */
  function set_filename($handle, $filename)
  {
    return $this->set_filenames( array($handle=>$filename) );
  }

  /**
   * Sets the template filenames for handles.
   *
   * @param string[] $filename_array hashmap of handle=>filename
   * @return true
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
   *
   * @param string $filename
   * @param mixed $param
   * @param string $dir
   * @param bool $overwrite
   * @param string $theme
   * @return bool
   */
  function set_extent($filename, $param, $dir='', $overwrite=true, $theme='N/A')
  {
    return $this->set_extents(array($filename => $param), $dir, $overwrite);
  }

  /**
   * Sets template extentions filenames for handles.
   *
   * @param string[] $filename_array hashmap of handle=>filename
   * @param string $dir
   * @param bool $overwrite
   * @param string $theme
   * @return bool
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

  /**
   * Returns template extension if exists.
   *
   * @param string $filename should be empty!
   * @param string $handle
   * @return string
   */
  function get_extent($filename='', $handle='')
  {
    if (isset($this->extents[$handle]))
    {
      $filename = $this->extents[$handle];
    }
    return $filename;
  }

  /**
   * Assigns a template variable.
   * @see http://www.smarty.net/manual/en/api.assign.php
   *
   * @param string|array $tpl_var can be a var name or a hashmap of variables
   *    (in this case, do not use the _$value_ parameter)
   * @param mixed $value
   */
  function assign($tpl_var, $value=null)
  {
    $this->smarty->assign( $tpl_var, $value );
  }

  /**
   * Defines _$varname_ as the compiled result of _$handle_.
   * This can be used to effectively include a template in another template.
   * This is equivalent to assign($varname, $this->parse($handle, true)).
   *
   * @param string $varname
   * @param string $handle
   * @return true
   */
  function assign_var_from_handle($varname, $handle)
  {
    $this->assign($varname, $this->parse($handle, true));
    return true;
  }

  /**
   * Appends a new value in a template array variable, the variable is created if needed.
   * @see http://www.smarty.net/manual/en/api.append.php
   *
   * @param string $tpl_var
   * @param mixed $value
   * @param bool $merge
   */
  function append($tpl_var, $value=null, $merge=false)
  {
    $this->smarty->append( $tpl_var, $value, $merge );
  }

  /**
   * Performs a string concatenation.
   *
   * @param string $tpl_var
   * @param string $value
   */
  function concat($tpl_var, $value)
  {
    $this->assign($tpl_var,
      $this->smarty->getTemplateVars($tpl_var) . $value);
  }

  /**
   * Removes an assigned template variable.
   * @see http://www.smarty.net/manual/en/api.clear_assign.php
   *
   * @param string $tpl_var
   */
  function clear_assign($tpl_var)
  {
    $this->smarty->clearAssign( $tpl_var );
  }

  /**
   * Returns an assigned template variable.
   * @see http://www.smarty.net/manual/en/api.get_template_vars.php
   *
   * @param string $tpl_var
   */
  function get_template_vars($tpl_var=null)
  {
    return $this->smarty->getTemplateVars( $tpl_var );
  }

  /**
   * Loads the template file of the handle, compiles it and appends the result to the output
   * (or returns it if _$return_ is true).
   *
   * @param string $handle
   * @param bool $return
   * @return null|string
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
      $this->smarty->compile_id .= '_'.$lang_info['code'];
    }

    $v = $this->smarty->fetch($this->files[$handle]);

    $this->smarty->compile_id = $save_compile_id;
    $this->unload_external_filters($handle);

    if ($return)
    {
      return $v;
    }
    $this->output .= $v;
  }

  /**
   * Loads the template file of the handle, compiles it and appends the result to the output,
   * then sends the output to the browser.
   *
   * @param string $handle
   */
  function pparse($handle)
  {
    $this->parse($handle, false);
    $this->flush();
  }

  /**
   * Load and compile JS & CSS into the template and sends the output to the browser.
   */
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

          $this->output = substr_replace( $this->output, implode( "\n", $content ), $pos, strlen(self::COMBINED_SCRIPTS_TAG) );
      } //else maybe error or warning ?
    }

    $css = $this->cssLoader->get_css();

    $content = array();
    foreach( $css as $combi )
    {
      $href = embellish_url(get_root_url().$combi->path);
      if ($combi->version !== false)
        $href .= '?v' . ($combi->version ? $combi->version : PHPWG_VERSION);
      // trigger the event for eventual use of a cdn
      $href = trigger_change('combined_css', $href, $combi);
      $content[] = '<link rel="stylesheet" type="text/css" href="'.$href.'">';
    }
    $this->output = str_replace(self::COMBINED_CSS_TAG,
        implode( "\n", $content ),
        $this->output );
    $this->cssLoader->clear();

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

  /**
   * Same as flush() but with optional debugging.
   * @see Template::flush()
   */
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
      Smarty_Internal_Debug::display_debug($this->smarty);
    }
  }

  /**
   * Eval a temp string to retrieve the original PHP value.
   *
   * @param string $str
   * @return mixed
   */
  static function get_php_str_val($str)
  {
    if (is_string($str) && strlen($str)>1)
    {
      if ( ($str[0]=='\'' && $str[strlen($str)-1]=='\'')
        || ($str[0]=='"' && $str[strlen($str)-1]=='"'))
      {
        eval('$tmp='.$str.';');
        return $tmp;
      }
    }
    return null;
  }

  /**
   * "translate" variable modifier.
   * Usage :
   *    - {'Comment'|translate}
   *    - {'%d comments'|translate:$count}
   * @see l10n()
   *
   * @param array $params
   * @return string
   */
  static function modcompiler_translate($params)
  {
    global $conf, $lang;

    switch (count($params))
    {
    case 1:
      if ($conf['compiled_template_cache_language']
        && ($key=self::get_php_str_val($params[0])) !== null
        && isset($lang[$key])
      ) {
        return var_export($lang[$key], true);
      }
      return 'l10n('.$params[0].')';

    default:
      if ($conf['compiled_template_cache_language'])
      {
        $ret = 'sprintf(';
        $ret .= self::modcompiler_translate( array($params[0]) );
        $ret .= ','. implode(',', array_slice($params, 1));
        $ret .= ')';
        return $ret;
      }
      return 'l10n('.$params[0].','.implode(',', array_slice($params, 1)).')';
    }
  }

  /**
   * "translate_dec" variable modifier.
   * Usage :
   *    - {$count|translate_dec:'%d comment':'%d comments'}
   * @see l10n_dec()
   *
   * @param array $params
   * @return string
   */
  static function modcompiler_translate_dec($params)
  {
    global $conf, $lang, $lang_info;
    if ($conf['compiled_template_cache_language'])
    {
      $ret = 'sprintf(';
      if ($lang_info['zero_plural'])
      {
        $ret .= '($tmp=('.$params[0].'))>1||$tmp==0';
      }
      else
      {
        $ret .= '($tmp=('.$params[0].'))>1';
      }
      $ret .= '?';
      $ret .= self::modcompiler_translate( array($params[2]) );
      $ret .= ':';
      $ret .= self::modcompiler_translate( array($params[1]) );
      $ret .= ',$tmp';
      $ret .= ')';
      return $ret;
    }
    return 'l10n_dec('.$params[1].','.$params[2].','.$params[0].')';
  }

  /**
   * "explode" variable modifier.
   * Usage :
   *    - {assign var=valueExploded value=$value|explode:','}
   *
   * @param string $text
   * @param string $delimiter
   * @return array
   */
  static function mod_explode($text, $delimiter=',')
  {
    return explode($delimiter, $text);
  }
  
  /**
   * ternary variable modifier.
   * Usage :
   *    - {$variable|ternary:'yes':'no'}
   *
   * @param mixed $param
   * @param mixed $true
   * @param mixed $false
   * @return mixed
   */
  static function mod_ternary($param, $true, $false)
  {
    return $param ? $true : $false;
  }

  /**
   * The "html_head" block allows to add content just before
   * </head> element in the output after the head has been parsed.
   *
   * @param array $params (unused)
   * @param string $content
   */
  function block_html_head($params, $content)
  {
    $content = trim($content);
    if ( !empty($content) )
    { // second call
      $this->html_head_elements[] = $content;
    }
  }

  /**
   * The "html_style" block allows to add CSS juste before
   * </head> element in the output after the head has been parsed.
   *
   * @param array $params (unused)
   * @param string $content
   */
  function block_html_style($params, $content)
  {
    $content = trim($content);
    if ( !empty($content) )
    { // second call
      $this->html_style .= "\n".$content;
    }
  }

  /**
   * The "define_derivative" function allows to define derivative from tpl file.
   * It assigns a DerivativeParams object to _name_ template variable.
   *
   * @param array $params
   *    - name (required)
   *    - type (optional)
   *    - width (required if type is empty)
   *    - height (required if type is empty)
   *    - crop (optional, used if type is empty)
   *    - min_height (optional, used with crop)
   *    - min_height (optional, used with crop)
   * @param Smarty $smarty
   */
  function func_define_derivative($params, $smarty)
  {
    !empty($params['name']) or fatal_error('define_derivative missing name');
    if (isset($params['type']))
    {
      $derivative = ImageStdParams::get_by_type($params['type']);
      $smarty->assign( $params['name'], $derivative);
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

    $smarty->assign( $params['name'], ImageStdParams::get_custom($w, $h, $crop, $minw, $minh) );
  }

  /**
   * The "combine_script" functions allows inclusion of a javascript file in the current page.
   * The engine will combine several js files into a single one.
   *
   * @param array $params
   *   - id (required)
   *   - path (required)
   *   - load (optional) 'header', 'footer' or 'async'
   *   - require (optional) comma separated list of script ids required to be loaded
   *     and executed before this one
   *   - version (optional) used to force a browser refresh
   */
  function func_combine_script($params)
  {
    if (!isset($params['id']))
    {
      trigger_error("combine_script: missing 'id' parameter", E_USER_ERROR);
    }
    $load = 0;
    if (isset($params['load']))
    {
      switch ($params['load'])
      {
        case 'header': break;
        case 'footer': $load=1; break;
        case 'async': $load=2; break;
        default: trigger_error("combine_script: invalid 'load' parameter", E_USER_ERROR);
      }
    }

    $this->scriptLoader->add( $params['id'], $load,
      empty($params['require']) ? array() : explode( ',', $params['require'] ),
      @$params['path'],
      isset($params['version']) ? $params['version'] : 0,
      @$params['template']);
  }

  /**
   * The "get_combined_scripts" function returns HTML tag of combined scripts.
   * It can returns a placeholder for delayed JS files combination and minification.
   *
   * @param array $params
   *    - load (required)
   */
  function func_get_combined_scripts($params)
  {
    if (!isset($params['load']))
    {
      trigger_error("get_combined_scripts: missing 'load' parameter", E_USER_ERROR);
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

  /**
   * Returns clean relative URL to script file.
   *
   * @param Combinable $script
   * @return string
   */
  private static function make_script_src($script)
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
    $ret = trigger_change('combined_script', $ret, $script);
    return embellish_url($ret);
  }

  /**
   * The "footer_script" block allows to add runtime script in the HTML page.
   *
   * @param array $params
   *    - require (optional) comma separated list of script ids
   * @param string $content
   */
  function block_footer_script($params, $content)
  {
    $content = trim($content);
    if ( !empty($content) )
    { // second call

      $this->scriptLoader->add_inline(
        $content,
        empty($params['require']) ? array() : explode(',', $params['require'])
      );
    }
  }

  /**
   * The "combine_css" function allows inclusion of a css file in the current page.
   * The engine will combine several css files into a single one.
   *
   * @param array $params
   *    - id (optional) used to deal with multiple inclusions from plugins
   *    - path (required)
   *    - version (optional) used to force a browser refresh
   *    - order (optional)
   *    - template (optional) set to true to allow smarty syntax in the css file
   */
  function func_combine_css($params)
  {
    if (empty($params['path']))
    {
      fatal_error('combine_css missing path');
    }

    if (!isset($params['id']))
    {
      $params['id'] = md5($params['path']);
    }

    $this->cssLoader->add($params['id'], $params['path'], isset($params['version']) ? $params['version'] : 0, (int)@$params['order'], (bool)@$params['template']);
  }

  /**
   * The "get_combined_scripts" function returns a placeholder for delayed
   * CSS files combination and minification.
   *
   * @param array $params (unused)
   */
  function func_get_combined_css($params)
  {
    return self::COMBINED_CSS_TAG;
  }

  /**
   * Declares a Smarty prefilter from a plugin, allowing it to modify template
   * source before compilation and without changing core files.
   * They will be processed by weight ascending.
   * @see http://www.smarty.net/manual/en/advanced.features.prefilters.php
   *
   * @param string $handle
   * @param Callable $callback
   * @param int $weight
   */
  function set_prefilter($handle, $callback, $weight=50)
  {
    $this->external_filters[$handle][$weight][] = array('pre', $callback);
    ksort($this->external_filters[$handle]);
  }

  /**
   * Declares a Smarty postfilter.
   * They will be processed by weight ascending.
   * @see http://www.smarty.net/manual/en/advanced.features.postfilters.php
   *
   * @param string $handle
   * @param Callable $callback
   * @param int $weight
   */
  function set_postfilter($handle, $callback, $weight=50)
  {
    $this->external_filters[$handle][$weight][] = array('post', $callback);
    ksort($this->external_filters[$handle]);
  }

  /**
   * Declares a Smarty outputfilter.
   * They will be processed by weight ascending.
   * @see http://www.smarty.net/manual/en/advanced.features.outputfilters.php
   *
   * @param string $handle
   * @param Callable $callback
   * @param int $weight
   */
  function set_outputfilter($handle, $callback, $weight=50)
  {
    $this->external_filters[$handle][$weight][] = array('output', $callback);
    ksort($this->external_filters[$handle]);
  }

  /**
   * Register the filters for the tpl file.
   *
   * @param string $handle
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
          $this->smarty->registerFilter($type, $callback);
        }
      }
      $this->smarty->compile_id .= '.'.base_convert(crc32($compile_id), 10, 36);
    }
  }

  /**
   * Unregister the filters for the tpl file.
   *
   * @param string $handle
   */
  function unload_external_filters($handle)
  {
    if (isset($this->external_filters[$handle]))
    {
      foreach ($this->external_filters[$handle] as $filters)
      {
        foreach ($filters as $filter)
        {
          list($type, $callback) = $filter;
          $this->smarty->unregisterFilter($type, $callback);
        }
      }
    }
  }

  /**
   * @toto : description of Template::prefilter_white_space
   *
   * @param string $source
   * @param Smarty $smarty
   * @param return string
   */
  static function prefilter_white_space($source, $smarty)
  {
    $ld = $smarty->left_delimiter;
    $rd = $smarty->right_delimiter;
    $ldq = preg_quote($ld, '#');
    $rdq = preg_quote($rd, '#');

    $regex = array();
    $tags = array('if','foreach','section','footer_script');
    foreach($tags as $tag)
    {
      $regex[] = "#^[ \t]+($ldq$tag"."[^$ld$rd]*$rdq)\s*$#m";
      $regex[] = "#^[ \t]+($ldq/$tag$rdq)\s*$#m";
    }
    $tags = array('include','else','combine_script','html_head');
    foreach($tags as $tag)
    {
      $regex[] = "#^[ \t]+($ldq$tag"."[^$ld$rd]*$rdq)\s*$#m";
    }
    $source = preg_replace( $regex, "$1", $source);
    return $source;
  }

  /**
   * Postfilter used when $conf['compiled_template_cache_language'] is true.
   *
   * @param string $source
   * @param Smarty $smarty
   * @param return string
   */
  static function postfilter_language($source, $smarty)
  {
    // replaces echo PHP_STRING_LITERAL; with the string literal value
    $source = preg_replace_callback(
      '/\\<\\?php echo ((?:\'(?:(?:\\\\.)|[^\'])*\')|(?:"(?:(?:\\\\.)|[^"])*"));\\?\\>\\n/',
      create_function('$matches', 'eval(\'$tmp=\'.$matches[1].\';\');return $tmp;'),
      $source);
    return $source;
  }

  /**
   * Prefilter used to add theme local CSS files.
   *
   * @param string $source
   * @param Smarty $smarty
   * @param return string
   */
  static function prefilter_local_css($source, $smarty)
  {
    $css = array();
    foreach ($smarty->getTemplateVars('themes') as $theme)
    {
      $f = PWG_LOCAL_DIR.'css/'.$theme['id'].'-rules.css';
      if (file_exists(PHPWG_ROOT_PATH.$f))
      {
        $css[] = "{combine_css path='$f' order=10}";
      }
    }
    $f = PWG_LOCAL_DIR.'css/rules.css';
    if (file_exists(PHPWG_ROOT_PATH.$f))
    {
      $css[] = "{combine_css path='$f' order=10}";
    }

    if (!empty($css))
    {
      $source = str_replace("{get_combined_css}", implode( "\n", $css )."\n{get_combined_css}", $source);
    }

    return $source;
  }

  /**
   * Loads the configuration file from a theme directory and returns it.
   *
   * @param string $dir
   * @return array
   */
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

  /**
   * Registers a button to be displayed on picture page.
   *
   * @param string $content
   * @param int $rank
   */
  function add_picture_button($content, $rank=BUTTONS_RANK_NEUTRAL)
  {
    $this->picture_buttons[$rank][] = $content;
  }

  /**
   * Registers a button to be displayed on index pages.
   *
   * @param string $content
   * @param int $rank
   */
  function add_index_button($content, $rank=BUTTONS_RANK_NEUTRAL)
  {
    $this->index_buttons[$rank][] = $content;
  }

  /**
   * Assigns PLUGIN_PICTURE_BUTTONS template variable with registered picture buttons.
   */
  function parse_picture_buttons()
  {
    if (!empty($this->picture_buttons))
    {
      ksort($this->picture_buttons);
      $buttons = array();
      foreach ($this->picture_buttons as $k => $row)
      {
        $buttons = array_merge($buttons, $row);
      }
      $this->assign('PLUGIN_PICTURE_BUTTONS', $buttons);

      // only for PHP 5.3
      // $this->assign('PLUGIN_PICTURE_BUTTONS',
          // array_reduce(
            // $this->picture_buttons,
            // create_function('$v,$w', 'return array_merge($v, $w);'),
            // array()
          // ));
    }
  }

  /**
   * Assigns PLUGIN_INDEX_BUTTONS template variable with registered index buttons.
   */
  function parse_index_buttons()
  {
    if (!empty($this->index_buttons))
    {
      ksort($this->index_buttons);
      $buttons = array();
      foreach ($this->index_buttons as $k => $row)
      {
        $buttons = array_merge($buttons, $row);
      }
      $this->assign('PLUGIN_INDEX_BUTTONS', $buttons);

      // only for PHP 5.3
      // $this->assign('PLUGIN_INDEX_BUTTONS',
          // array_reduce(
            // $this->index_buttons,
            // create_function('$v,$w', 'return array_merge($v, $w);'),
            // array()
          // ));
    }
  }
}


/**
 * This class contains basic functions that can be called directly from the
 * templates in the form $pwg->l10n('edit')
 */
class PwgTemplateAdapter
{
  /**
   * @deprecated use "translate" modifier
   */
  function l10n($text)
  {
    return l10n($text);
  }

  /**
   * @deprecated use "translate_dec" modifier
   */
  function l10n_dec($s, $p, $v)
  {
    return l10n_dec($s, $p, $v);
  }

  /**
   * @deprecated use "translate" or "sprintf" modifier
   */
  function sprintf()
  {
    $args = func_get_args();
    return call_user_func_array('sprintf',  $args );
  }

  /**
   * @param string $type
   * @param array $img
   * @return DerivativeImage
   */
  function derivative($type, $img)
  {
    return new DerivativeImage($type, $img);
  }

  /**
   * @param string $type
   * @param array $img
   * @return string
   */
  function derivative_url($type, $img)
  {
    return DerivativeImage::url($type, $img);
  }
}


/**
 * A Combinable represents a JS or CSS file ready for cobination and minification.
 */
class Combinable
{
  /** @var string */
  public $id;
  /** @var string */
  public $path;
  /** @var string */
  public $version;
  /** @var bool */
  public $is_template;

  /**
   * @param string $id
   * @param string $path
   * @param string $version
   */
  function __construct($id, $path, $version=0)
  {
    $this->id = $id;
    $this->set_path($path);
    $this->version = $version;
    $this->is_template = false;
  }

  /**
   * @param string $path
   */
  function set_path($path)
  {
    if (!empty($path))
      $this->path = $path;
  }

  /**
   * @return bool
   */
  function is_remote()
  {
    return url_is_remote($this->path) || strncmp($this->path, '//', 2)==0;
  }
}

/**
 * Implementation of Combinable for JS files.
 */
final class Script extends Combinable
{
  /** @var int 0,1,2 */
  public $load_mode;
  /** @var array */
  public $precedents;
  /** @var array */
  public $extra;

  /**
   * @param int 0,1,2
   * @param string $id
   * @param string $path
   * @param string $version
   * @param array $precedents
   */
  function __construct($load_mode, $id, $path, $version=0, $precedents=array())
  {
    parent::__construct($id, $path, $version);
    $this->load_mode = $load_mode;
    $this->precedents = $precedents;
    $this->extra = array();
  }
}

/**
 * Implementation of Combinable for CSS files.
 */
final class Css extends Combinable
{
  /** @var int */
  public $order;

  /**
   * @param string $id
   * @param string $path
   * @param string $version
   * @param int $order
   */
  function __construct($id, $path, $version=0, $order=0)
  {
    parent::__construct($id, $path, $version);
    $this->order = $order;
  }
}


/**
 * Manages a list of CSS files and combining them in a unique file.
 */
class CssLoader
{
  /** @param Css[] */
  private $registered_css;
  /** @param int used to keep declaration order */
  private $counter;

  function __construct()
  {
    $this->clear();
  }

  function clear()
  {
    $this->registered_css = array();
    $this->counter = 0;
  }

  /**
   * @return Combinable[] array of combined CSS.
   */
  function get_css()
  {
    uasort($this->registered_css, array('CssLoader', 'cmp_by_order'));
    $combiner = new FileCombiner('css', $this->registered_css);
    return $combiner->combine();
  }

  /**
   * Callback for CSS files sorting.
   */
  private static function cmp_by_order($a, $b)
  {
    return $a->order - $b->order;
  }

  /**
   * Adds a new file, if a file with the same $id already exsists, the one with
   * the higher $order or higher $version is kept.
   *
   * @param string $id
   * @param string $path
   * @param string $version
   * @param int $order
   * @param bool $is_template
   */
  function add($id, $path, $version=0, $order=0, $is_template=false)
  {
    if (!isset($this->registered_css[$id]))
    {
      // costum order as an higher impact than declaration order
      $css = new Css($id, $path, $version, $order*1000+$this->counter);
      $css->is_template = $is_template;
      $this->registered_css[$id] = $css;
      $this->counter++;
    }
    else
    {
      $css = $this->registered_css[$id];
      if ($css->order<$order*1000 || version_compare($css->version, $version)<0)
      {
        unset($this->registered_css[$id]);
        $this->add($id, $path, $version, $order, $is_template);
      }
    }
  }
}


/**
 * Manage a list of required scripts for a page, by optimizing their loading location (head, footer, async)
 * and later on by combining them in a unique file respecting at the same time dependencies.
 */
class ScriptLoader
{
  /** @var Script[] */
  private $registered_scripts;
  /** @var string[] */
  public $inline_scripts;

  /** @var bool */
  private $did_head;
  /** @var bool */
  private $head_done_scripts;
  /** @var bool */
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

  /**
   * @return bool
   */
  function did_head()
  {
    return $this->did_head;
  }

  /**
   * @return Script[]
   */
  function get_all()
  {
    return $this->registered_scripts;
  }

  /**
   * @param string $code
   * @param string[] $require
   */
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

  /**
   * @param string $id
   * @param int $load_mode
   * @param string[] $require
   * @param string $path
   * @param string $version
   */
  function add($id, $load_mode, $require, $path, $version=0, $is_template=false)
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
      $script->is_template = $is_template;
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

  /**
   * Returns combined scripts loaded in header.
   *
   * @return Combinable[]
   */
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

  /**
   * Returns combined scripts loaded in footer.
   *
   * @return Combinable[]
   */
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

  /**
   * @param Script[] $scripts
   * @param int $load_mode
   * @return Combinable[]
   */
  private static function do_combine($scripts, $load_mode)
  {
    $combiner = new FileCombiner('js', $scripts);
    return $combiner->combine();
  }

  /**
   * Checks dependencies among Scripts.
   * Checks that if B depends on A, then B->load_mode >= A->load_mode in order to respect execution order.
   *
   * @param Script[] $scripts
   */
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

  /**
   * Fill a script dependancies with the known jQuery UI scripts.
   *
   * @param string $id in FileCombiner::$known_paths
   * @param Script $script
   */
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

  /**
   * Add a known jQuery UI script to loaded scripts.
   *
   * @param string $id in FileCombiner::$known_paths
   * @param int $load_mode
   * @return bool
   */
  private function load_known_required_script($id, $load_mode)
  {
    if ( isset(self::$known_paths[$id]) or strncmp($id, 'jquery.ui.', 10)==0  )
    {
      $this->add($id, $load_mode, array(), null);
      return true;
    }
    return false;
  }

  /**
   * Compute script order depending on dependencies.
   * Assigned to $script->extra['order'].
   *
   * @param string $script_id
   * @param int $recursion_limiter
   * @return int
   */
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

  /**
   * Callback for scripts sorter.
   */
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


/**
 * Allows merging of javascript and css files into a single one.
 */
final class FileCombiner
{
  /** @var string 'js' or 'css' */
  private $type;
  /** @var bool */
  private $is_css;
  /** @var Combinable[] */
  private $combinables;

  /**
   * @param string $type 'js' or 'css'
   * @param Combinable[] $combinables
   */
  function __construct($type, $combinables=array())
  {
    $this->type = $type;
    $this->is_css = $type=='css';
    $this->combinables = $combinables;
  }

  /**
   * Deletes all combined files from cache directory.
   */
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

  /**
   * @param Combinable|Combinable[] $combinable
   */
  function add($combinable)
  {
    if (is_array($combinable))
    {
      $this->combinables = array_merge($this->combinables, $combinable);
    }
    else
    {
      $this->combinables[] = $combinable;
    }
  }

  /**
   * @return Combinable[]
   */
  function combine()
  {
    global $conf;
    $force = false;
    if (is_admin() && ($this->is_css || !$conf['template_compile_check']) )
    {
      $force = (isset($_SERVER['HTTP_CACHE_CONTROL']) && strpos($_SERVER['HTTP_CACHE_CONTROL'], 'max-age=0') !== false)
        || (isset($_SERVER['HTTP_PRAGMA']) && strpos($_SERVER['HTTP_PRAGMA'], 'no-cache'));
    }

    $result = array();
    $pending = array();
    $ini_key = $this->is_css ? array(get_absolute_root_url(false)): array(); //because for css we modify bg url;
    $key = $ini_key;

    foreach ($this->combinables as $combinable)
    {
      if ($combinable->is_remote())
      {
        $this->flush_pending($result, $pending, $key, $force);
        $key = $ini_key;
        $result[] = $combinable;
        continue;
      }
      elseif (!$conf['template_combine_files'])
      {
        $this->flush_pending($result, $pending, $key, $force);
        $key = $ini_key;
      }

      $key[] = $combinable->path;
      $key[] = $combinable->version;
      if ($conf['template_compile_check'])
        $key[] = filemtime( PHPWG_ROOT_PATH . $combinable->path );
      $pending[] = $combinable;
    }
    $this->flush_pending($result, $pending, $key, $force);
    return $result;
  }

  /**
   * Process a set of pending files.
   *
   * @param array &$result
   * @param array &$pending
   * @param string[] $key
   * @param bool $force
   */
  private function flush_pending(&$result, &$pending, $key, $force)
  {
    if (count($pending)>1)
    {
      $key = join('>', $key);
      $file = PWG_COMBINED_DIR . base_convert(crc32($key),10,36) . '.' . $this->type;
      if ($force || !file_exists(PHPWG_ROOT_PATH.$file) )
      {
        $output = '';
        $header = '';
        foreach ($pending as $combinable)
        {
          $output .= "/*BEGIN $combinable->path */\n";
          $output .= $this->process_combinable($combinable, true, $force, $header);
          $output .= "\n";
        }
        $output = "/*BEGIN header */\n" . $header . "\n" . $output;
        mkgetdir( dirname(PHPWG_ROOT_PATH.$file) );
        file_put_contents( PHPWG_ROOT_PATH.$file, $output );
        @chmod(PHPWG_ROOT_PATH.$file, 0644);
      }
      $result[] = new Combinable("combi", $file, false);
    }
    elseif ( count($pending)==1)
    {
      $header = '';
      $this->process_combinable($pending[0], false, $force, $header);
      $result[] = $pending[0];
    }
    $key = array();
    $pending = array();
  }

  /**
   * Process one combinable file.
   *
   * @param Combinable $combinable
   * @param bool $return_content
   * @param bool $force
   * @param string $header CSS directives that must appear first in
   *                       the minified file (only used when
   *                       $return_content===true)
   * @return null|string
   */
  private function process_combinable($combinable, $return_content, $force, &$header)
  {
    global $conf;
    if ($combinable->is_template)
    {
      if (!$return_content)
      {
        $key = array($combinable->path, $combinable->version);
        if ($conf['template_compile_check'])
          $key[] = filemtime( PHPWG_ROOT_PATH . $combinable->path );
        $file = PWG_COMBINED_DIR . 't' . base_convert(crc32(implode(',',$key)),10,36) . '.' . $this->type;
        if (!$force && file_exists(PHPWG_ROOT_PATH.$file) )
        {
          $combinable->path = $file;
          $combinable->version = false;
          return;
        }
      }

      global $template;
      $handle = $this->type. '.' .$combinable->id;
      $template->set_filename($handle, realpath(PHPWG_ROOT_PATH.$combinable->path));
      trigger_notify( 'combinable_preparse', $template, $combinable, $this); //allow themes and plugins to set their own vars to template ...
      $content = $template->parse($handle, true);

      if ($this->is_css)
        $content = self::process_css($content, $combinable->path, $header );
      else
        $content = self::process_js($content, $combinable->path );

      if ($return_content)
        return $content;
      file_put_contents( PHPWG_ROOT_PATH.$file, $content );
      $combinable->path = $file;
    }
    elseif ($return_content)
    {
      $content = file_get_contents(PHPWG_ROOT_PATH . $combinable->path);
      if ($this->is_css)
        $content = self::process_css($content, $combinable->path, $header );
      else
        $content = self::process_js($content, $combinable->path );
      return $content;
    }
  }

  /**
   * Process a JS file.
   *
   * @param string $js file content
   * @param string $file
   * @return string
   */
  private static function process_js($js, $file)
  {
    if (strpos($file, '.min')===false and strpos($file, '.packed')===false )
    {
      require_once(PHPWG_ROOT_PATH.'include/jshrink.class.php');
      try { $js = JShrink_Minifier::minify($js); } catch(Exception $e) {}
    }
    return trim($js, " \t\r\n;").";\n";
  }

  /**
   * Process a CSS file.
   *
   * @param string $css file content
   * @param string $file
   * @param string $header CSS directives that must appear first in
   *                       the minified file.
   * @return string
   */
  private static function process_css($css, $file, &$header)
  {
    $css = self::process_css_rec($css, dirname($file), $header);
    if (strpos($file, '.min')===false and version_compare(PHP_VERSION, '5.2.4', '>='))
    {
      require_once(PHPWG_ROOT_PATH.'include/cssmin.class.php');
      $css = CssMin::minify($css, array('Variables'=>false));
    }
    $css = trigger_change('combined_css_postfilter', $css);
    return $css;
  }

  /**
   * Resolves relative links in CSS file.
   *
   * @param string $css file content
   * @param string $dir
   * @param string $header CSS directives that must appear first in
   *                       the minified file.
   * @return string
   */
  private static function process_css_rec($css, $dir, &$header)
  {
    static $PATTERN_URL = "#url\(\s*['|\"]{0,1}(.*?)['|\"]{0,1}\s*\)#";
    static $PATTERN_IMPORT = "#@import\s*['|\"]{0,1}(.*?)['|\"]{0,1};#";

    if (preg_match_all($PATTERN_URL, $css, $matches, PREG_SET_ORDER))
    {
      $search = $replace = array();
      foreach ($matches as $match)
      {
        if ( !url_is_remote($match[1]) && $match[1][0] != '/' && strpos($match[1], 'data:image/')===false)
        {
          $relative = $dir . "/$match[1]";
          $search[] = $match[0];
          $replace[] = 'url('.embellish_url(get_absolute_root_url(false).$relative).')';
        }
      }
      $css = str_replace($search, $replace, $css);
    }

    if (preg_match_all($PATTERN_IMPORT, $css, $matches, PREG_SET_ORDER))
    {
      $search = $replace = array();
      
      foreach ($matches as $match)
      {
        $search[] = $match[0];
        
        if (
          strpos($match[1], '..') !== false // Possible attempt to get out of Piwigo's dir
          or strpos($match[1], '://') !== false // Remote URL
          or !is_readable(PHPWG_ROOT_PATH . $dir . '/' . $match[1])
          )
        {
          // If anything is suspicious, don't try to process the
          // @import. Since @import need to be first and we are
          // concatenating several CSS files, remove it from here and return
          // it through $header.
          $header .= $match[0];
          $replace[] = '';
        }
        else
        {
          $sub_css = file_get_contents(PHPWG_ROOT_PATH . $dir . "/$match[1]");
          $replace[] = self::process_css_rec($sub_css, dirname($dir . "/$match[1]"), $header);
        }
      }
      $css = str_replace($search, $replace, $css);
    }
    return $css;
  }
}

?>
