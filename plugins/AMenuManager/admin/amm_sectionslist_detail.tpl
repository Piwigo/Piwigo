
{if isset($datas.sections) and count($datas.sections)}
  <table class="table2 littlefont">
    <tr class="throw">
      <th>{'g002_owner'|@translate}</th>
      <th>{'g002_sectionid'|@translate}</th>
      <th>{'g002_name'|@translate}</th>
      <th>{'g002_visible'|@translate}</th>
      <th colspan=2>&nbsp;</th>
    </tr>

    {foreach from=$datas.sections key=name item=section}
      <tr>
        <td>{$section.OWNER}</td>
        <td>{$section.ID}</td>
        <td>{$section.NAME}</td>
        <td style="text-align:center;"><a style="cursor:pointer;" onclick="load_list('showhide', '{$section.ID}', '')">{$section.VISIBLE}</a></td>
        <td width="15px">{if $section.up}<a style="cursor:pointer;" onclick="load_list('position', '{$section.ID}', '{$section.PREVPOS}')"><img src='{$plugin.PATH}/admin/go-up.png'/></a>{else}&nbsp;{/if}</td>
        <td width="15px">{if $section.down}<a style="cursor:pointer;" onclick="load_list('position', '{$section.ID}', '{$section.NEXTPOS}')"><img src='{$plugin.PATH}/admin/go-down.png'/></a>{else}&nbsp;{/if}</td>
      </tr>
    {/foreach}

  </table>
{/if}
