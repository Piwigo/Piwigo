
{if isset($datas.LIST) and count($datas.LIST)}
  <table class="table2 littlefont">
    <tr class="throw">
      <th>{'g002_labelmenu'|@translate}</th>
      <th>{'g002_visible'|@translate}</th>
    </tr>

    {foreach from=$datas.LIST key=name item=data}
      <tr>
        <td>{$data.LABEL|@translate}</td>
        <td style="text-align:center;"><a style="cursor:pointer;" onclick="load_list('showhide', '{$data.ID}', '')">{$data.VISIBLE|@translate}</a></td>
      </tr>
    {/foreach}

  </table>
{/if}
