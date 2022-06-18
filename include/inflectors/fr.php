<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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