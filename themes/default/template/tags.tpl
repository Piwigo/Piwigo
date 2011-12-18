{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content">

<div class="titrePage">
	<ul class="categoryActions">
{if $display_mode == 'letters'}
		<li><a href="{$U_CLOUD}" title="{'show tag cloud'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-cloud">&nbsp;</span><span class="pwg-button-text">{'cloud'|@translate}</span>
		</a></li>
{/if}
{if $display_mode == 'cloud'}
		<li><a href="{$U_LETTERS}" title="{'group by letters'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-letters">&nbsp;</span><span class="pwg-button-text">{'letters'|@translate}</span>
		</a></li>
{/if}
		<li><a href="{$U_HOME}" title="{'Home'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-home">&nbsp;</span><span class="pwg-button-text">{'Home'|@translate}</span>
		</a></li>
	</ul>
	<h2>{'Tags'|@translate}</h2>
</div>

{include file='infos_errors.tpl'}

{if isset($tags)}
	{if $display_mode == 'cloud'}
	<div id="fullTagCloud">
		{foreach from=$tags item=tag}
		<span><a href="{$tag.URL}" class="tagLevel{$tag.level}" title="{$pwg->l10n_dec('%d photo', '%d photos', $tag.counter)}">{$tag.name}</a></span>
		{/foreach}
	</div>
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
        <td><a href="{$tag.URL}" title="{$tag.name}">{$tag.name}</a></td>
        <td class="nbEntries">{$pwg->l10n_dec('%d photo', '%d photos', $tag.counter)}</td>
      </tr>
      {/foreach}
    </table>
  </fieldset>
      {if isset($letter.CHANGE_COLUMN) }
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
