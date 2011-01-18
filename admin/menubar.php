<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}


function abs_fn_cmp($a, $b)
{
  return abs($a)-abs($b);
}

function make_consecutive( &$orders, $step=50 )
{
  uasort( $orders, 'abs_fn_cmp' );
  $crt = 1;
  foreach( $orders as $id=>$pos)
  {
    $orders[$id] = $step * ($pos<0 ? -$crt : $crt);
    $crt++;
  }
}


global $template;

include_once(PHPWG_ROOT_PATH.'include/block.class.php');

$menu = new BlockManager('menubar');
$menu->load_registered_blocks();
$reg_blocks = $menu->get_registered_blocks();

$mb_conf = @$conf[ 'blk_'.$menu->get_id() ];
if ( is_string($mb_conf) )
  $mb_conf = unserialize( $mb_conf );
if ( !is_array($mb_conf) )
  $mb_conf=array();

foreach ($mb_conf as $id => $pos)
{
  if (!isset($reg_blocks[$id]))
    unset($mb_conf[$id]);
}

if ( isset($_POST['reset']))
{
  $mb_conf = array();
  $query = '
UPDATE '.CONFIG_TABLE.'
  SET value=\'\'
  WHERE param=\'blk_'.addslashes($menu->get_id()).'\'
  LIMIT 1';
  pwg_query($query);
}


$idx=1;
foreach ($reg_blocks as $id => $block)
{
  if ( !isset($mb_conf[$id]) )
    $mb_conf[$id] = $idx*50;
  $idx++;
}


if ( isset($_POST['submit']) )
{
  foreach ( $mb_conf as $id => $pos )
  {
    $hide = isset($_POST['hide_'.$id]);
    $mb_conf[$id] = ($hide ? -1 : +1)*abs($pos);

    $pos = (int)@$_POST['pos_'.$id];
    if ($pos>0)
      $mb_conf[$id] = $mb_conf[$id] > 0 ? $pos : -$pos;
  }
  make_consecutive( $mb_conf );

  // BEGIN OPTIM - DONT ASK ABOUT THIS ALGO - but optimizes the size of the array we save in DB
  /* !!! OPTIM DISABLED UNTIL IT HAS BEEN FIXED !!!
  $reg_keys = array_keys($reg_blocks);
  $cnf_keys = array_keys($mb_conf);
  $best_slice = array( 'len'=>0 );
  for ($i=0; $i<count($reg_keys); $i++)
  {
    for ($j=0; $j<count($cnf_keys); $j++)
    {
      for ($k=0; max($i,$j)+$k<count($cnf_keys); $k++)
      {
        if ($cnf_keys[$j+$k] == $reg_keys[$i+$k] )
        {
          if ( 1+$k>$best_slice['len'])
          {
            $best_slice['len'] = 1+$k;
            $best_slice['start_cnf'] = $j;
          }
        }
        else
          break;
      }
    }
  }
  */
  $mb_conf_db = $mb_conf;
  /*
  if ($best_slice['len'])
  {
    for ($j=0; $j<$best_slice['start_cnf']; $j++ )
    {
      $sign = $mb_conf_db[ $cnf_keys[$j] ] > 0 ? 1 : -1;
      $mb_conf_db[ $cnf_keys[$j] ] = $sign * ( ($best_slice['start_cnf'])*50 - ($best_slice['start_cnf']-$j) );
    }
    for ($j=$best_slice['start_cnf']; $j<$best_slice['start_cnf']+$best_slice['len']; $j++ )
    {
      if ($mb_conf_db[ $cnf_keys[$j] ] > 0)
        unset( $mb_conf_db[ $cnf_keys[$j] ] );
    }
  }
  //var_export( $best_slice ); var_export($mb_conf);  var_export($mb_conf_db);
  // END OPTIM
  */
  $query = '
UPDATE '.CONFIG_TABLE.'
  SET value=\''.addslashes(serialize($mb_conf_db)).'\'
  WHERE param=\'blk_'.addslashes($menu->get_id()).'\'
  ';
  pwg_query($query);

  array_push($page['infos'], l10n('Order of menubar items has been updated successfully.'));
}

make_consecutive( $mb_conf );

foreach ($mb_conf as $id => $pos )
{
  $template->append( 'blocks',
      array(
        'pos' => $pos/5,
        'reg' => $reg_blocks[$id]
      )
     );
}

$action = get_root_url().'admin.php?page=menubar';
$template->assign(array('F_ACTION'=>$action));

$template->set_filename( 'menubar_admin_content', 'menubar.tpl' );
$template->assign_var_from_handle( 'ADMIN_CONTENT', 'menubar_admin_content');
?>
