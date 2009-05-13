{* $Id: /piwigo/trunk/admin/template/goto/advanced_feature.tpl 6371 2008-09-14T12:25:34.485116Z vdigital  $ *}
<div class="titrePage">
  <h2>{'Advanced_features'|@translate}</h2>
</div>

<ul>
  {foreach from=$advanced_features item=feature}
    <li><a href="{$feature.URL}" {$TAG_INPUT_ENABLED}>{$feature.CAPTION}</a></li>
  {/foreach}
</ul>
