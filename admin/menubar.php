<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

if (!is_webmaster())
{
  $page['warnings'][] = str_replace('%s', l10n('user_status_webmaster'), l10n('%s status is required to edit parameters.'));
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

include_once(PHPWG_ROOT_PATH.'include/block.class.php');

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$my_base_url = get_root_url().'admin.php?page=';

$tabsheet = new tabsheet();
$tabsheet->set_id('menus');
$tabsheet->select('');
$tabsheet->assign();


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

$idx=1;
foreach ($reg_blocks as $id => $block)
{
  if ( !isset($mb_conf[$id]) )
    $mb_conf[$id] = $idx*50;
  $idx++;
}


if ( isset($_POST['submit']) and is_webmaster())
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

  $page['infos'][] = l10n('Order of menubar items has been updated successfully.');
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

$template->assign('isWebmaster', (is_webmaster()) ? 1 : 0);
$template->assign('ADMIN_PAGE_TITLE', l10n('Menu Management'));

$template->set_filename( 'menubar_admin_content', 'menubar.tpl' );
$template->assign_var_from_handle( 'ADMIN_CONTENT', 'menubar_admin_content');
?>
