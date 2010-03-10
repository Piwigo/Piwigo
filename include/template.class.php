<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

// migrate lang:XXX
//    sed "s/{lang:\([^}]\+\)}/{\'\1\'|@translate}/g" my_template.tpl
// migrate change root level vars {XXX}
//    sed "s/{pwg_root}/{ROOT_URL}/g" my_template.tpl
// migrate change root level vars {XXX}
//    sed "s/{\([a-zA-Z_]\+\)}/{$\1}/g" my_template.tpl
// migrate all
//    cat my_template.tpl | sed "s/{lang:\([^}]\+\)}/{\'\1\'|@translate}/g" | sed "s/{pwg_root}/{ROOT_URL}/g" | sed "s/{\([a-zA-Z_]\+\)}/{$\1}/g"


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

  function Template($root = ".", $theme= "")
  {
    global $conf, $lang_info;

    $this->smarty = new Smarty;
    $this->smarty->debugging = $conf['debug_template'];
    $this->smarty->compile_check=$conf['template_compile_check'];

    $compile_dir = $conf['local_data_dir'].'/templates_c';
    mkgetdir( $compile_dir );

    $this->smarty->compile_dir = $compile_dir;

    $this->smarty->assign_by_ref( 'pwg', new PwgTemplateAdapter() );
    $this->smarty->register_modifier( 'translate', array('Template', 'mod_translate') );
    $this->smarty->register_modifier( 'explode', array('Template', 'mod_explode') );
    $this->smarty->register_modifier( 'get_extent', array(&$this, 'get_extent') );
    $this->smarty->register_block('html_head', array(&$this, 'block_html_head') );
    $this->smarty->register_function('known_script', array(&$this, 'func_known_script') );
    $this->smarty->register_prefilter( array('Template', 'prefilter_white_space') );
    if ( $conf['compiled_template_cache_language'] )
    {
      $this->smarty->register_prefilter( array('Template', 'prefilter_language') );
    }

    if ( !empty($theme) )
    {
      include($root.'/theme/'.$theme.'/themeconf.inc.php');
      $this->smarty->assign('themeconf', $themeconf);
    }

    $this->smarty->assign('lang_info', $lang_info);

    $this->set_template_dir($root);

    if (!defined('IN_ADMIN') and isset($conf['extents_for_templates']))
    {
      $tpl_extents = unserialize($conf['extents_for_templates']);
      $this->set_extents($tpl_extents, './template-extension/', true);
    }
  }

  /**
   * Sets the template root directory for this Template object.
   * Revised from Piwigo 2.1 to add modeling support
   */
  function set_template_dir($dir)
  {
    $modeling = $this->get_themeconf('modeling');
	if (!defined('IN_ADMIN') and ($modeling !== false)) 
	{ // Modeling is active only on gallery side and never in admin
	  // Set the normal directory
      $this->smarty->template_dir = array($dir);
	  // Modeling by theme parameter
	  $model = './template/' . $modeling;
      if ( $model != './template/' and is_dir($model))
	  {
		$this->smarty->template_dir[] = $model;
      }
	  // Default template directory
	  $this->smarty->template_dir[] = './template/default';
	}
    else
	{
      $this->smarty->template_dir = $dir;
	}
    $real_dir = realpath($dir);
    $compile_id = crc32( $real_dir===false ? $dir : $real_dir);
    $this->smarty->compile_id = base_convert($compile_id, 10, 36 );
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
  function set_extent($filename, $param, $dir='', $overwrite=true)
  {
    return $this->set_extents(array($filename => $param), $dir, $overwrite);
  }

  /**
   * Sets template extentions filenames for handles.
   * $filename_array should be an hash of filename => array( handle, param) or filename => handle
   */
  function set_extents($filename_array, $dir='', $overwrite=true)
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
        $tpl = $value[2];
      }
      elseif (is_string($value))
      {
        $handle = $value;
        $param = 'N/A';
        $tpl = 'N/A';
      }
      else
      {
        return false;
      }

      if ((stripos(implode('',array_keys($_GET)), '/'.$param) !== false or $param == 'N/A')
        and (preg_match('/'.preg_quote($tpl,'/').'$/', $this->get_template_dir()) or $tpl == 'N/A')
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

?>
