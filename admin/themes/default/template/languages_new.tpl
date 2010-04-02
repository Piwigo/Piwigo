{known_script id="jquery" src=$ROOT_URL|@cat:"themes/default/js/jquery.packed.js"}
{known_script id="jquery.cluetip" src=$ROOT_URL|@cat:"themes/default/js/plugins/jquery.cluetip.packed.js"}

<script type="text/javascript">
jQuery().ready(function(){ldelim}
  jQuery('.cluetip').cluetip({ldelim}
    width: 300,
    splitTitle: '|'
  });
});
</script>

<div class="titrePage">
  <h2>{'Add New Language'|@translate}</h2>
</div>

{if !empty($languages)}
<table class="table2 languages">
<thead>
  <tr class="throw">
    <td>{'Language'|@translate}</td>
    <td>{'Version'|@translate}</td>
    <td>{'Date'|@translate}</td>
    <td>{'Author'|@translate}</td>
    <td>{'Actions'|@translate}</td>
  </tr>
</thead>
{foreach from=$languages item=language name=languages_loop}
  <tr class="{if $smarty.foreach.languages_loop.index is odd}row1{else}row2{/if}">
    <td><a href="{$language.EXT_URL}" class="externalLink cluetip" title="{$language.EXT_NAME}|{$language.EXT_DESC|htmlspecialchars|nl2br}">{$language.EXT_NAME}</a></td>
    <td style="text-align:center;"><a href="{$language.EXT_URL}" class="externalLink cluetip" title="{$language.EXT_NAME}|{$language.VER_DESC|htmlspecialchars|nl2br}">{$language.VERSION}</a></td>
    <td>{$language.DATE}</td>
    <td>{$language.AUTHOR}</td>
    <td style="text-align:center;"><a href="{$language.URL_INSTALL}">{'Install'|@translate}</a>
      / <a href="{$language.URL_DOWNLOAD}">{'download'|@translate|@ucfirst}</a>
    </td>
  </tr>
{/foreach}
</table>
{else}
<p>{'There is no other language available.'|@translate}</p>
{/if}
