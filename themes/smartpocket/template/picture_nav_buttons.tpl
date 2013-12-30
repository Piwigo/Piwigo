<div data-role="controlgroup" data-type="horizontal" align="center">
{strip}
{if isset($previous)}
	 <a href="{$previous.U_IMG}" rel="prev" data-role="button" data-icon="arrow-l"  data-iconpos="notext" data-inline="true">{'Previous'|@translate}</a>
{/if}
{if isset($U_UP) and !isset($slideshow)}
	<a href="{$U_UP}" rel="prev" data-role="button" data-icon="arrow-u" data-iconpos="notext" data-inline="true">data-iconpos="notext"</a>
{/if}
{if isset($next)}
	<a href="{$next.U_IMG}" rel="next" data-role="button" data-icon="arrow-r"  data-iconpos="notext" data-iconpos="right" data-inline="true">{'Next'|@translate}</a>
{/if}
{/strip}
</div>
