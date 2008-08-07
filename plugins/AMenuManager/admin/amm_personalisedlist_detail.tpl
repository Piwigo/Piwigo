
{if isset($datas.sections) and count($datas.sections)}
  <table class="table2 littlefont">
    <tr class="throw">
      <th>{'g002_setting_personalised_nfo'|@translate}</th>
      <th>{'g002_title'|@translate}</th>
      <th>{'g002_visible'|@translate}</th>
      <th colspan=2>&nbsp;</th>
    </tr>

    {foreach from=$datas.sections key=name item=section}
      <tr>
        <td>{$section.nfo}</td>
        <td>{$section.title}</td>
        <td style="text-align:center;">{$section.visible}</td>
        <td width="15px"><a href="{$section.edit}"><img src='{$themeconf.icon_dir}/category_edit.png'/></a></td>
        <td width="15px"><a style="cursor:pointer;" onclick="load_list('delete', {$section.ID})"><img src='{$themeconf.icon_dir}/delete.png'/></a></td>
      </tr>
    {/foreach}

  </table>
{/if}
