<div id="cat_group_info">
<h2>{'Informations'|@translate}</h2>
{if $IMG}
<p><a href="{$IMG.link}" class="thumblnk"><img src="{$IMG.src}"></a></p>
{/if}
<p>{'Hello,'|@translate}</p>
<p>{'Discover album:'|@translate} <a href="{$LINK}">{$CAT_NAME}</a></p>
<p>{$CPL_CONTENT}</p>
<p>{'See you soon.'|@translate}</p>
</div>
