<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

/**
 * This file is included by the picture page to manage rates
 *
 */

if ($conf['rate'])
{
  $query = '
SELECT COUNT(rate) AS count
     , ROUND(AVG(rate),2) AS average
     , ROUND(STD(rate),2) AS STD
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$picture['current']['id'].'
;';
  $row = mysql_fetch_array(pwg_query($query));
  if ($row['count'] == 0)
  {
    $value = $lang['no_rate'];
  }
  else
  {
    $value = sprintf(
      l10n('%.2f (rated %d times, standard deviation = %.2f)'),
      $row['average'],
      $row['count'],
      $row['STD']
      );
  }

  if ($conf['rate_anonymous'] or is_autorize_status(ACCESS_CLASSIC) )
  {
    if ($row['count']>0)
    {
      $query = 'SELECT rate
      FROM '.RATE_TABLE.'
      WHERE element_id = '.$page['image_id'] . '
      AND user_id = '.$user['id'] ;

      if ( !is_autorize_status(ACCESS_CLASSIC) )
      {
        $ip_components = explode('.', $_SERVER['REMOTE_ADDR']);
        if ( count($ip_components)>3 )
        {
          array_pop($ip_components);
        }
        $anonymous_id = implode ('.', $ip_components);
        $query .= ' AND anonymous_id = \''.$anonymous_id . '\'';
      }

      $result = pwg_query($query);
      if (mysql_num_rows($result) > 0)
      {
        $row = mysql_fetch_array($result);
        $sentence = $lang['already_rated'];
        $sentence.= ' ('.$row['rate'].'). ';
        $sentence.= $lang['update_rate'];
      }
      else
      {
        $sentence = $lang['never_rated'].'. '.$lang['Rate'];
      }
    }
    else
    {
      $sentence = $lang['never_rated'].'. '.$lang['Rate'];
    }
    $template->assign_block_vars(
      'rate',
      array(
        'CONTENT' => $value,
        'SENTENCE' => $sentence
        )
      );

    $template->assign_block_vars('info_rate', array('CONTENT' => $value));

    $template->assign_vars(
      array(
        'INFO_RATE' => $value
        )
      );

    foreach ($conf['rate_items'] as $num => $mark)
    {
      $template->assign_block_vars(
        'rate.rate_option',
        array(
          'OPTION'    => $mark,
          'URL'       => add_url_params(
                          $url_self,
                          array(
                            'action'=>'rate',
                            'rate'=>$mark
                          )
                        ),
          'SEPARATOR' => ($num > 0 ? '|' : ''),
          )
        );
    }
  }
}

?>