<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

class BlockManager
{
  protected $id;
  protected $registered_blocks=array();
  protected $display_blocks = array();

  public function BlockManager($id)
  {
    $this->id = $id;
  }

  /** triggers an action that allows implementors of menu blocks to register the blocks*/
  public function load_registered_blocks()
  {
    trigger_action('blockmanager_register_blocks', array(&$this) );
  }

  public function get_id()
  {
    return $this->id;
  }

  public function get_registered_blocks()
  {
    return $this->registered_blocks;
  }

  /** registers a block with this menu. usually called as a result of menubar_register_blocks action
   * @param MenuBlock block
  */
  public function register_block(&$block)
  {
    if ( isset($this->registered_blocks[$block->get_id()] ) )
    {
      trigger_error("Block '".$block->get_id()."' is already registered", E_USER_WARNING);
      return false;
    }
    $this->registered_blocks[$block->get_id()] = &$block;
    return true;
  }

  /** performs one time preparation of registered blocks for display;
   * triggers the action menubar_prepare_display where implementors can
   * reposition or hide blocks
  */
  public function prepare_display()
  {
    global $conf;
    $conf_id = 'blk_'.$this->id;
    $mb_conf = isset($conf[$conf_id]) ? $conf[$conf_id] : array();
    if ( !is_array($mb_conf) )
      $mb_conf = @unserialize($mb_conf);

    $idx = 1;
    foreach( $this->registered_blocks as $id => $block )
    {
      $pos = isset( $mb_conf[$id] ) ? $mb_conf[$id] : $idx*50;
      if ( $pos>0 )
      {
        $this->display_blocks[$id] = new DisplayBlock($block);
        $this->display_blocks[$id]->set_position($pos);
      }
      $idx++;
    }
    $this->sort_blocks();
    trigger_action( 'blockmanager_prepare_display', array(&$this) );
    $this->sort_blocks();
  }

  /** returns true if the block whose id is hidden
   * @param string block_id
  */
  public function is_hidden($block_id)
  {
    return isset($this->display_blocks[$block_id]) ? false : true;
  }

  public function hide_block($block_id)
  {
    unset( $this->display_blocks[$block_id] );
  }

  public function &get_block($block_id)
  {
    $tmp = null;
    if ( isset($this->display_blocks[$block_id]) )
    {
      return $this->display_blocks[$block_id];
    }
    return $tmp;
  }

  public function set_block_position($block_id, $position)
  {
    if ( isset($this->display_blocks[$block_id]) )
    {
      $this->display_blocks[$block_id]->set_position($position);
    }
  }

  protected function sort_blocks()
  {
    uasort( $this->display_blocks, array('BlockManager', 'cmp_by_position') );
  }

  static protected function cmp_by_position($a, $b)
  {
    return $a->get_position() - $b->get_position();
  }

  public function apply($var, $file)
  {
    global $template;

    $template->set_filename('menubar', $file);
    trigger_action('blockmanager_apply', array(&$this) );

    foreach( $this->display_blocks as $id=>$block)
    {
      if (empty($block->raw_content) and empty($block->template) )
      {
        $this->hide_block($id);
      }
    }
    $this->sort_blocks();
    $template->assign('blocks', $this->display_blocks);
    $template->assign_var_from_handle($var, 'menubar');
  }
}

/**
 * Represents a menu block registered in a Menu object.
 */
class RegisteredBlock
{
  protected $id;
  protected $name;
  protected $owner;

  public function RegisteredBlock($id, $name, $owner)
  {
    $this->id = $id;
    $this->name = $name;
    $this->owner = $owner;
  }

  public function get_id() { return $this->id; }
  public function get_name() { return $this->name; }
  public function get_owner() { return $this->owner; }
}

/**
 * Represents a menu block ready for display in the Menu object.
 */
class DisplayBlock
{
  protected $_registeredBlock;
  protected $_position;

  protected $_title;

  public $data;
  public $template;
  public $raw_content;

  public function DisplayBlock($registeredBlock)
  {
    $this->_registeredBlock = &$registeredBlock;
  }

  public function &get_block() { return $this->_registeredBlock; }

  public function get_position() { return $this->_position; }
  public function set_position($position)
  {
    $this->_position = $position;
  }

  public function get_title()
  {
    if (isset($this->_title))
      return $this->_title;
    else
      return $this->_registeredBlock->get_name();
  }

  public function set_title($title)
  {
    $this->_title = $title;
  }
}

?>