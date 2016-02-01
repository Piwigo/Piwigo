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

class Inflector_fr
{
  private $exceptions;
  private $pluralizers;
  private $singularizers;

  function __construct()
  {
    $tmp = 	array ('monsieur' => 'messieurs',
      'madame' => 'mesdames',
      'mademoiselle' => 'mesdemoiselles',
    );

    $this->exceptions = $tmp;
    foreach ($tmp as $k => $v)
      $this->exceptions[$v] = $k;

    $this->pluralizers = array_reverse(array( '/$/' => 's',
      '/(bijou|caillou|chou|genou|hibou|joujou|pou|au|eu|eau)$/' => '\1x',
      '/(bleu|émeu|landau|lieu|pneu|sarrau)$/' => '\1s',
      '/al$/' => 'aux',
      '/ail$/' => 'ails',
      '/(b|cor|ém|gemm|soupir|trav|vant|vitr)ail$/' => '\1aux',
      '/(s|x|z)$/' => '\1',
    ));

    $this->singularizers = array_reverse(array(
      '/s$/' => '',
      '/(bijou|caillou|chou|genou|hibou|joujou|pou|au|eu|eau)x$/' => '\1',
      '/(journ|chev)aux$/' => '\1al',
      '/ails$/' => 'ail',
      '/(b|cor|ém|gemm|soupir|trav|vant|vitr)aux$/' => '\1ail',
    ));
  }

  function get_variants($word)
  {
    $res = array();

    $word = strtolower($word);

    $rc = @$this->exceptions[$word];
    if ( isset($rc) )
    {
      if (!empty($rc))
        $res[] = $rc;
      return $res;
    }

    foreach ($this->pluralizers as $rule => $replacement)
    {
      $rc = preg_replace($rule, $replacement, $word, -1, $count);
      if ($count)
      {
        $res[] = $rc;
        break;
      }
    }

    foreach ($this->singularizers as $rule => $replacement)
    {
      $rc = preg_replace($rule, $replacement, $word, -1, $count);
      if ($count)
      {
        $res[] = $rc;
        break;
      }
    }

    return $res;
  }
}
?>