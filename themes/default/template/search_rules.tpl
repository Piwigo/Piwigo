<div id="content" class="content">
	<div class="titrePage">
	<ul class="categoryActions">
		<li><a href="#" onclick="window.close();" title="{'Close this window'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-close">&nbsp;</span><span class="pwg-button-text">exit</span>
		</a></li>
	</ul>
	<h2>{'Search rules'|@translate}</h2>
	</div>

{if isset($INTRODUCTION)}
<p>{$INTRODUCTION}</p>
{/if}

<ul>

  {if isset($search_words)}
  {foreach from=$search_words item=v}
  <li>{$v}</li>
  {/foreach}
  {/if}

  {if isset($SEARCH_TAGS_MODE) }
  <li>
    <p>{if 'AND'==$SEARCH_TAGS_MODE}{'All tags'|@translate}{else}{'Any tag'|@translate}{/if}</p>
    <ul>
      {foreach from=$search_tags item=v}
      <li>{$v}</li>
      {/foreach}
    </ul>
  </li>
  {/if}
  
  {if isset($DATE_CREATION)}
  <li>{$DATE_CREATION}</li>
  {/if}

  {if isset($DATE_AVAILABLE)}
  <li>{$DATE_AVAILABLE}</li>
  {/if}

  {if isset($search_categories)}
  <li>
    <p>{'Albums'|@translate}</p>

    <ul>
      {foreach from=$search_categories item=v}
      <li>{$v}</li>
      {/foreach}
    </ul>
  </li>
  {/if}
  
</ul>

<p>
    <a href="#" onclick="window.close();">{'Close this window'|@translate}</a>
</p>

</div> <!-- content -->

