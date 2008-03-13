<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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


require 'smarty/libs/Smarty.class.php';

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

  var $_old;

  var $output = '';

  // Hash of filenames for each template handle.
  var $files = array();

  function Template($root = ".", $theme= "")
  {
    global $conf;

    $this->smarty = new Smarty;
    $this->smarty->debugging = $conf['debug_template'];
    //$this->smarty->force_compile = true;

    if ( isset($conf['compiled_template_dir'] ) )
    {
      $compile_dir = $conf['compiled_template_dir'];
    }
    else
    {
      $compile_dir = $conf['local_data_dir'];
      if ( !is_dir($compile_dir) )
      {
        mkdir( $compile_dir, 0777);
        file_put_contents($compile_dir.'/index.htm', '');
      }
      $compile_dir .= '/templates_c';
    }
    if ( !is_dir($compile_dir) )
    {
      mkdir( $compile_dir, 0777 );
      file_put_contents($compile_dir.'/index.htm', '');
    }

    $this->smarty->compile_dir = $compile_dir;

    $this->smarty->register_function( 'lang', array('Template', 'fn_l10n') );

    $this->smarty->assign_by_ref( 'pwg', new PwgTemplateAdapter() );
    $this->smarty->register_modifier( 'translate', array('Template', 'mod_translate') );

    if ( !empty($theme) )
    {
      include($root.'/theme/'.$theme.'/themeconf.inc.php');
      $this->smarty->assign('themeconf', $themeconf);
    }

    $this->_old = & new TemplateOld($root, $theme);

    $this->set_template_dir($root);
  }

  /**
   * Sets the template root directory for this Template object.
   */
  function set_template_dir($dir)
  {
    $this->_old->set_rootdir($dir);
    $this->smarty->template_dir = $dir;

    $real_dir = realpath($dir);
    $compile_id = crc32( $real_dir===false ? $dir : $real_dir);
    $this->smarty->compile_id = sprintf('%08X', $compile_id );
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
      file_put_contents($this->smarty->compile_dir.'/index.htm', '');
  }

  /** DEPRECATED */
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
        unset( $this->files[$handle] );
      else
        $this->files[$handle] = $filename;
    }
    return true;
  }

  /**
   * DEPRECATED - backward compatibility only; use assign
   */
  function assign_vars($vararray)
  {
    is_array( $vararray ) || die('assign_vars parameter not array');
    $this->assign( $vararray );
  }

  /**
   * DEPRECATED - backward compatibility only; use assign
   */
  function assign_var($varname, $varval)
  {
    !is_array( $varname ) || die('assign_var parameter name is array');
    $this->assign( $varname, $varval );
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

  /**
   * DEPRECATED - backward compatibility only
   */
  function assign_block_vars($blockname, $vararray)
  {
    if (strstr($blockname, '.')!==false)
    {
      $blocks = explode('.', $blockname);
      $blockcount = sizeof($blocks) - 1;
      $root_var = & $this->smarty->get_template_vars();

      $str = '$root_var';
      for ($i = 0; $i < $blockcount; $i++)
      {
        $str .= '[\'' . $blocks[$i] . '\']';
        eval('$lastiteration = isset('.$str.') ? sizeof('.$str.')-1:0;');
        $str .= '[' . $lastiteration . ']';
      }
      $str .= '[\'' . $blocks[$blockcount] . '\'][] = $vararray;';
      eval($str);
    }
    else
      $this->smarty->append( $blockname, $vararray );

    $this->_old->assign_block_vars($blockname, $vararray);
  }

  /**
   * DEPRECATED - backward compatibility only
   */
  function merge_block_vars($blockname, $vararray)
  {
    if (strstr($blockname, '.')!==false)
    {
      $blocks = explode('.', $blockname);
      $blockcount = count($blocks);
      $root_var = & $this->smarty->get_template_vars();

      $str = '$root_var';
      for ($i = 0; $i < $blockcount; $i++)
      {
        $str .= '[\'' . $blocks[$i] . '\']';
        eval('$lastiteration = isset('.$str.') ? sizeof('.$str.')-1:-1;');
        if ($lastiteration==-1)
        {
          return false;
        }
        $str .= '[' . $lastiteration . ']';
      }
      $str = $str.'=array_merge('.$str.', $vararray);';
      eval($str);
    }
    else
      $this->smarty->append( $blockname, $vararray, true );

    $this->_old->merge_block_vars($blockname, $vararray);
    return true;
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
      die("Template->parse(): Couldn't load template file for handle $handle");
    }

    $is_new = true;
    $params = array('resource_name' => $this->files[$handle], 'quiet'=>true, 'get_source'=>true);
    if ( $this->smarty->_fetch_resource_info($params) )
    {
      if (!preg_match('~{(/(if|section|foreach))|\$[a-zA-Z_]+}~', @$params['source_content']) )
        $is_new = false;
    }

    if ($is_new)
    {
      $this->smarty->assign( 'ROOT_URL', get_root_url() );
      $this->smarty->assign( 'TAG_INPUT_ENABLED',
        ((is_adviser()) ? 'disabled="disabled" onclick="return false;"' : ''));
      $v = $this->smarty->fetch($this->files[$handle], null, null, false);
    }
    else
    {
      $this->_old->assign_vars(array('TAG_INPUT_ENABLED' =>
        ((is_adviser()) ? 'disabled onclick="return false;"' : '')));
      $this->_old->set_filename( $handle, $this->files[$handle] );
      $v = $this->_old->parse($handle, true);
    }
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
    echo $this->output;
    $this->output='';

  }


  /** flushes the output */
  function p()
  {
    $start = get_moment();

    echo $this->output;
    $this->output='';

    if ($this->smarty->debugging)
    {
      global $t2;
      $this->smarty->assign(
        array(
        'AAAA_DEBUG_OUTPUT_TIME__' => get_elapsed_time($start, get_moment()),
        'AAAA_DEBUG_TOTAL_TIME__' => get_elapsed_time($t2, get_moment())
        )
        );
      require_once(SMARTY_CORE_DIR . 'core.display_debug_console.php');
      echo smarty_core_display_debug_console(null, $this->smarty);
    }
  }

  /**
   * Root-level variable concatenation. Appends a  string to an existing
   * variable assignment with the same name.
   */
  function concat_var($tpl_var, $value)
  {
    $old_val = & $this->smarty->get_template_vars($tpl_var);
    if ( isset($old_val) )
    {
      $old_val .= $value;
      $this->_old->concat_var( $tpl_var, $value );
    }
    else
    {
      $this->assign($tpl_var, $value);
    }
  }

  /** see smarty assign http://www.smarty.net/manual/en/api.assign.php */
  function assign($tpl_var, $value = null)
  {
    $this->smarty->assign( $tpl_var, $value );

    if ( is_array($tpl_var) )
      $this->_old->assign_vars( $tpl_var );
    else
      $this->_old->assign_var( $tpl_var, $value );
  }

  /** see smarty append http://www.smarty.net/manual/en/api.append.php */
  function append($tpl_var, $value=null, $merge=false)
  {
    $this->smarty->append( $tpl_var, $value, $merge );
  }

  /** see smarty get_template_vars http://www.smarty.net/manual/en/api.get_template_vars.php */
  function &get_template_vars($name=null)
  {
    return $this->smarty->get_template_vars( $name );
  }

  /** see smarty append http://www.smarty.net/manual/en/api.clear_assign.php */
  function clear_assign($tpl_var)
  {
    $this->smarty->clear_assign( $tpl_var );
  }

  /*static*/ function fn_l10n($params, &$smarty)
  {
    return l10n($params['t']);
  }

  /**
   * translate variable modifiers - translates a text to the currently loaded
   * language
   */
  /*static*/ function mod_translate($text)
  {
    return l10n($text);
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
