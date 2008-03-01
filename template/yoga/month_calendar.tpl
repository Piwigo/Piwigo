{* $Id$ *}

{if !empty($chronology_navigation_bars) }
{foreach from=$chronology_navigation_bars item=bar}
<div class="calendarBar">
  {if isset($bar.previous)}
    <div style="float:left">&laquo; <a href="{$bar.previous.URL}">{$bar.previous.LABEL}</a></div>
  {/if}
  {if isset($bar.next)}
    <div style="float:right"><a href="{$bar.next.URL}">{$bar.next.LABEL}</a> &raquo;</div>
  {/if}
  {if isset($bar.CONTENT)}
  {$bar.CONTENT}
  {else}
  &nbsp;
  {/if}
</div>
{/foreach}
{/if}

{if !empty($chronology_calendar.calendar_bars) }
{foreach from=$chronology_calendar.calendar_bars item=bar}
<div class="calendarCalBar">
  <span class="calCalHead"><a href="{$bar.U_HEAD}">{$bar.HEAD_LABEL}</a>  ({$bar.NB_IMAGES})</span><br/>
  {$bar.NAV_BAR}
</div>
{/foreach}
{/if}

{if isset($chronology_calendar.month_view) }
<table class="calMonth">
 <thead>
 <tr>
 {foreach from=$chronology_calendar.month_view.wday_labels item=wday}
	<td class="calDayHead">{$wday}</td>
 {/foreach}
 </tr>
 </thead>

 {foreach from=$chronology_calendar.month_view.weeks item=week}
 <tr>
 	{foreach from=$week item=day}
 	{if !empty($day)}
 		{if isset($day.IMAGE)}
 			<td class="calDayCellFull">
	 			<div class="calBackDate">{$day.DAY}</div><div class="calForeDate">{$day.DAY}</div>
	 			<div class="calImg" style="width:{$chronology_calendar.month_view.CELL_WIDTH}px;height:{$chronology_calendar.month_view.CELL_HEIGHT}px;">
					<a href="{$day.U_IMG_LINK}">
			  			<img style="{$day.IMAGE_STYLE}" src="{$day.IMAGE}" alt="{$day.IMAGE_ALT}" title="{$day.IMAGE_ALT}" />
					</a>
				</div>
 		{else}
 			<td class="calDayCellEmpty" style="width:{$chronology_calendar.month_view.CELL_WIDTH}px;height:{$chronology_calendar.month_view.CELL_HEIGHT}px;">{$day.DAY}
 		{/if}
 	{else}
 		<td class="calDayCellBlank" style="width:{$chronology_calendar.month_view.CELL_WIDTH}px;height:{$chronology_calendar.month_view.CELL_HEIGHT}px;">
 	{/if}
 	</td>
 	{/foreach} {*day in week*}
 </tr>
 {/foreach}  {*week in month*}
</table>
{/if}

