<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-02-28 02:13:16 +0100 (mar., 28 févr. 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1058 $
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

//------------------------------------------------------------------- functions
// official_req returns the managed requests list in array format
function official_req()
{
return array(
    'random'                              /* Random order */
  , 'list'               /* list on MBt & z0rglub request */
  , 'maxviewed'             /* hit > 0 and hit desc order */
  , 'recent'        /* recent = Date_available desc order */
  , 'highrated'            /* avg_rate > 0 and desc order */
  , 'oldest'                  /* Date_available asc order */
  , 'lessviewed'                         /* hit asc order */
  , 'lowrated'                      /* avg_rate asc order */
  , 'undescribed'                  /* description missing */
  , 'unnamed'                         /* new name missing */
  , 'portraits'     /* width < height (portrait oriented) */
  , 'landscapes'   /* width > height (landscape oriented) */
  , 'squares'             /* width ~ height (square form) */
);
}


// expand_id_list($ids) convert a human list expression to a full ordered list
// example : expand_id_list( array(5,2-3,2) ) returns array( 2, 3, 5)
function expand_id_list($ids)
{
    $tid = array();
    foreach ( $ids as $id )
    {
      if ( is_numeric($id) )
      {
        $tid[] = (int) $id;
      }
      else
      {
        $range = explode( '-', $id );
        if ( is_numeric($range[0]) and is_numeric($range[1]) )
        {
          $from = min($range[0],$range[1]);
          $to = max($range[0],$range[1]);
          for ($i = $from; $i <= $to; $i++) 
          {
            $tid[] = (int) $i;
          }
        }
      }
    }
    $result = array_unique ($tid); // remove duplicates...
    sort ($result);
    return $result;
}

// check_target($string) verifies and corrects syntax of target parameter
// example : check_target(cat/23,24,24,24,25,27) returns cat/23-25,27
function check_target($list)
{
  if ( $list !== '' )
  {
    $type = explode('/',$list); // Find type list
    if ( !in_array($type[0],array('list','cat','tag') ) )
    {
      $type[0] = 'list'; // Assume an id list
    } 
    $ids = explode( ',',$type[1] );
    $list = $type[0] . '/';

    // 1,2,21,3,22,4,5,9-12,6,11,12,13,2,4,6,

    $result = expand_id_list( $ids ); 

    // 1,2,3,4,5,6,9,10,11,12,13,21,22, 
    // I would like
    // 1-6,9-13,21-22
    $serial[] = $result[0]; // To be shifted                      
    foreach ($result as $k => $id)
    {
      $next_less_1 = (isset($result[$k + 1]))? $result[$k + 1] - 1:-1;
      if ( $id == $next_less_1 and end($serial)=='-' )
      { // nothing to do 
      }
      elseif ( $id == $next_less_1 )
      {
        $serial[]=$id;
        $serial[]='-';
      }
      else
      {
        $serial[]=$id;  // end serie or non serie
      }
    }
    $null = array_shift($serial); // remove first value
    $list .= array_shift($serial); // add the real first one
    $separ = ',';
    foreach ($serial as $id)
    {
      $list .= ($id=='-') ? '' : $separ . $id;
      $separ = ($id=='-') ? '-':','; // add comma except if hyphen
    }
  }
  return $list;
}
?>
