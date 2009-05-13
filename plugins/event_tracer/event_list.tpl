There are {$NB_EVENTS} calls to triger_event or triger_action.

<table width="99%" class="table2">
<tr class="throw">
  <th><a href="{$U_SORT0}">Type</a></th>
  <th><a href="{$U_SORT1}">Name</a></th>
  <th><a href="{$U_SORT2}">File</a></th>
</tr>
{foreach from=$events item=event}
<tr>
  <td>{$event.TYPE}</td>
  <td>{$event.NAME}</td>
  <td>{$event.FILE}</td>
</tr>
{/foreach}
</table>
