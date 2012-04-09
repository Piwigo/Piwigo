
{if !empty($chronology_navigation_bars) }
{foreach from=$chronology_navigation_bars item=bar}
<div class="calendarBar">
	{if isset($bar.previous)}
		<div style="float:left">&laquo; <a href="{$bar.previous.URL}">{$bar.previous.LABEL}</a></div>
	{/if}
	{if isset($bar.next)}
		<div style="float:right"><a href="{$bar.next.URL}">{$bar.next.LABEL}</a> &raquo;</div>
	{/if}
	{if empty($bar.items)}
		&nbsp;
	{else}
		{foreach from=$bar.items item=item}
		<span class="calItem{if !isset($item.URL)}Empty{/if}" {if isset($item.NB_IMAGES)}title="{$pwg->l10n_dec('%d photo', '%d photos', $item.NB_IMAGES)}"{/if}>
		{if isset($item.URL)}
		<a href="{$item.URL}">{$item.LABEL}</a>
		{else}
		{$item.LABEL}
		{/if}
		</span>
		{/foreach}
	{/if}
</div>
{/foreach}
{/if}

{if !empty($chronology_calendar.calendar_bars) }
{foreach from=$chronology_calendar.calendar_bars item=bar}
<div class="calendarCalBar">
	<span class="calCalHead"><a href="{$bar.U_HEAD}">{$bar.HEAD_LABEL}</a>  ({$bar.NB_IMAGES})</span><br>
	{foreach from=$bar.items item=item}
	<span class="calCal{if !isset($item.URL)}Empty{/if}">
	{if isset($item.URL)}
	<a href="{$item.URL}">{$item.LABEL}</a>
	{else}
	{$item.LABEL}
	{/if}
	{if isset($item.NB_IMAGES)}({$item.NB_IMAGES}){/if}
	</span>
	{/foreach}
</div>
{/foreach}
{/if}

{if isset($chronology_calendar.month_view) }
<table class="calMonth">
 <thead>
 <tr>
 {foreach from=$chronology_calendar.month_view.wday_labels item=wday}
	<th>{$wday}</th>
 {/foreach}
 </tr>
 </thead>
{html_head} {*add the style to html head for strict standard compliance*}
<style type="text/css">
TABLE.calMonth TBODY TD, TABLE.calMonth TBODY TD DIV.calImg {ldelim}
  width:{$chronology_calendar.month_view.CELL_WIDTH}px;height:{$chronology_calendar.month_view.CELL_HEIGHT}px;
}
</style>
{/html_head}
 {foreach from=$chronology_calendar.month_view.weeks item=week}
 <tr>
 	{foreach from=$week item=day}
 	{if !empty($day)}
 		{if isset($day.IMAGE)}
 			<td class="calDayCellFull">
	 			<div class="calBackDate">{$day.DAY}</div><div class="calForeDate">{$day.DAY}</div>
	 			<div class="calImg">
					<a href="{$day.U_IMG_LINK}">
			  			<img style="{$day.IMAGE_STYLE}" src="{$day.IMAGE}" alt="{$day.IMAGE_ALT}" title="{$pwg->l10n_dec('%d photo','%d photos', $day.NB_ELEMENTS)}">
					</a>
				</div>
 		{else}
 			<td class="calDayCellEmpty">{$day.DAY}
 		{/if}
 	{else}{*blank cell first or last row only*}
 		<td>
 	{/if}
 	</td>
 	{/foreach} {*day in week*}
 </tr>
 {/foreach}  {*week in month*}
</table>
{/if}

