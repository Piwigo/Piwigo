{* $Id$ *}
<div id="content" class="content">

  <div class="titrePage">
    <ul class="categoryActions">
{if $display_mode == 'letters'}
      <li><a href="{$U_CLOUD}" title="{'show tag cloud'|@translate}"><img src="{$themeconf.icon_dir}/tag_cloud.png" class="button" alt="{'cloud'|@translate}"/></a></li>
{/if}

{if $display_mode == 'cloud'}
      <li><a href="{$U_LETTERS}" title="{'group by letters'|@translate}"><img src="{$themeconf.icon_dir}/tag_letters.png" class="button" alt="{'letters'|@translate}"/></a></li>
{/if}

      <li><a href="{$U_HOME}" title="{'return to homepage'|@translate}"><img src="{$themeconf.icon_dir}/home.png" class="button" alt="{'home'|@translate}"/></a></li>
    </ul>
    <h2>{'Tags'|@translate}</h2>
  </div>

{if isset($tags)}
  {if $display_mode == 'cloud'}
  <ul id="fullTagCloud">
    {foreach from=$tags item=tag}
    <li><a href="{$tag.URL}" class="{$tag.CLASS}" title="{$tag.TITLE}">{$tag.NAME}</a></li>
    {/foreach}
  </ul>
  {/if}

  {if $display_mode == 'letters'}
  <table>
    <tr>
      <td valign="top">
    {foreach from=$letters item=letter}
  <fieldset class="tagLetter">
    <legend class="tagLetterLegend">{$letter.TITLE}</legend>
    <table class="tagLetterContent">
      {foreach from=$letter.tags item=tag}
      <tr class="tagLine">
        <td><a href="{$tag.URL}">{$tag.NAME}</a></td>
        <td class="nbEntries">{$pwg->l10n_dec('%d element', '%d elements', $tag.COUNTER)}</td>
      </tr>
      {/foreach}
    </table>
  </fieldset>
      {if $letter.CHANGE_COLUMN|@default:false}
      </td>
      <td valign="top">
      {/if}
    {/foreach}
      </td>
    </tr>
  </table>
  {/if}
{/if}

</div> <!-- content -->
